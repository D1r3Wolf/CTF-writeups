from Crypto.Util.number import long_to_bytes as lb
from Crypto.Cipher import AES

A = open("AEeeeeS.key").read()

Long = int(A,2) # base 2 to base 10
Key = lb(Long)	# base 10 to base 256(str)

Ct = "c68145ccbc1bd6228da45a574ad9e29a77ca32376bc1f2a1e4cd66c640450d77".decode('hex')

Cipher = AES.new(Key)
Pt = Cipher.decrypt(Ct)
print "[+] flag :",Pt