from pwn import *
from Crypto.Cipher import AES
from time import sleep
def Encrypt(P,key):
	A = AES.new(key)
	return A.encrypt(P).encode('hex')
def Attack(p,c):
	for i in range(256):
		for j in range(256):
			key = chr(i)+chr(j)
			key = key*8
			if Encrypt(p,key) == c:
				return key

def start():
	con = remote("aes.sunshinectf.org","4200")
	print con.recvuntil("Your text: \r\n")
	con.sendline(Pt)
	Ct = con.recvuntil('\r\n\r\n')[:-4]
	Key = Attack(Pt,Ct)
	print "Extracted Key : ",Key
	con.recvuntil("same key: ") ; 
	Text = con.recv()[:-2] ; sleep(2)
	print "Plain Text : ",Text
	Ct = Encrypt(Text,Key)
	print "Cipher Text: ",Ct
	con.sendline(Ct)
	con.interactive()

Pt = "aaaabbbbccccdddd"
start()