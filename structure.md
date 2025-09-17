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
    ├── controller_backup.php   # Database backup controller
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
├── class_apiHandler.php                # API request handler
├── class_auth.php                      # Authentication system
├── class_db.php                        # Main database class
├── class_dbBackuper.php                # Database backup and restore system
├── class_dbConnect.php                 # Database connection
├── class_dbMigrator.php                # Database migrations
├── class_dbQueryBuilder.php            # SQL query builder
├── class_debugger.php                  # Debugger system
├── class_defaultControllerFactory.php  # Default controller factory
├── class_defaultMiddlewareFactory.php  # Default middleware factory
├── class_dependencyContainer.php       # Dependency injection container
├── class_templateRendererPhp.php       # PHP template renderer
├── class_templateRendererHtml.php      # HTML template renderer
├── class_themeConfig.php               # Theme configuration implementation
├── class_themes.php                    # Theme management system
├── class_mailer.php                    # Mailer system
├── class_role.php                      # Roles system
├── class_settings.php                  # System settings
├── class_shteirlitz.php                # Special functionality
├── class_user.php                      # User management
├── interface_controllerFactory.php     # Controller factory interface
├── interface_middlewareFactory.php     # Middleware factory interface
├── interface_templateRenderer.php      # Template renderer interface
├── interface_themeConfig.php           # Theme configuration interface
├── trait_dbExtends.php                 # DB extends trait
└── trait_debugLog.php                  # Debug log trait
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
├── backup_cli.php              # CLI interface for database backups
├── backups/                    # Database backup files storage
│   ├── .htaccess               # Web access protection rules (soon)
│   └── index.php               # Directory access protection
├── migrate_cli.php             # CLI interface for migrations
├── migrations/                 # Migration files
│   ├── example_1.php           # Migration example 1
│   ├── example_2.php           # Migration example 2
│   ├── migrate_2_0_0_1.php     # Migration version 2.0.0.1
│   └── migrate_2_0_0_2.php     # Migration version 2.0.0.2
├── README_Migrate.md           # Migration docs
└── README_Backup.md            # Database backup system docs
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
├── backup.php              # Database backup service
└── user.php                # User service
```

### Initialization

```
roocms/
└── init.php                # System initialization file (Initializes configuration, helpers, autoloader, database, and DI container.)
```

Registers core services and template system:

```
$container->register(TemplateRendererPhp::class, TemplateRendererPhp::class, true);
$container->register(TemplateRendererHtml::class, TemplateRendererHtml::class, true);
$container->register(Themes::class, function(DependencyContainer $c) {
    return new Themes(
        $c->get(TemplateRendererPhp::class),
        $c->get(TemplateRendererHtml::class),
        'themes'
    );
}, true);
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

System themes for configuring the appearance of the website. RooCMS supports two rendering modes: PHP and HTML.

```
themes/
├── default/                        # Default theme
│   ├── assets/                     # Theme resources
│   │   ├── css/                    # CSS styles
│   │   │   ├── app.css             # Application main styles
│   │   │   ├── dist/               # Pico CSS sources
│   │   │   │   └── pico/           # Pico CSS framework components
│   │   │   ├── pico.css            # Pico CSS framework
│   │   │   └── pico.min.css        # Minified Pico CSS (soon)
│   │   └── js/                     # JavaScript files
│   │       ├── alpine.csp.min.js   # Alpine.js with CSP support
│   │       ├── alpine.min.js       # Alpine.js framework
│   │       ├── app/                # Application modules
│   │       │   ├── alpine-defer.js # Deferred loading Alpine
│   │       │   ├── alpine-start.js # Alpine initialization
│   │       │   ├── api.js          # API client
│   │       │   ├── auth.js         # Authentication
│   │       │   ├── config.js       # Configuration
│   │       │   └── main.js         # Main module
│   │       └── pages/              # Pages scripts
│   │           ├── auth_login.js   # Login page
│   │           ├── home.js         # Home page
│   │           └── users_index.js  # Users list
│   ├── layouts/                    # Layouts templates
│   │   └── base.php                # Base layout
│   ├── pages/                      # Pages templates
│   │   ├── 404.php                 # 404 page
│   │   ├── auth/                   # Authentication pages
│   │   │   └── login.php           # Login page
│   │   ├── index.php               # Home page
│   │   └── users/                  # Users pages
│   │       └── index.php           # Users list
│   ├── partials/                   # Partial templates
│   │   ├── footer.php              # Footer
│   │   └── header.php              # Header
│   └── theme.json                  # Theme manifest (type: "php")
│
└── default_html/                   # HTML theme (placeholders, includes, conditionals)
    ├── assets/                     # Theme resources (css/js)
    │   ├── css/                    # CSS styles
    │   │   ├── app.css             # Application main styles
    │   │   ├── pico.css            # Pico CSS framework
    │   │   └── pico.min.css        # Minified Pico CSS (soon)
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
    ├── layouts/
    │   └── base.html               # Base layout (HTML)
    ├── pages/
    │   ├── 404.html                # 404 page
    │   ├── auth/
    │   │   └── login.html          # Login page
    │   ├── index.html              # Home page
    │   └── users/
    │       └── index.html          # Users list
    ├── partials/
    │   ├── header.html             # Header
    │   ├── footer.html             # Footer
    └── theme.json                  # Theme manifest (type: "html")
```

HTML engine supports:
- `{{variable}}` and `{{{raw_variable}}}`
- `<!-- if: variable --> ... <!-- endif -->`
- `<!-- foreach: items as item --> ... <!-- endforeach -->`
- `<!-- include: partials/header.html -->`
- `{{asset: css/app.css}}` / `{{asset: js/app.js}}`

## Features architecture

### Development principles
- **Without frameworks**: Project uses pure PHP without external frameworks
- **Without ORM**: Direct SQL queries through PDO
- **PHP 8.1+**: Modern PHP 8.1 and higher capabilities
- **MVC pattern**: Own implementation of Model-View-Controller
- **Dependency Injection**: Custom DI container for managing dependencies
- **SOLID principles**: Clean architecture with dependency inversion
- **API-first**: RESTful API interface
- **Theme system**: Modular theme system

### Database Backup System

Comprehensive database backup and restore system with CLI and API interfaces, featuring complete database structure preservation and enterprise-level security.

#### Components
- **DbBackuper class** (`class_dbBackuper.php`) - Core backup functionality with full structure support
- **BackupService** (`services/backup.php`) - Business logic layer with validation and logging  
- **CLI interface** (`backup_cli.php`) - Command-line utility for backup operations
- **API controller** (`controller_backup.php`) - RESTful API for backup management
- **Storage directory** (`database/backups/`) - Secured backup files storage location

#### Key Features
- **Complete Structure Preservation** - All database objects: PRIMARY/FOREIGN/UNIQUE keys, indexes, constraints, AUTO_INCREMENT, DEFAULT values, ENUM types
- **Universal Cross-Database Format** - Compatible with MySQL/MariaDB, PostgreSQL, Firebird
- **Enterprise Security** - Multi-layer protection: .htaccess rules, API-only access, admin authentication, path traversal protection
- **Performance Optimization** - Gzip compression (9:1 ratio), memory efficiency, batch processing
- **Auto-naming** - Date/time-based backup filenames
- **Transaction Safety** - Rollback support with BEGIN/COMMIT blocks

#### API Endpoints
- `GET /api/v1/backup/status` - System status and statistics
- `GET /api/v1/backup/list` - List all available backups
- `POST /api/v1/backup/create` - Create new backup
- `POST /api/v1/backup/restore` - Restore from backup
- `DELETE /api/v1/backup/delete/{filename}` - Delete backup file
- `GET /api/v1/backup/download/{filename}` - Download backup file
- `GET /api/v1/backup/logs` - Backup operation logs

#### CLI Commands
- `php backup_cli.php create [--filename=name] [--compress] [--universal]` - Create new backup
- `php backup_cli.php restore [--filename=name] [--batch-size=1000]` - Restore from backup
- `php backup_cli.php list` - List all available backups with metadata
- `php backup_cli.php delete --filename=name` - Delete specific backup file
- `php backup_cli.php status` - Show backup system status and statistics
- `php backup_cli.php help` - Show detailed help information

**CLI Options:**
- `--filename` - Custom backup filename (auto-generates date/time if omitted)
- `--compress` - Enable gzip compression (default: true)
- `--universal` - Use universal cross-database format (default: true)
- `--batch-size` - Number of rows to process per batch during restore
- `--exclude-tables` - Comma-separated list of tables to exclude
- `--structure-only` - Backup table structure without data
- `--data-only` - Backup data without table structure

### Dependency Injection architecture

RooCMS implements a custom dependency injection (DI) container for managing service dependencies and promoting clean architecture following SOLID principles.

#### DI Container (`class_dependency_container.php`)
- **Automatic dependency resolution**: Uses reflection to analyze constructor parameters
- **Singleton support**: Long-lived services can be registered as singletons
- **Factory functions**: Support for custom service creation logic
- **Service registry**: Centralized management of all services

#### Registered services

**Core services (singletons):**
- `Db` - Database connection and queries
- `Auth` - Authentication and authorization
- `User` - User management operations
- `Settings` - System configuration
- `Mailer` - Email sending system
- `DbBackuper` - Database backup and restore operations
- `UserService` - Business logic for user operations
- `AuthService` - Business logic for authentication
- `BackupService` - Business logic for backup operations

**Request-scoped services (new instance per request):**
- `UsersController` - User management API controller
- `AuthController` - Authentication API controller
- `BackupController` - Database backup API controller

#### Service dependencies

```
AuthService
├── Db (database)
├── Auth (authentication)
├── Settings (configuration)
└── Mailer (email sending)

UserService
├── Db (database)
└── User (user operations)

BackupService
├── DbBackuper (backup model)
└── Db (database connection)

DbBackuper
└── Db (database connection)

UsersController
├── UserService (business logic)
├── Auth (authentication)
├── Settings (configuration)
└── Mailer (email sending)

AuthController
└── AuthService (business logic)

BackupController
├── BackupService (business logic)
└── Auth (authentication)
```

#### Benefits
- **Loose coupling**: Classes don't create their own dependencies
- **Testability**: Easy to mock dependencies for unit testing
- **Maintainability**: Centralized dependency management
- **Performance**: Singleton optimization for shared services
- **SOLID compliance**: Dependency inversion principle implementation

### Naming conventions
- **snake_case**: for functions, methods and variables
- **CamelCase**: for class names and controllers
- **Class prefixes**: `class_` for main classes
- **Controller prefixes**: `controller_` for API controllers
- **Middleware prefixes**: `middleware_` for middleware

### Security
- Protected `index.php` files in directories with data
- **Web directory protection** - `.htaccess` rules preventing direct file access
- **Backup security** - Multi-layer protection for database backup files:
  - Apache-level access denial via `.htaccess`
  - PHP-level directory protection via `index.php`
  - API-only authorized downloads with JWT tokens
  - Admin role requirement for all backup operations
  - Path traversal attack prevention
  - Filename validation and sanitization
- Content Security Policy configuration
- Roles and authentication system with JWT tokens
- Data sanitization and input validation
- CORS protection for API endpoints

### Code quality tools
- **Logging system**: Tracking errors and debugging

### Frontend technologies
- **Pico CSS**: Minimalistic CSS framework for fast prototyping
- **Alpine.js**: Lightweight JavaScript framework for interactivity
- **Modular architecture**: Division of JavaScript code by pages and components
- **CSP compatibility**: Support for Content Security Policy

This project is a modern CMS system built on the principles of pure PHP with a focus on performance, security and ease of maintenance. 