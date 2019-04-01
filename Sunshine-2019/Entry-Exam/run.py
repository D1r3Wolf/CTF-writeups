from PIL import Image
from requests import session
from bs4 import BeautifulSoup
from time import sleep

Coords = {
	1 : (330,430),
	2 : (330,520),
	3 : (330,610),
	4 : (330,700),
	5 : (330,791),
	6 : (330,880),
	7 : (330,974),
	8 : (330,1064),
	9 : (330,1154),
	10: (330,1244),
	11: (820,430),
	12: (820,520),
	13: (820,610),
	14: (820,700),
	15: (820,791),
	16: (820,881),
	17: (820,974),
	18: (820,1064),
	19: (820,1154),
	20: (820,1244)
}

# A = Image.open("A.png") ; B = Image.open("B.png") ; C = Image.open("C.png") ; D = Image.open("D.png")
def End(S):
	flag = S.get(url).text
	print("[+] Flag is :: %s"%flag)

def Edit(I,O,C,Img):
	E = Image.open(O+'.png')
	Pix = E.load()
	Ans_P = Img.load() ; w = C[0] ; h = C[1]
	for i in range(340):
		for j in range(60):
			Ans_P[w+i,h+j] = Pix[i,j] 
def Answer_Sheet(D):
	Ans = Image.open("scantron.png")
	for i in Coords:
		Edit(i,D[i],Coords[i],Ans)
	Ans.save("Ans.png")

def Get_Answers(html_doc):
	soup = BeautifulSoup(html_doc, 'html.parser')
	Elem = [x.get_text() for x in soup.find_all("li")]
	if len(Elem) != 100 : return 1
	D = {}
	for i in range(20):
		Q = Elem[i*5] ; A = [int(x) for x in Elem[i*5+1:i*5+5]]
		D[i+1] = chr(65+A.index(int(eval(Q))))
	return D

def Post(S):
	File = { "file" : open("Ans.png",'rb').read()}
	Data = { "submit" : "value" }
	A = S.post(url,data=Data,files=File)
	if '<h1>Exam Section' not in A.text:
		print("[-] Error :: %s"%A.text)
	else:
		print("[+] Wow Move On :: %s"%(A.text.split('\n')[0]))

def Exam(S,i):
	A = S.get(url).text
	Ans = Get_Answers(A)
	if Ans == 1:
		End(S) ; return 0
	print("[{0}] Grabbed Answers :: {1}".format(i,str(Ans.values())))
	Answer_Sheet(Ans)
	Post(S)
	Exam(S,i+1)

def main():
	Ss = session()
	Ss.get(url)
	Exam(Ss,0)

url = "http://ee.sunshinectf.org/exam"
main()