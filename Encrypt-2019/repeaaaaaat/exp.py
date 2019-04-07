from requests import get
import sys
import html
def Obj(S):
	L = []
	while "<class '" in S:
		s = S.find("<class '")+8
		e = S.find("'>",s)
		# print(S[s])
		L.append(S[s:e])
		S = S[e:]
	return L

A = sys.argv[1]
A = get("http://127.0.0.1:5000/?secret="+A).text
s = A.find("</div>")+6
e = A.find("<!--")
Str = A[s:e]
S = html.unescape(Str)
Check = ''
if len(sys.argv) == 3:
	if sys.argv[2] == '-p':
		print(Obj(S))
print(S)


