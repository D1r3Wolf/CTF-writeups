# Reverse : Smash 
```
Given a Executable file : WrestleOfMania
It asks for the acces code : Then it gives the result , code is valid or not
__________________________________________________________________________ 
$ ./WrestleOfMania 
WRESTLE-O-MANIA! We bring your wrestling bets to the internet.
All rights reserved, 1991.
Beginning your installation.....

Please enter your access code: aaaabbbbccccddddeeeeffffgggg
ERROR: Access code invalid.
___________________________________________________________________________
```
### Analyzing the Executable
```
*checkAccessCode* is the function which is checking our input

In checkAccessCode :
____________________________________________________________________________________________________
  process(Input,&local_88);
  prepare(Input);
  local_10 = verify(Input,sig,(size_t)siglen,tbs,local_8c);
  format(Input,&stack0xffffff74);
  iVar1 = checkResult(local_8c);
  return (uint)(iVar1 == 1); 
}
____________________________________________________________________________________________________

In the functions : (process, prepare, verify, format, checkResult)
We hace to make the checkResult function to return 1:
`iVar1 = checkResult(local_8c);
return (uint)(iVar1 == 1);`
[+] checResult function ::::
____________________________________________________________________________________________________
undefined4 checkResult(int param_1){
  int iVar1;
  int *piVar2;
  int *piVar3;
  int local_88 [30];
  int local_10;
  
  iVar1 = 0x1e;
  piVar2 = &DAT_00010de0;
  piVar3 = local_88;
  while (iVar1 != 0) {
    iVar1 = iVar1 + -1;
    *piVar3 = *piVar2;
    piVar2 = piVar2 + 1;
    piVar3 = piVar3 + 1;
  }
  local_10 = 0;
  while( true ) {
    if (0x1d < local_10) {
      return 1;
    }
    if (local_88[local_10] != *(int *)(param_1 + local_10 * 4)) break;
    local_10 = local_10 + 1;
  }
  return 0
}
____________________________________________________________________________________________________
It is noting but Checking the Argument with the &DAT_00010de0;
Equal or Not ; The Argument has to be Equal to $DAT_00010de0
from `if (0x1d < local_10)` we confirm that the length is 0x1d+1 means `30` 

I Have used the gdb to Extract the &DAT_00010de0 (30 values)
____________________________________________________________________________________________________
gdb-peda$ x/30w $edx-0x21d0
0x56555de0: 0x00000e60  0x000003a8  0x00001b80  0x00000f60
0x56555df0: 0x00000120  0x00000ea0  0x00000188  0x00000358
0x56555e00: 0x000001a0  0x000009a0  0x00000184  0x000004e0
0x56555e10: 0x00000c40  0x00000c20  0x000005a0  0x000001c8
0x56555e20: 0x000001d4  0x000009c0  0x000001cc  0x00000b40
0x56555e30: 0x00000ae0  0x00000062  0x00000360  0x00000340
0x56555e40: 0x000005a0  0x00000180  0x000006e0  0x00000b40
0x56555e50: 0x00001540  0x00000fa0
____________________________________________________________________________________________________
`Check_res = [0x00000e60, 0x000003a8, 0x00001b80, 0x00000f60, 0x00000120, 0x00000ea0, 0x00000188, 0x00000358, 0x000001a0, 0x000009a0, 0x00000184, 0x000004e0, 0x00000c40, 0x00000c20, 0x000005a0, 0x000001c8, 0x000001d4, 0x000009c0, 0x000001cc, 0x00000b40, 0x00000ae0, 0x00000062, 0x00000360, 0x00000340, 0x000005a0, 0x00000180, 0x000006e0, 0x00000b40, 0x00001540, 0x00000fa0]`

Now we got the local_8c ;  Which came from the `format` function:
format(Input,&stack0xffffff74);
iVar1 = checkResult(local_8c);

[+] format function:::
____________________________________________________________________________________________________
void format(int Input,int *param_2){
  void *__ptr;
  int local_10;
  
  __ptr = malloc(0x78);
  local_10 = 0;
  while (local_10 < 0x1e) {
    *(int *)(*param_2 + local_10 * 4) = (int)*(char *)(Input + local_10) << ((byte)*(undefined4 *)(*param_2 + local_10 * 4) & 0x1f)
    ;
    local_10 = local_10 + 1;
  }
  free(__ptr);
  return;
}
____________________________________________________________________________________________________
It is nothing but Forming a string by Left shifting our Input with Argument2
Param2[i] = Input[i] << Param2[i]

String[i] = Input[i] << Param2[i]

1)Through checkResukt function we came to know that the value of String should be
`Check_res = [0x00000e60, 0x000003a8, 0x00001b80, 0x00000f60, 0x00000120, 0x00000ea0, 0x00000188, 0x00000358, 0x000001a0, 0x000009a0, 0x00000184, 0x000004e0, 0x00000c40, 0x00000c20, 0x000005a0, 0x000001c8, 0x000001d4, 0x000009c0, 0x000001cc, 0x00000b40, 0x00000ae0, 0x00000062, 0x00000360, 0x00000340, 0x000005a0, 0x00000180, 0x000006e0, 0x00000b40, 0x00001540, 0x00000fa0]`
2)I have extracted the Param2 values using gdb
____________________________________________________________________________________________________
gdb-peda$ x/w 0x56559980
0x56559980: U"\005\003\006\005\002\005\003\003\003\005\002\004\006\005\005\002\002\005\002\006\005\001\003\004\005\003\004\006\006\005"
____________________________________________________________________________________________________

Now we have String , Param2 
Then :
Input[i] = String[i] >> Param2
```

```py
Param2 = "\005\003\006\005\002\005\003\003\003\005\002\004\006\005\005\002\002\005\002\006\005\001\003\004\005\003\004\006\006\005"
Param2 = [ord(x) for x in Param2]
Input  = ['*']*30
Check_res = [0x00000e60, 0x000003a8, 0x00001b80, 0x00000f60, 0x00000120, 0x00000ea0, 0x00000188, 0x00000358, 0x000001a0, 0x000009a0, 0x00000184, 0x000004e0, 0x00000c40, 0x00000c20, 0x000005a0, 0x000001c8, 0x000001d4, 0x000009c0, 0x000001cc, 0x00000b40, 0x00000ae0, 0x00000062, 0x00000360, 0x00000340, 0x000005a0, 0x00000180, 0x000006e0, 0x00000b40, 0x00001540, 0x00000fa0]

for i in range(30):
	Input[i] = Check_res[i] >> Param2[i]
print(''.join([chr(x) for x in Input]))
```
### Output 
```
$ py exp.py 
sun{Hu1k4MaN1a-ruNs-W1l4-0n-U}
```

# flag : `sun{Hu1k4MaN1a-ruNs-W1l4-0n-U}`