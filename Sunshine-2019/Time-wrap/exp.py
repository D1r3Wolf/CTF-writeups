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
	L = [39] # Starig point , 39
	print "[+] List is :: 39 , ",
	while True:
		N = NUM(L)
		if N[1] != L[-1]:
			Flag = 1 ; continue
		else:
			print N[0],',',
		L.append(N[0])

main()
