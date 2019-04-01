# Scripting Chall : Entry-Exam
```
http://ee.sunshinectf.org for the chall
Given Website for the Exam : http://ee.sunshinectf.org/exam
```
![Exam1](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Sunshine-2019/Entry-Exam/img/Exam2.png)
![Exam2](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Sunshine-2019/Entry-Exam/img/Exam1.png)
```
We have to find the Answers for all 20 Questions in Options (A,B,C,D)
Now we have to mark that options in given scatron.jpg , As shown in figure below
```
![scatron](https://raw.githubusercontent.com/D1r3Wolf/CTF-writeups/master/Sunshine-2019/Entry-Exam/img/Ans.png)
```
And Then submit that jpg to the http://ee.sunshinectf.org/exam
Then It gives another Exam :
Our Tasks :::
1). Grab the Answers of the Text
2). Mark it on the Given scatron image file
3). Then Post that image to site , Repeat it until the server gives the flag

We have to script our code for those tasks
```
### Python CODE [run.py](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Sunshine-2019/Entry-Exam/run.py "run.py")
### output
```
$ python3 run.py
[0] Grabbed Answers :: dict_values(['C', 'B', 'B', 'D', 'C', 'A', 'D', 'B', 'A', 'C', 'A', 'C', 'B', 'B', 'C', 'B', 'B', 'D', 'B', 'C'])
[+] Wow Move On :: <h1>Exam Section 2</h1><ol type="1">
[1] Grabbed Answers :: dict_values(['A', 'B', 'D', 'A', 'D', 'A', 'C', 'D', 'C', 'B', 'B', 'B', 'A', 'B', 'C', 'A', 'A', 'B', 'B', 'C'])
[+] Wow Move On :: <h1>Exam Section 3</h1><ol type="1">
[2] Grabbed Answers :: dict_values(['A', 'B', 'D', 'C', 'A', 'C', 'D', 'B', 'D', 'A', 'B', 'A', 'A', 'C', 'A', 'A', 'B', 'B', 'D', 'C'])
[+] Wow Move On :: <h1>Exam Section 4</h1><ol type="1">
[3] Grabbed Answers :: dict_values(['C', 'D', 'C', 'A', 'A', 'C', 'D', 'A', 'A', 'A', 'C', 'B', 'C', 'C', 'A', 'C', 'C', 'D', 'D', 'A'])
[+] Wow Move On :: <h1>Exam Section 5</h1><ol type="1">
[4] Grabbed Answers :: dict_values(['A', 'B', 'D', 'B', 'B', 'C', 'A', 'D', 'A', 'C', 'C', 'C', 'D', 'C', 'C', 'C', 'B', 'C', 'B', 'D'])
[+] Wow Move On :: <h1>Exam Section 6</h1><ol type="1">
[5] Grabbed Answers :: dict_values(['B', 'B', 'B', 'C', 'B', 'A', 'B', 'D', 'B', 'C', 'D', 'A', 'A', 'B', 'C', 'C', 'C', 'D', 'B', 'C'])
[+] Wow Move On :: <h1>Exam Section 7</h1><ol type="1">
[6] Grabbed Answers :: dict_values(['C', 'D', 'C', 'B', 'B', 'B', 'A', 'B', 'A', 'C', 'B', 'C', 'A', 'C', 'D', 'B', 'A', 'A', 'D', 'A'])
[+] Wow Move On :: <h1>Exam Section 8</h1><ol type="1">
[7] Grabbed Answers :: dict_values(['D', 'D', 'D', 'D', 'A', 'D', 'A', 'C', 'C', 'A', 'A', 'C', 'C', 'A', 'A', 'B', 'D', 'B', 'A', 'A'])
[+] Wow Move On :: <h1>Exam Section 9</h1><ol type="1">
[8] Grabbed Answers :: dict_values(['D', 'D', 'B', 'A', 'B', 'C', 'D', 'C', 'C', 'A', 'C', 'C', 'A', 'D', 'D', 'A', 'D', 'A', 'A', 'A'])
[-] Error :: sun{7h3_b357_7h3r3_15_7h3_b357_7h3r3_w45_7h3_b357_7h3r3_3v3r_w1ll_b3}
[+] Flag is :: sun{7h3_b357_7h3r3_15_7h3_b357_7h3r3_w45_7h3_b357_7h3r3_3v3r_w1ll_b3}

```
### flag is :: `sun{7h3_b357_7h3r3_15_7h3_b357_7h3r3_w45_7h3_b357_7h3r3_3v3r_w1ll_b3}`
