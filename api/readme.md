# Setting required for server software
Here are the necessary configurations for different server software.

## Apache

```
RewriteEngine On
RewriteBase /api/

# Ensure directory access and directory index resolve correctly
Require all granted
DirectoryIndex index.php
Options -MultiViews

# Allow access to index.php
<Files "index.php">
    Require all granted
    Satisfy Any
</Files>

# Always route versioned API prefixes (e.g., /v1, /v1/...) to index.php
RewriteRule ^v[0-9]+(/.*)?$ index.php [L,QSA]

# Route non-existing paths to index.php (handles nested routes like /api/v1/name)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L,QSA]
```

## nginx

```
# Always route versioned API prefixes (e.g., /api/v1, /api/v1/...)
location ~ ^/api/v[0-9]+(/.*)?$ {
    rewrite ^ /api/index.php?$query_string last;
}

# General API front controller
location ^~ /api/ {
    # Route non-existing files to index.php (handles /api/v1/name)
    try_files $uri /api/index.php?$args;
}

# PHP handler (adjust upstream to your environment)
#location = /api/index.php {
#    include fastcgi_params;
#    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
#    fastcgi_param QUERY_STRING    $query_string;
#    fastcgi_pass  php-fpm; # e.g., unix:/run/php/php8.1-fpm.sock or 127.0.0.1:9000
#}
```

## php build

```
php -S localhost:8000 api/index.php
```
