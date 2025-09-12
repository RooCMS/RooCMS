# RooCMS API Documentation

> Documents are temporarily in Russian language.   
> They will be translated into English later.

## Обзор
RooCMS предоставляет RESTful API для управления контентом и данными системы. API построен на чистом PHP без использования фреймворков с использованием собственного класса `ApiHandler`.

## Base information
- **Базовый URL**: `https://dev.roocms.com/api/`
- **Формат данных**: JSON
- **Аутентификация**: Пока не требуется (в разработке)
- **Версионирование**: Через URL path (`/v1/`)
- **Архитектура**: Собственная система роутинга RooCMS

## API version
- **v1**: Текущая рабочая версия

## Our rules
- Все запросы должны использовать HTTPS
- Все ответы возвращаются в формате JSON
- HTTP статус коды используются для указания результата
- CORS включен для кросс-доменных запросов
- Поддержка динамических параметров в URL (`{id}`, `{param}`)

## Стандартный формат ответа

### Успешный ответ:
```json
{
  "success": true,
  "timestamp": "2024-01-15 12:00:00",
  "data": {...}
}
```

### Error:
```json
{
  "error": true,
  "message": "Error description",
  "status_code": 400,
  "timestamp": "2024-01-15 12:00:00"
}
```

## Реализованные эндпоинты API

### Корневой эндпоинт
Информация о доступных эндпоинтах API.

**GET** `/api/`

**Ответ:**
```json
{
  "success": true,
  "message": "RooCMS API v1",
  "timestamp": "2025-09-01 20:34:31",
  "version": "2.0.0 alpha",
  "endpoints": {
    "health": "/api/v1/health",
    "health_details": "/api/v1/health/details"
  }
}
```

### Health Check
Проверка здоровья системы и подключений.

**GET** `/api/v1/health`

**Ответ:**
```json
{
  "success": true,
  "timestamp": "2025-09-01 20:34:37",
  "data": {
    "status": "healthy",
    "checks": {
      "api": {
        "status": "ok",
        "message": "API is responding normally",
        "response_time": 0.0081
      },
      "database": {
        "status": "healthy",
        "connection_alive": true,
        "database_info": {
          "driver": "mysql",
          "version": "11.7.2-MariaDB"
        }
      }
    },
    "system_info": {
      "timestamp": "2025-09-01 20:34:37",
      "timezone": "UTC"
    }
  }
}
```

### Детальная диагностика
Подробная информация о состоянии системы.

**GET** `/api/v1/health/details`

**Ответ:**
```json
{
  "success": true,
  "timestamp": "2025-09-01 20:34:45",
  "data": {
    "api check": {
      "status": "ok",
      "message": "API is responding normally",
      "response_time": 0.0142
    },
    "system_info": {
      "timestamp": "2025-09-01 20:34:45",
      "timezone": "UTC",
      "memory_usage": {
        "current": 2097152,
        "peak": 2097152,
        "limit": "1024M"
      }
    },
    "php_info": {
      "configuration": {
        "memory_limit": "1024M",
        "max_execution_time": "30",
        "post_max_size": "64M",
        "upload_max_filesize": "64M"
      }
    },
    "roocms_info": {
      "version": "2.0.0 alpha",
      "major_version": "2",
      "minor_version": "0",
      "build": "alpha"
    }
  }
}
```

### Обработка ошибок

#### 404 Not Found
```json
{
  "error": true,
  "message": "Endpoint not found",
  "status_code": 404,
  "timestamp": "2025-09-01 20:35:04"
}
```

#### 405 Method Not Allowed
```json
{
  "error": true,
  "message": "Method not allowed",
  "status_code": 405,
  "allowed_methods": ["GET"],
  "timestamp": "2025-09-01 20:35:09"
}
```

#### 500 Internal Server Error
```json
{
  "error": true,
  "message": "Internal server error",
  "status_code": 500,
  "timestamp": "2025-09-01 20:35:15"
}
```

## Архитектура API

### Основные компоненты

#### ApiHandler (`/roocms/class/class_apiHandler.php`)
Основной класс роутера для обработки HTTP запросов:
- Поддержка RESTful методов (GET, POST, PUT, DELETE, PATCH)
- Динамические параметры в URL (`{id}`, `{param}`)
- Middleware система
- Обработка ошибок

#### BaseController (`/api/v1/controller_base.php`)
Базовый класс для всех API контроллеров:
- Стандартизированные JSON ответы
- Валидация и санитизация данных
- Пагинация
- Интеграция с базой данных RooCMS

#### HealthController (`/api/v1/controller_health.php`)
Контроллер для проверки состояния системы:
- Базовая проверка здоровья
- Детальная диагностика системы

### Добавление новых эндпоинтов

Для добавления нового контроллера:

1. Создайте файл `/api/v1/controller_название.php`
2. Наследуйте от `BaseController`
3. Добавьте маршруты в `/api/router.php`

**Пример:**
```php
// В router.php
require_once _API . '/v1/controller_users.php';
$api->get('/v1/users', 'UsersController@index');
$api->post('/v1/users', 'UsersController@store');
$api->get('/v1/users/{id}', 'UsersController@show');
```

## Планируемые эндпоинты

### Пользователи (Users) - В разработке
- `GET /api/v1/users` - Список пользователей
- `GET /api/v1/users/{id}` - Получить пользователя
- `POST /api/v1/users` - Создать пользователя
- `PUT /api/v1/users/{id}` - Обновить пользователя
- `DELETE /api/v1/users/{id}` - Удалить пользователя

### Контент (Posts) - В разработке
- `GET /api/v1/posts` - Список постов
- `GET /api/v1/posts/{id}` - Получить пост
- `POST /api/v1/posts` - Создать пост
- `PUT /api/v1/posts/{id}` - Обновить пост
- `DELETE /api/v1/posts/{id}` - Удалить пост

## HTTP Статус коды

- `200` - Успешный запрос
- `201` - Ресурс создан
- `400` - Неверный запрос (ошибка в данных)
- `404` - Ресурс не найден
- `405` - Метод не разрешен
- `500` - Внутренняя ошибка сервера

## Примеры использования

### JavaScript (fetch)
```javascript
// Проверка здоровья API
fetch('https://dev.roocms.com/api/v1/health')
  .then(response => response.json())
  .then(data => console.log(data));

// Получить детальную информацию
fetch('https://dev.roocms.com/api/v1/health/details')
  .then(response => response.json())
  .then(data => console.log(data));

// Получить информацию о API
fetch('https://dev.roocms.com/api/')
  .then(response => response.json())
  .then(data => console.log(data));
```

### PHP (curl)
```php
// Проверка здоровья API
$ch = curl_init('https://dev.roocms.com/api/v1/health');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

if ($data['success'] && $data['data']['status'] === 'healthy') {
    echo "API работает нормально";
} else {
    echo "Проблемы с API";
}
```

### Python (requests)
```python
import requests

# Проверка здоровья API
response = requests.get('https://dev.roocms.com/api/v1/health', verify=False)
data = response.json()

if data['success'] and data['data']['status'] == 'healthy':
    print("API работает нормально")
    print(f"Время ответа: {data['data']['checks']['api']['response_time']}s")
else:
    print("Проблемы с API")

# Детальная диагностика
details = requests.get('https://dev.roocms.com/api/v1/health/details', verify=False)
print(f"Версия RooCMS: {details.json()['data']['roocms_info']['version']}")
```

### curl (командная строка)
```bash
# Базовая проверка
curl -X GET https://dev.roocms.com/api/v1/health -k -s | jq

# Детальная информация
curl -X GET https://dev.roocms.com/api/v1/health/details -k -s | jq

# Тест несуществующего эндпоинта (404)
curl -X GET https://dev.roocms.com/api/v1/nonexistent -k -s | jq
```

---

## Техническая информация

### Системные требования
- **PHP**: 8.1+ (тестировано на 8.4.1)
- **База данных**: MySQL/MariaDB (тестировано на MariaDB 11.7.2)
- **Веб-сервер**: Apache 2.4+ / nginx
- **Расширения PHP**: PDO, JSON, mbstring, openssl

### Производительность
- **Время ответа**: 8-15ms (health check)
- **Память**: ~2MB peak usage
- **Запросы к БД**: 2-4 на health check

### Структура проекта
```
api/
├── index.php                    # Точка входа
├── router.php                   # Конфигурация маршрутов  
├── v1/
│   ├── controller_base.php      # Базовый контроллер
│   └── controller_health.php    # Health контроллер
└── README.md                    # Эта документация

roocms/class/
└── class_apiHandler.php         # Система роутинга
```

### Настройка веб-сервера

#### Apache
```apache
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

#### nginx
```nginx
# Always route versioned API prefixes (e.g., /api/v1, /api/v1/...)
location ~ ^/api/v[0-9]+(/.*)?$ {
    rewrite ^ /api/index.php?$query_string last;
}

# General API front controller
location ^~ /api/ {
    # Route non-existing files to index.php (handles /api/v1/name)
    try_files $uri /api/index.php?$args;
}
```

### Loggin and Debug
- Логи запросов записываются в `SYSERRLOG` при `DEBUGMODE = true`
- Ошибки обрабатываются централизованно через `ApiHandler`

---

**Doc Version**: 1.0  
**Last Update**: 2025-09-12  
**API Status**: Рабочая версия v1
