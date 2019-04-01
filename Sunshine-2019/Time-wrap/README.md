# Reverse : Smash 
* Given a netcat connection :: nc tw.sunshinectf.org 4101
```
$ nc tw.sunshinectf.org 4101

I'm going to give you some numbers between 0 and 999.
Repeat them back to me in 30 seconds or less!

```
* we have to guess the number and input it 
```
$ nc tw.sunshinectf.org 4101

I'm going to give you some numbers between 0 and 999.
Repeat them back to me in 30 seconds or less!
0
39
H3r3 w3 g0 aga1n!
```
* Then it will the correct number (39) and terminates
* Or else if you give the number correctly , Then it asks to guess the second number ,
* Repeats like upto some iterations, And after those gives the flag
```
$ nc tw.sunshinectf.org 4101
I'm going to give you some numbers between 0 and 999.
Repeat them back to me in 30 seconds or less!
39
39
Icr3dible!
0
61
Y0u kn0w wh4t y0u're do1ng, right?!

```
* Here the numbers wont change , 39 always comes first only ,
* So we have to bruteforce for all the numbers , to get the List upto we get the Flag

```py
from pwn import *
def find_flag(L):
	for i in L:
		if 'sun{' in i: return i
def Number(L):
	I = []
	for i in L:
		if len(I) == 2: break
		try:
			I.append(int(i))
		except:
			pass
	return I
def NUM(L):
	con = remote("tw.sunshinectf.org",4101,level='error')
	for i in range(len(L)):
		con.sendline(str(L[i]))
	con.sendline('0') ;
	P = con.recvall().split('\n')[-10:][::-1]
	if Flag:
		print '\n[+] flag :: %s'%find_flag(P)
		exit()
	con.close()
	return Number(P)
Flag = False
def main():
	global Flag
	L = [39]
	print "[+] List is :: 39 , ",
	while True:
		N = NUM(L)
		if N[1] != L[-1]:
			Flag = 1 ; continue
		else:
			print N[0],',',
		L.append(N[0])

main()
```
### output 
```
$ py2 exp.py 
[+] List is :: 39 ,  61 , 267 , 475 , 178 , 760 , 660 , 257 , 897 , 994 , 610 , 639 , 813 , 495 , 832 , 647 , 228 , 74 , 474 , 215 , 523 , 905 , 65 , 741 , 814 , 742 , 787 , 58 , 917 , 548 , 465 , 309 , 609 , 733 , 784 , 140 , 493 , 444 , 397 , 391 , 790 , 359 , 382 , 956 , 854 , 566 , 603 , 435 , 640 , 429 , 650 , 163 , 335 , 716 , 256 , 149 , 458 , 396 , 559 , 375 , 944 , 24 , 684 , 905 , 757 , 820 , 397 , 251 , 264 , 794 , 994 , 407 , 506 , 728 , 363 , 360 , 294 , 318 , 795 , 286 , 747 , 446 , 801 , 434 , 514 , 410 , 935 , 972 , 806 , 494 , 699 , 102 , 519 , 736 , 359 , 276 , 908 , 757 , 879 , 525 , 903 , 225 , 932 , 409 , 953 , 647 , 122 , 599 , 965 , 917 , 237 , 712 , 363 , 39 , 147 , 877 , 801 , 82 , 201 , 607 , 929 , 253 , 61 , 448 , 341 , 772 , 724 , 249 , 529 , 604 , 774 , 785 , 181 , 58 , 546 , 135 , 705 , 668 , 86 , 670 , 586 , 324 , 735 , 301 , 715 , 234 , 531 , 516 , 316 , 732 , 123 , 245 , 337 , 536 , 45 , 678 , 308 , 770 , 280 , 190 , 726 , 406 , 975 , 907 , 465 , 521 , 394 , 170 , 190 , 481 , 841 , 128 , 805 , 576 , 429 , 520 , 162 , 960 , 36 , 478 , 45 , 511 , 76 , 382 , 399 , 473 , 413 , 707 , 243 , 693 , 897 , 321 , 99 , 224 , 581 , 916 , 98 , 975 , 87 , 288 , 456 , 280 , 416 , 613 , 208 , 197 , 485 , 370 , 158 , 873 , 200 , 203 , 384 , 628 , 937 , 135 , 102 , 350 , 843 , 697 , 395 , 92 , 19 , 847 , 669 , 600 , 763 , 767 , 927 , 202 , 55 , 736 , 482 , 823 , 701 , 690 , 20 , 187 , 60 , 530 , 60 , 613 , 85 , 797 , 241 , 23 , 932 , 343 , 725 , 127 , 41 , 121 , 220 , 412 , 320 , 241 , 364 , 83 , 8 , 291 , 286 , 63 , 379 , 120 , 238 , 81 , 811 , 610 , 268 , 223 , 141 , 680 , 836 , 226 , 477 , 430 , 249 , 762 , 773 , 975 , 889 , 166 , 448 , 461 , 930 , 768 , 702 , 646 , 203 , 710 , 938 , 841 , 125 , 669 , 962 , 715 , 750 , 773 , 326 , 370 , 
[+] flag :: sun{derotser_enilemit_1001130519}
```
### flag : `sun{derotser_enilemit_1001130519}`
