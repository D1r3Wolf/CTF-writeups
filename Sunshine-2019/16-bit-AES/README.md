# Crypto Chall : 16-Bit-AES 
```
Given that the Key is 16 bit , Which means 2 bytes ; Reapeting key

Given a netcat server : nc aes.sunshinectf.org 4200
___________________________________________________________________________________
Welcome, I'm using an AES-128 cipher with a 16-bit key in ECB mode.

I'll give you some help: give me some text, and I'll tell you what it looks like

Your text: 
aaaabbbbccccdddd
30cfd198a3b981c10e78c584abe134f8

Ok, now encrypt this text with the same key: EQlD1aLpLTlKfiR3

___________________________________________________________________________________

First it asks us for Plaintext , Then we send a plain text;
Then it encrypts it, with it's key and Returns out Cipher Text
Then it Gives a Plain text ; Asks for it's Cipher text 
We have to Encrypt the Given plain text with the key with which 
our plaintext is encrypted ;
1). So our First Task is to Get the Key 
2). Encrypt The Given Plaintext and Send the Cipher text
____________________________________________________________________________________
```
### 1] To get the Key : Brute force the key, As the key is 2 bytes (63365) possibilities
```py
from Crypto.Cipher import AES

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
```
Attack Function takes the Plaintext as p , Ciphertext as c ;
Then It returns the Key , With Which the Plain text is Encrypted

### 2] Encrypt The Given Plaintext and Send the Cipher text
Now Script for netcat connections;
```py
from pwn import *

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

Pt = "aaaabbbbccccdddd" # 
start()
```
### Output :
```
$ python exp.py
[+] Opening connection to aes.sunshinectf.org on port 4200: Done
Welcome, I'm using an AES-128 cipher with a 16-bit key in ECB mode.

I'll give you some help: give me some text, and I'll tell you what it looks like

Your text: 

Extracted Key :  fLfLfLfLfLfLfLfL
Plain Text :  04nUWp27ucim2CCQ
Cipher Text:  752d5ca14bad08216bb7282b2734cab1
[*] Switching to interactive mode

Correct! The flag is sun{Who_kn3w_A3$_cou1d_be_s0_vulner8ble?}

[*] Got EOF while reading in interactive
$  
```
