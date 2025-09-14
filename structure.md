# RooCMS project structure

This document describes the organization of files and directories in the RooCMS project.

## Root directory

```
/
├── api/                    # API interface
├── roocms/                 # Core RooCMS
├── storage/                # Storage
├── themes/                 # Themes
├── upload/                 # Uploaded files
├── err.php                 # Error handler
├── favicon.ico             # Favicon
├── index.php               # Main entry point
├── LICENSE.md              # License
├── logobg.png              # Logo
├── phpcs.xml               # PHP CodeSniffer config
├── phpstan.neon            # PHPStan config
├── README.md               # Main docs
├── RELEASE.md              # Release info
├── robots.txt              # Robots rules
└── structure.md            # Project structure docs
```

## API (`/api/`)

Module API for interaction with the system through the RESTful interface.

```
api/
├── index.php               # API entry point
├── README.md               # API Docs
├── router.php              # Request router
└── v1/                     # API version 1
    ├── controller_auth.php     # Authentication controller
    ├── controller_base.php     # Base controller
    ├── controller_csp.php      # Content Security Policy controller
    ├── controller_health.php   # Health check controller
    ├── controller_users.php    # Users controller
    ├── docs/                   # API docs
    │   ├── postman.json        # Postman collection
    │   └── swagger.yaml        # Swagger docs
    ├── middleware_auth.php     # Middleware authentication
    └── middleware_role.php     # Middleware roles
```

## Core system (`/roocms/`)

Core CMS system with main classes and configuration.

### Classes (`/roocms/class/`)

```
roocms/class/
├── class_apiHandler.php            # API request handler
├── class_auth.php                  # Authentication system
├── class_db.php                    # Main database class
├── class_dbConnect.php             # Database connection
├── class_dbMigrator.php            # Database migrations
├── class_dbQueryBuilder.php        # SQL query builder
├── class_debugger.php              # Debugger system
├── class_defaultControllerFactory.php  # Default controller factory
├── class_defaultMiddlewareFactory.php  # Default middleware factory
├── class_mailer.php                # Mailer system
├── class_role.php                  # Roles system
├── class_settings.php              # System settings
├── class_shteirlitz.php            # Special functionality
├── class_user.php                  # User management
├── interface_controllerFactory.php # Controller factory interface
├── interface_middlewareFactory.php # Middleware factory interface
├── trait_dbExtends.php             # DB extends trait
└── trait_debugLog.php              # Debug log trait
```

### Configuration (`/roocms/config/`)

```
roocms/config/
├── config.php              # Main configuration
├── csp.cfg.php             # Content Security Policy settings
├── defines.php             # System constants
└── set.cfg.php             # Additional settings
```

### Database (`/roocms/database/`)

```
roocms/database/
├── migrate_cli.php             # CLI interface for migrations
├── migrations/                 # Migration files
│   ├── example_1.php           # Migration example 1
│   ├── example_2.php           # Migration example 2
│   ├── migrate_2_0_0_1.php     # Migration version 2.0.0.1
│   └── migrate_2_0_0_2.php     # Migration version 2.0.0.2
└── README.md                   # Migration docs
```

### Helpers (`/roocms/helpers/`)

```
roocms/helpers/
├── check.php               # Check functions
├── cli/                    # CLI utilities
│   └── pastcost_cli.php    # CLI utility pastcost
├── debug.php               # Debug functions
├── functions.php           # Common functions
└── sanitize.php            # Data sanitization functions
```

### Services (`/roocms/services/`)

```
roocms/services/
├── auth.php                # Authentication service
└── user.php                # User service
```

### Initialization

```
roocms/
└── init.php                # System initialization file
```

## Storage (`/storage/`)

Directory for storing data, logs and resources.

```
storage/
├── assets/                 # System resources
│   ├── critical.html       # Critical resources
│   ├── index.php           # Protected file
│   └── mail/               # Email templates
│       ├── notice.php      # Notice template
│       └── welcome.php     # Welcome template
├── index.php               # Protected file
└── logs/                   # System logs
    ├── debug.log           # Debug log
    ├── index.php           # Protected file
    ├── lowerrors.log       # Log of minor errors
    └── syserrors.log       # Log of system errors
```

## Uploads (`/upload/`)

Directory for storing uploaded user files.

```
upload/
├── files/                  # Uploaded files
│   └── index.php           # Protected file
├── images/                 # Uploaded images
└── index.php               # Protected file
```

## Themes (`/themes/`)

System themes for configuring the appearance of the website.

```
themes/
└── default/                        # Default theme
    ├── assets/                     # Theme resources
    │   ├── css/                    # CSS styles
    │   │   ├── app.css             # Application main styles
    │   │   ├── dist/               # Pico CSS sources
    │   │   │   └── pico/           # Pico CSS framework components
    │   │   ├── pico.css            # Pico CSS framework
    │   │   └── pico.min.css        # Minified Pico CSS
    │   └── js/                     # JavaScript files
    │       ├── alpine.csp.min.js   # Alpine.js with CSP support
    │       ├── alpine.min.js       # Alpine.js framework
    │       ├── app/                # Application modules
    │       │   ├── alpine-defer.js # Deferred loading Alpine
    │       │   ├── alpine-start.js # Alpine initialization
    │       │   ├── api.js          # API client
    │       │   ├── auth.js         # Authentication
    │       │   ├── config.js       # Configuration
    │       │   └── main.js         # Main module
    │       └── pages/              # Pages scripts
    │           ├── auth_login.js   # Login page
    │           ├── home.js         # Home page
    │           └── users_index.js  # Users list
    ├── layouts/                    # Layouts templates
    │   └── base.php                # Base layout
    ├── pages/                      # Pages templates
    │   ├── 404.php                 # 404 page
    │   ├── auth/                   # Authentication pages
    │   │   └── login.php           # Login page
    │   ├── index.php               # Home page
    │   └── users/                  # Users pages
    │       └── index.php           # Users list
    └── partials/                   # Partial templates
        ├── footer.php              # Footer
        └── header.php              # Header
```

## Features architecture

### Development principles
- **Without frameworks**: Project uses pure PHP without external frameworks
- **Without ORM**: Direct SQL queries through PDO
- **PHP 8.1+**: Modern PHP 8.1 and higher capabilities
- **MVC pattern**: Own implementation of Model-View-Controller
- **API-first**: RESTful API interface
- **Theme system**: Modular theme system

### Naming conventions
- **snake_case**: for functions, methods and variables
- **CamelCase**: for class names and controllers
- **Class prefixes**: `class_` for main classes
- **Controller prefixes**: `controller_` for API controllers
- **Middleware prefixes**: `middleware_` for middleware

### Security
- Protected `index.php` files in directories with data
- Content Security Policy configuration
- Roles and authentication system
- Data sanitization

### Code quality tools
- **Logging system**: Tracking errors and debugging

### Frontend technologies
- **Pico CSS**: Minimalistic CSS framework for fast prototyping
- **Alpine.js**: Lightweight JavaScript framework for interactivity
- **Modular architecture**: Division of JavaScript code by pages and components
- **CSP compatibility**: Support for Content Security Policy

This project is a modern CMS system built on the principles of pure PHP with a focus on performance, security and ease of maintenance. The system includes a modular theme architecture with modern frontend technologies for creating responsive and interactive user interfaces.
