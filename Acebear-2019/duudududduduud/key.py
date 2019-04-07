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
