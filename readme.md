[![RooCMS](https://dev.roocms.com/skin/default/img/logo.png)](https://www.roocms.com) 
===============================
[![RooCMS](https://img.shields.io/badge/RooCMS-2.0.0%20alpha-green)](https://www.roocms.com)  [![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)


Contents
--------
- [Notice to our RooCMS Users](#notice-to-our-roocms-users)
- [System requirements](#system-requirements)
- [Recommended software](#recommended-software)
- [Key Features](#key-features)
- [Install](#install)
- [Documentation](#documentation)
- [Useful links](#useful-links)
- [Architecture](#architecture)
- [Contributing](#contributing)
- [Security](#security)
- [License](#license)
- [CodeRank and Dev Status](#coderrank-and-dev-status)

Notice to our RooCMS Users
-------------------
This is the development branch for version 2.0. All code here is unstable and may not work in your environment. Development is done on an Apache server, but with compatibility for Nginx and a standard PHP build in mind. Full testing will be conducted prior to the release.

**NOTE:** The information below may not reflect the current state of the code.

System requirements
-------------------
 - WebServer:	[![Apache 2.2](https://img.shields.io/badge/Apache-2.2-red)](https://httpd.apache.org/)  [![Apache 2.4](https://img.shields.io/badge/Apache-2.4-red)](https://httpd.apache.org/)  [![Nginx](https://img.shields.io/badge/Nginx-latest-brightgreen)](https://nginx.org/)
 - PHP:		[![PHP](https://img.shields.io/badge/PHP-8.1+-8892BF.svg)](https://php.net)
 - DB:		[![MySQL](https://img.shields.io/badge/MySQL-5.7+-00758F)](https://www.mysql.com/)  [![MariaDB](https://img.shields.io/badge/MariaDB-10.10+-C0765A)](https://mariadb.org/)  [![PostgreSQL](https://img.shields.io/badge/PostgreSQL-14+-336791)](https://www.postgresql.org/)  [![Firebird](https://img.shields.io/badge/Firebird-3.0+-F41A0A)](https://www.firebirdsql.org/)
 - Frontend:	Modern browser with JavaScript support
 - PHP Extension: 
	[![Core](https://img.shields.io/badge/Core-yes-8892BF)](https://php.net)  [![pdo](https://img.shields.io/badge/pdo-yes-8892BF)](https://php.net)  [![calendar](https://img.shields.io/badge/calendar-yes-8892BF)](https://php.net)  [![date](https://img.shields.io/badge/date-yes-8892BF)](https://php.net)  [![pcre](https://img.shields.io/badge/pcre-yes-8892BF)](https://php.net)  [![gd](https://img.shields.io/badge/gd-yes-8892BF)](https://php.net)  [![mbstring](https://img.shields.io/badge/mbstring-yes-8892BF)](https://php.net)  [![standard](https://img.shields.io/badge/standard-yes-8892BF)](https://php.net)  [![curl](https://img.shields.io/badge/curl-yes-8892BF)](https://php.net)  [![openssl](https://img.shields.io/badge/openssl-yes-8892BF)](https://php.net)  [![json](https://img.shields.io/badge/json-yes-8892BF)](https://php.net)  [![fileinfo](https://img.shields.io/badge/fileinfo-yes-8892BF)](https://php.net)  [![zip](https://img.shields.io/badge/zip-yes-8892BF)](https://php.net)  [![exif](https://img.shields.io/badge/exif-yes-8892BF)](https://php.net)
	
	
Recommended software
--------------------
 - WebServer:	[![Apache 2.4](https://img.shields.io/badge/Apache-2.4-red)](https://httpd.apache.org/)
 - PHP:		[![PHP](https://img.shields.io/badge/PHP-8.4-8892BF)](https://php.net)
 - DB:		[![MariaDB](https://img.shields.io/badge/MariaDB-11.7-C0765A)](https://mariadb.org/)

Key Features
------------
- **Pure PHP**: No external frameworks or ORM dependencies
- **Modern PHP 8.1+**: Uses latest PHP features and strict typing
- **RESTful API**: Complete API interface for all operations
- **File Management System**: Advanced file upload, processing and management with multiple format support
- **Theme System**: Modular theme architecture with modern frontend
- **Frontend Stack**: Tailwind CSS 4.x + Alpine.js for interactivity
- **Security First**: CSP support, role-based access, input sanitization
- **Database Agnostic**: Support for MySQL, MariaDB, PostgreSQL, Firebird
- **Migration System**: Database schema versioning and migrations
- **Backup System**: Comprehensive database backup and restore with CLI/API interfaces

Install
-------
1. Download latest release: https://github.com/RooCMS/RooCMS/releases
2. Unpack the archive to the folder with your site on hosting
3. Create a MySQL/MariaDB database on your hosting
4. Configure your web server and database settings
5. Access your site through the web browser

> **Note**: This is development version 2.0 - installation process may differ from final release.

> **Security Notice**: For security purposes, RooCMS requires proper database authentication. Empty database passwords are not supported.



Documentation
-------------
- Project structure: `structure.md`
- API overview and examples: `api/README.md`
- Database migrations: `roocms/database/README_Migrate.md`
- Database backup system: `roocms/database/README_Backup.md`
- API schemas: `api/v1/docs/swagger.yaml`, `api/v1/docs/postman.json`

Useful links
------------
- Releases: https://github.com/RooCMS/RooCMS/releases
- Changelog (highlights): `RELEASE.md`
- Website: https://www.roocms.com

Architecture
------------
RooCMS follows a **framework-free** approach with these core principles:

- **No External Dependencies**: Pure PHP without frameworks or ORM
- **Custom MVC**: Own implementation of Model-View-Controller pattern
- **File Management**: Advanced file processing with trait-based architecture
- **API-First Design**: RESTful API as primary interface
- **Modern PHP**: PHP 8.1+ features with strict typing
- **Theme System**: Modular frontend with multiple rendering engines
- **Security Focus**: CSP, role-based access, input sanitization

### Directory Structure
```
├── api/          # RESTful API endpoints
├── roocms/       # Core CMS system
├── themes/       # Theme system (Tailwind CSS 4.x + Alpine.js)
├── storage/      # Data storage and logs
└── up/           # User uploaded files
```

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

CodeRank and Dev Status
-------

<div align="center">

![GitHub commit activity](https://img.shields.io/github/commit-activity/m/RooCMS/RooCMS/dev) ![GitHub last commit](https://img.shields.io/github/last-commit/RooCMS/RooCMS/dev) ![Codacy Badge](https://app.codacy.com/project/badge/Grade/e9c0df8a7bd5445eb45fc727bf0cd8c4) ![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/RooCMS/RooCMS/badges/quality-score.png?b=dev)

</div>