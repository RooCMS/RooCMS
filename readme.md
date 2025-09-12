
[![RooCMS](https://dev.roocms.com/skin/default/img/logo.png)](https://www.roocms.com)
===============================
 [![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net/)
[![Database](https://img.shields.io/badge/DB-MySQL%20%7C%20PostgreSQL%20%7C%20Firebird-orange)](https://dev.roocms.com/api/v1/health/details)

| RooCMS   | Open Source Free CMS                              |
|:--------:|:--------------------------------------------------|
| Author   | alex Roosso                                       |
| Web      | https://www.roocms.com                            |
| Contact  | info@roocms.com                                   |
| Download | https://github.com/RooCMS/RooCMS/releases         |
| Source   | https://github.com/RooCMS/RooCMS                  |
| License  | GNU GPL v3                                        |
| CodeRank | [![Codacy Badge](https://app.codacy.com/project/badge/Grade/e9c0df8a7bd5445eb45fc727bf0cd8c4)](https://www.codacy.com/gh/RooCMS/RooCMS/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=RooCMS/RooCMS&amp;utm_campaign=Badge_Grade)  [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/RooCMS/RooCMS/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/RooCMS/RooCMS/?branch=master)                                      |

Contents
--------
- [Notice to our RooCMS Users](#notice-to-our-roocms-users)
- [System requirements](#system-requirements)
- [Recommended software](#recommended-software)
- [Install](#install)
- [Documentation](#documentation)
- [Useful links](#useful-links)
- [Contributing](#contributing)
- [Security](#security)
- [License](#license)

Notice to our RooCMS Users
-------------------
This is the development branch for version 2.0. All code here is unstable and may not work in your environment. Development is done on an Apache server, but with compatibility for Nginx and a standard PHP build in mind. Full testing will be conducted prior to the release.

**NOTE:** The information below may not reflect the current state of the code.

System requirements
-------------------
 - WebServer:	`Apache 2.2`, `Apache 2.4`
 - PHP:		`8.1+`
 - DB:		`MySQL 5.7+`, `MariaDB: 10.10+`, `PostgreSQL 14+`, `Firebird`
 - PHP Extension: 
	`Core`
	`pdo`
	`calendar`
	`date`
	`pcre`
	`gd`
	`mbstring`
	`standard`
	`curl`
	`openssl`
	`json`
	
Recommended software
--------------------
 - WebServer:	`Apache 2.4`
 - PHP:		`8.4`
 - DB:		`MariaDB: 11.7`

Install
-------
1. Download latest release: https://github.com/RooCMS/RooCMS/releases
2. Unpack the archive to the folder with your site on hosting
3. Create a MySQL/MariaDB database on your hosting
4. In browser open link: `https://www.your_site.com/install/`

> Instead of `your_site.com` use the domain name attached to your hosting.

> Attention
> For security purposes, RooCMS does not support the ability to work with a database without a login and password. When trying to use an empty password for the database, the system will show an error.



Documentation
-------------
- API overview and examples: `api/README.md`
- Database migrations: `roocms/database/README.md`
- API schemas: `api/v1/docs/swagger.yaml`, `api/v1/docs/postman.json`

Useful links
------------
- Releases: https://github.com/RooCMS/RooCMS/releases
- Changelog (highlights): `RELEASE.md`
- Website: https://www.roocms.com

Contributing
------------
We welcome issues and pull requests.
- Open an issue describing the change or problem
- Keep PRs focused and small; include rationale and testing notes
- Follow PHP 8.1+ features and project rules (no frameworks/ORM)

Security
--------
If you discover a security vulnerability, please responsibly disclose it to: info@roocms.com

License
-------
[License](https://gplv3.fsf.org/)