# Setting required for server software
Here are the necessary configurations for different server software.

## Apache

```
RewriteEngine On
RewriteBase /api/

# Ensure directory access and directory index resolve correctly
Require all granted
DirectoryIndex index.php

# Block access to all files except index.php
<FilesMatch "^(?!index\.php$).+">
    Require all denied
</FilesMatch>

# Allow access only to index.php
<Files "index.php">
    Require all granted
</Files>

# All requests are redirected to index.php
RewriteRule ^ index.php [QSA,L]
```

## nginx

```
location /api {
    # Block access to all files except index.php
    location ~ ^/api/(?!index\.php$) {
        deny all;
        return 403;
    }
    
    # All requests are redirected to index.php
    try_files $uri /api/index.php$is_args$args;
}
```

## php build

```
php -S localhost:8000 api/index.php
```
