# Setting required for server software
Here are the necessary configurations for different server software.

## Apache

```
RewriteEngine On
RewriteBase /api/

# Если файл или папка существует, отдаем его
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Иначе всё идёт в index.php
RewriteRule ^ index.php [QSA,L]

```

## ngnix

```
location /api {
    try_files $uri /api/index.php;
}
```

## php build

```
php -S localhost:8000 api/index.php
```
