# Crypto : AEeeeeS

```
the key given in [AEeeeeS.key](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Encrypt-2019/AEeeeeS/AEeeeeS.key)
'110100001010100111001101110001011101000010100000110001001101100010100101100010011110010111010001100101011010110110010101111001'

And
ciphertext: c68145ccbc1bd6228da45a574ad9e29a77ca32376bc1f2a1e4cd66c640450d77
```
* Key is also given we just need to convert to string and decrypt!
```py
from Crypto.Util.number import long_to_bytes as lb
from Crypto.Cipher import AES
A = '110100001010100111001101110001011101000010100000110001001101100010100101100010011110010111010001100101011010110110010101111001'

Long = int(A,2) # base 2 to base 10
Key = lb(Long)	# base 10 to base 256(str)

Ct = "c68145ccbc1bd6228da45a574ad9e29a77ca32376bc1f2a1e4cd66c640450d77".decode('hex')

Cipher = AES.new(Key)
Pt = Cipher.decrypt(Ct)
print "[+] flag :",Pt
```

### OUTPUT [exp.py](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/AEeeeeS/exp.py)
```
$ python exp.py 
[+] flag : encryptCTF{3Y3S_4R3_0N_A3S_3CB!}
```

# flag : `encryptCTF{3Y3S_4R3_0N_A3S_3CB!}`