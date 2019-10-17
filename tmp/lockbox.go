package main

import (
	"bytes"
	"crypto/aes"
	"crypto/cipher"
	"crypto/hmac"
	"crypto/sha256"
	"encoding/base64"
	"fmt"
	"image"
	"image/color"
	"image/png"
	"math/rand"
	"net/http"
	"os"
	"strconv"
	"text/template"
	"time"

	_ "github.com/go-sql-driver/mysql"
	"github.com/gorilla/mux"
	"github.com/jinzhu/gorm"
	"golang.org/x/image/font"
	"golang.org/x/image/font/basicfont"
	"golang.org/x/image/math/fixed"
)

type Text struct {
	ID   uint `gorm:"primary_key"`
	Data string
	Lock int64
}

type Env struct {
	key []byte
	db *gorm.DB
	domain string
}

func main() {
	var env Env
	var err error
	key := os.Getenv("key")
	env.key, err = base64.RawURLEncoding.DecodeString(key)
	panicIfError(err)

	env.db, err = gorm.Open("mysql", os.Getenv("DATABASE_URL"))
	panicIfError(err)

	panicIfError(env.db.AutoMigrate(&Text{}).Error)

	env.domain = os.Getenv("domain")

	router := mux.NewRouter()
	router.Use(func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			defer func() {
				if v := recover(); v != nil {
					http.Error(w, fmt.Sprintf("%s", v), http.StatusBadRequest)
				}
			}()
			next.ServeHTTP(w, r)
		})
	})

	router.HandleFunc("/", env.index).Methods("GET")
	router.HandleFunc("/", env.upload).Methods("POST")
	router.HandleFunc("/captcha", env.captcha).Methods("GET")

	srv := &http.Server{
		Addr: fmt.Sprintf(":%s", os.Getenv("PORT")),
		ReadTimeout:  5 * time.Second,
		WriteTimeout: 5 * time.Second,
		Handler:      router,
	}
	panicIfError(srv.ListenAndServe())
}

func (env *Env) index(w http.ResponseWriter, r *http.Request) {
	if r.URL.Query().Get("id") != "" {
		env.fetch(w, r)
		return
	}
	const html = `<html>
  <body>
    <h1>Immensely Secure Lockbox</h1>
    <form method="POST">
      <div>lock: <input id="lock" name="lock" type="date"></div>
      <div>text:</div>
      <div><textarea id="textarea" name="data" style="width: 300px; height: 100px"></textarea></div>
      <div><span id="chars">0</span> / 140</div>
      <div>captcha: <input name="response" type="text" autocomplete="off"> <img valign="middle" src="/captcha?w=70&c={{.C}}"></div>
      <input name="challenge" type="hidden" value="{{.C}}">
      <div><input type="submit" value="upload"></div>
    </form>
    <script>lock.value=new Date().toISOString().substr(0, 10);setInterval(()=>chars.innerText=textarea.value.length, 500);// -- Alok</script>
  </body>
</html>`
	t, err := template.New("html").Parse(html)
	panicIfError(err)

	buf := make([]byte, 5)
	_, err = rand.Read(buf)
	panicIfError(err)
	c := base64.RawURLEncoding.EncodeToString(buf)

	panicIfError(t.ExecuteTemplate(w, "html", struct{ C string }{C: env.encrypt(c)}))
}

func (env *Env) fetch(w http.ResponseWriter, r *http.Request) {
	var text Text
	panicIfError(env.db.First(&text, r.URL.Query().Get("id")).Error)

	lock := time.Unix(text.Lock, 0)
	now := time.Now()
	if lock.After(now) {
		http.Error(w, fmt.Sprintf("timelock: %s", lock.Sub(now).String()), http.StatusBadRequest)
		return
	}

	if env.computeHmac(text.Data) != r.URL.Query().Get("hash") {
		http.Error(w, "bad hash", http.StatusBadRequest)
		return
	}

	text.Data = env.decrypt(text.Data)

	const html = `<html>
  <body>
    <p>{{.Data}}</p>
  </body>
</html>`
	t, err := template.New("html").Parse(html)
	panicIfError(err)

	panicIfError(t.ExecuteTemplate(w, "html", text))
}

func (env *Env) upload(w http.ResponseWriter, r *http.Request) {
	defer func() {
		_ = r.Body.Close()
	}()

	panicIfError(r.ParseForm())

	if env.decrypt(r.Form.Get("challenge")) != r.Form.Get("response") {
		http.Error(w, "captcha failure", http.StatusBadRequest)
		return
	}

	lock, err := time.Parse("2006-01-02", r.Form.Get("lock"))
	panicIfError(err)

	data := r.Form.Get("data")
	if len(data) > 140 {
		data = data[0:140]
	}

	text := Text{
		Data: env.encrypt(data),
		Lock: lock.Unix(),
	}
	panicIfError(env.db.Create(&text).Error)

	url := fmt.Sprintf("%s/?id=%d&hash=%s", env.domain, text.ID, env.computeHmac(text.Data))

	const html = `<html>
  <body>
    <p><a href="{{.Url}}">{{.Url}}</a></p>
  </body>
</html>`
	t, err := template.New("html").Parse(html)
	panicIfError(err)

	panicIfError(t.ExecuteTemplate(w, "html", struct{ Url string }{Url: url}))
}

func (env *Env) captcha(w http.ResponseWriter, r *http.Request) {
	c := env.decrypt(r.URL.Query().Get("c"))
	width, _ := strconv.Atoi(r.URL.Query().Get("w"))
	img := image.NewRGBA(image.Rect(0, 0, width, 50))
	d := &font.Drawer{
		Dst:  img,
		Src:  image.NewUniform(color.RGBA{R: 50, G: 50, B: 200, A: 255}),
		Face: basicfont.Face7x13,
		Dot:  fixed.Point26_6{X: 0, Y: 25 * 64},
	}
	for x := 0; x < len(c); x++ {
		d.DrawString(c[x : x+1])
		d.Dot.X += 64 * 3
		d.Dot.Y = fixed.Int26_6((25 + rand.Intn(20) - 10) * 64)
	}
	panicIfError(png.Encode(w, img))
}

func (env *Env) encrypt(data string) string {
	block, err := aes.NewCipher(env.key)
	if err != nil {
		return err.Error()
	}
	buf := []byte(data)
	for len(buf)%block.BlockSize() != 0 {
		buf = append(buf, 0)
	}

	cbc := cipher.NewCBCEncrypter(block, make([]byte, block.BlockSize()))
	cbc.CryptBlocks(buf, buf)

	return base64.RawURLEncoding.EncodeToString(buf)
}

func (env *Env) decrypt(data string) string {
	buf, err := base64.RawURLEncoding.DecodeString(data)
	panicIfError(err)

	block, err := aes.NewCipher(env.key)
	panicIfError(err)

	cbc := cipher.NewCBCDecrypter(block, make([]byte, block.BlockSize()))
	cbc.CryptBlocks(buf, buf)
	return string(bytes.Trim(buf, "\x00"))
}

func (env *Env) computeHmac(data string) string {
	mac := hmac.New(sha256.New, env.key)
	mac.Write([]byte(data))
	return base64.RawURLEncoding.EncodeToString(mac.Sum(nil)[:8])
}

func panicIfError(err error) {
	if err != nil {
		panic(err.Error())
	}
}
