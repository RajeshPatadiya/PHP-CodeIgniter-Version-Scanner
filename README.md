# codeigniter-version-scanner

Purpose of this project is to make a scanner that can detect:

	- If a website is using CodeIgniter
	- What version of CodeIgniter is used

That can be helpfull to include in (automated) security scanners and used by penetration testers or 
for other purposes in IT Security. Hopefully developers will be better stimulated to update their 
CodeIgniter applications/websites.

This scanner can be build in PHP and in Python.
The use of this scanner should be as easy as calling the script with a URL to be tested.

For now this is just a proof of concept.
If you want to join this project, please contact me.

# TODO's
	- Finish PHP version of the scanner
	- Find more detectable differences in CodeIgniter versions
	- Make a similar Python version of the scanner
	- Other improvements
	
# Nice-to-have's
	- It can be nice to include CVE warnings similar to WPScan
		http://www.cvedetails.com/version-list/6918/11625/1/Codeigniter-Codeigniter.html
	- And other version specific security warnings like:
		https://github.com/Dionach/CodeIgniterXor - Session cookie decryption vulnerability 
		https://github.com/bcit-ci/CodeIgniter/pull/370 - SQL injection
		https://github.com/bcit-ci/CodeIgniter/pull/486 - MIME-type Injection
		https://github.com/bcit-ci/CodeIgniter/pull/606 - Potential SQL injection
		https://github.com/bcit-ci/CodeIgniter/pull/1366 - Code Injection
		https://github.com/bcit-ci/CodeIgniter/issues/1705 - XSS
		https://github.com/bcit-ci/CodeIgniter/issues/2667 - XSS
		https://github.com/bcit-ci/CodeIgniter/issues/2965 - XSS
		https://github.com/bcit-ci/CodeIgniter/issues/3189 - Template Injection
		https://github.com/bcit-ci/CodeIgniter/issues/3292 - XSS
		https://nealpoole.com/blog/2013/07/codeigniter-21-xss-clean-filter-bypass/ - XSS (CVE-2013-4891)
