# RooCMS API Documentation

> Documents are temporarily in Russian language.   
> They will be translated into English later.

## Обзор
RooCMS предоставляет RESTful API для управления контентом и данными системы. API построен на чистом PHP без использования фреймворков с использованием собственного класса `ApiHandler`.

## Базовая информация
- **Базовый URL**: `https://yourdomain.com/api/`
- **Формат данных**: JSON
- **Аутентификация**: JWT Bearer токены
- **Версионирование**: Через URL path (`/v1/`)
- **Архитектура**: Собственная система роутинга RooCMS с Dependency Injection

## Версии API
- **v1**: Текущая dev версия (2.0.0 alpha)

## Основные принципы
- Все запросы должны использовать HTTPS
- Все ответы возвращаются в формате JSON
- HTTP статус коды используются для указания результата
- CORS включен для кросс-доменных запросов
- Поддержка динамических параметров в URL (`{id}`, `{param}`, `{slug}`)
- JWT аутентификация для защищенных эндпоинтов
- Роль-базированный контроль доступа (RBAC)
- Middleware система для обработки запросов
- Полная валидация и санитизация данных

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

#### ApiHandler (`/roocms/modules/class_apiHandler.php`)
Основной класс роутера для обработки HTTP запросов:
- Поддержка RESTful методов (GET, POST, PUT, DELETE, PATCH, OPTIONS)
- Динамические параметры в URL (`{id}`, `{param}`, `{slug}`)
- Продвинутая middleware система с поддержкой цепочек
- Централизованная обработка ошибок
- Dependency Injection контейнер

#### BaseController (`/api/v1/controller_base.php`)
Базовый класс для всех API контроллеров:
- Стандартизированные JSON ответы с единым форматом
- Полная валидация и санитизация данных
- Встроенная пагинация с настраиваемыми параметрами
- Интеграция с базой данных RooCMS
- Поддержка фильтрации и сортировки

#### HealthController (`/api/v1/controller_health.php`)
Контроллер для проверки состояния системы:
- Базовая проверка здоровья
- Детальная диагностика системы

#### Система аутентификации
**AuthController** - управление сессиями:
- JWT токены (access/refresh)
- Безопасная регистрация и авторизация
- Восстановление паролей
- Управление сессиями на всех устройствах

**AuthMiddleware** - проверка токенов:
- Валидация JWT токенов
- Извлечение данных пользователя
- Обработка истекших токенов

**RoleMiddleware** - контроль доступа:
- Проверка ролей пользователей (u, m, a, su)
- Методы: `admin_access`, `moderator_access`, `superuser_access`
- Блокировка неавторизованного доступа

### Добавление новых эндпоинтов

#### Создание нового контроллера

1. **Создайте файл контроллера** `/api/v1/controller_название.php`:
```php
<?php
class ExampleController extends BaseController 
{
    public function index(): void {
        $this->success(['message' => 'Hello API']);
    }
    
    public function show(int $id): void {
        $this->success(['id' => $id]);
    }
}
```

2. **Зарегистрируйте в autoloader** (`/api/router.php`):
```php
$controllers = [
    // ... существующие
    'ExampleController' => _API . '/v1/controller_example.php',
];
```

3. **Добавьте маршруты с middleware**:
```php
// Публичные
$api->get('/v1/example', 'ExampleController@index');

// С авторизацией
$api->post('/v1/example', 'ExampleController@store', ['AuthMiddleware']);

// Административные
$api->delete('/v1/example/{id}', 'ExampleController@delete', 
    ['AuthMiddleware', 'RoleMiddleware@admin_access']);
```

## Реализованные эндпоинты API

### 🔐 Аутентификация
- `POST /api/v1/auth/login` - Авторизация
- `POST /api/v1/auth/register` - Регистрация
- `POST /api/v1/auth/refresh` - Обновление токена
- `POST /api/v1/auth/logout` - Выход (с авторизацией)
- `POST /api/v1/auth/logout/all` - Выход со всех устройств
- `POST /api/v1/auth/refresh/revoke` - Отзыв refresh токена
- `POST /api/v1/auth/password/recovery` - Восстановление пароля
- `POST /api/v1/auth/password/reset` - Сброс пароля
- `PUT /api/v1/auth/password` - Изменение пароля (с авторизацией)

### 👥 Пользователи
- `GET /api/v1/users` - Список пользователей
- `GET /api/v1/users/me` - Текущий пользователь (с авторизацией)
- `GET /api/v1/users/{user_id}` - Пользователь по ID
- `PATCH /api/v1/users/me` - Обновление профиля
- `DELETE /api/v1/users/me` - Удаление аккаунта
- `PUT /api/v1/users/{user_id}` - Обновление пользователя (админ)
- `DELETE /api/v1/users/{user_id}` - Удаление пользователя (админ)
- `POST /api/v1/users/me/verify-email` - Запрос подтверждения email
- `GET /api/v1/users/verify-email/{verification_code}` - Подтверждение email

### 📁 Медиафайлы
- `GET /api/v1/media` - Список медиафайлов
- `GET /api/v1/media/{id}` - Информация о файле
- `GET /api/v1/media/{id}/file` - Скачивание файла
- `POST /api/v1/media/upload` - Загрузка файла (с авторизацией)
- `PUT /api/v1/media/{id}` - Обновление медиафайла
### 🏗️ Структура сайта
**Публичные:**
- `GET /api/v1/structure/tree` - Дерево структуры
- `GET /api/v1/structure/page/{id}` - Страница по ID  
- `GET /api/v1/structure/page/slug/{slug}` - Страница по slug
- `GET /api/v1/structure/navigation` - Навигационное меню
- `GET /api/v1/structure/breadcrumbs/{id}` - Хлебные крошки
- `GET /api/v1/structure/search` - Поиск по страницам

**Административные (требуют прав админа):**
- `GET /api/v1/admin/structure` - Управление структурой
- `POST /api/v1/admin/structure` - Создание страницы
- `PUT /api/v1/admin/structure/{id}` - Обновление страницы
- `DELETE /api/v1/admin/structure/{id}` - Удаление страницы

### ⚙️ Настройки системы (требуют прав админа)
- `GET /api/v1/admin/settings` - Все настройки
- `GET /api/v1/admin/settings/group-{group}` - Настройки группы
- `PUT /api/v1/admin/settings/key-{key}` - Обновление настройки
- `PATCH /api/v1/admin/settings` - Массовое обновление

### 💾 Резервные копии (требуют прав админа)
- `POST /api/v1/backup/create` - Создание бэкапа
- `POST /api/v1/backup/restore` - Восстановление
- `GET /api/v1/backup/list` - Список копий
- `DELETE /api/v1/backup/delete/{filename}` - Удаление копии
- `GET /api/v1/backup/download/{filename}` - Скачивание

### 🐛 Отладка и безопасность (требуют прав админа)
- `POST /api/v1/admin/debug/clear` - Очистка логов
- `POST /api/v1/csp-report` - Прием отчетов CSP

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
import json
from typing import Optional, Dict, Any

class RooCMSApiClient:
    def __init__(self, base_url: str):
        self.base_url = base_url.rstrip('/')
        self.session = requests.Session()
        self.access_token: Optional[str] = None
        self.refresh_token: Optional[str] = None
        
        # Отключаем проверку SSL для dev окружения
        self.session.verify = False
        requests.packages.urllib3.disable_warnings()
    
    def login(self, login: str, password: str) -> Dict[str, Any]:
        """Авторизация пользователя"""
        response = self._make_request('POST', '/v1/auth/login', {
            'login': login,
            'password': password
        })
        
        if response.get('success'):
            self.access_token = response['data']['access_token']
            self.refresh_token = response['data']['refresh_token']
            self.session.headers.update({
                'Authorization': f'Bearer {self.access_token}'
            })
        
        return response
    
    def _make_request(self, method: str, endpoint: str, data: Optional[Dict] = None, 
                     authenticated: bool = False) -> Dict[str, Any]:
        """Базовый метод для выполнения запросов"""
        url = f"{self.base_url}{endpoint}"
        headers = {'Content-Type': 'application/json'}
        
        if authenticated and self.access_token:
            headers['Authorization'] = f'Bearer {self.access_token}'
        
        try:
            response = self.session.request(
                method=method,
                url=url,
                headers=headers,
                json=data if data else None
            )
            
            # Если токен истек, пробуем обновить
            if response.status_code == 401 and authenticated and self.refresh_token:
                if self._refresh_access_token():
                    headers['Authorization'] = f'Bearer {self.access_token}'
                    response = self.session.request(
                        method=method,
                        url=url,
                        headers=headers,
                        json=data if data else None
                    )
            
            return response.json()
            
        except requests.exceptions.RequestException as e:
            return {'error': True, 'message': str(e)}
    
    def _refresh_access_token(self) -> bool:
        """Обновление access токена"""
        if not self.refresh_token:
            return False
        
        response = self._make_request('POST', '/v1/auth/refresh', {
            'refresh_token': self.refresh_token
        })
        
        if response.get('success'):
            self.access_token = response['data']['access_token']
            return True
        
        self.access_token = None
        self.refresh_token = None
        return False
    
    # Публичные методы API
    def get_health(self) -> Dict[str, Any]:
        """Проверка здоровья API"""
        return self._make_request('GET', '/v1/health')
    
    def get_health_details(self) -> Dict[str, Any]:
        """Детальная информация о системе"""
        return self._make_request('GET', '/v1/health/details')
    
    def get_current_user(self) -> Dict[str, Any]:
        """Получить текущего пользователя"""
        return self._make_request('GET', '/v1/users/me', authenticated=True)
    
    def get_users(self, **filters) -> Dict[str, Any]:
        """Получить список пользователей"""
        query_string = '&'.join([f"{k}={v}" for k, v in filters.items()])
        endpoint = f"/v1/users{'?' + query_string if query_string else ''}"
        return self._make_request('GET', endpoint)
    
    def get_structure_tree(self) -> Dict[str, Any]:
        """Получить дерево структуры сайта"""
        return self._make_request('GET', '/v1/structure/tree')
    
    def update_user_profile(self, data: Dict[str, Any]) -> Dict[str, Any]:
        """Обновить профиль пользователя"""
        return self._make_request('PATCH', '/v1/users/me', data, authenticated=True)
    
    def create_backup(self, **options) -> Dict[str, Any]:
        """Создать резервную копию (только для админов)"""
        return self._make_request('POST', '/v1/backup/create', options, authenticated=True)
    
    def get_backup_list(self, **filters) -> Dict[str, Any]:
        """Получить список резервных копий"""
        query_string = '&'.join([f"{k}={v}" for k, v in filters.items()])
        endpoint = f"/v1/backup/list{'?' + query_string if query_string else ''}"
        return self._make_request('GET', endpoint, authenticated=True)

# Пример использования
if __name__ == "__main__":
    api = RooCMSApiClient('https://dev.roocms.com/api')
    
    # Проверка здоровья API
    health = api.get_health()
    if health.get('success') and health['data']['status'] == 'healthy':
        print("API работает нормально")
        print(f"Время ответа: {health['data']['checks']['api']['response_time']}s")
        print(f"Версия RooCMS: {health['data']['checks'].get('roocms_version', 'N/A')}")
    else:
        print("Проблемы с API")
    
    # Получить дерево структуры сайта
    structure = api.get_structure_tree()
    if structure.get('success'):
        print(f"Найдено страниц: {len(structure['data'])}")
    
    # Авторизация (если нужно)
    # login_result = api.login('username', 'password')
    # if login_result.get('success'):
    #     print("Успешная авторизация")
    #     
    #     # Получить текущего пользователя
    #     user = api.get_current_user()
    #     print(f"Текущий пользователь: {user['data']['login']}")
```

### curl (командная строка)
```bash
# Проверка здоровья API
curl -X GET "https://dev.roocms.com/api/v1/health" -k -s | jq

# Детальная информация
curl -X GET "https://dev.roocms.com/api/v1/health/details" -k -s | jq

# Получить информацию о доступных эндпоинтах
curl -X GET "https://dev.roocms.com/api/" -k -s | jq '.endpoints'

# Получить дерево структуры сайта
curl -X GET "https://dev.roocms.com/api/v1/structure/tree" -k -s | jq

# Получить список пользователей с фильтрацией
curl -X GET "https://dev.roocms.com/api/v1/users?limit=5&role=a" -k -s | jq

# Авторизация пользователя
curl -X POST "https://dev.roocms.com/api/v1/auth/login" -k -s \
  -H "Content-Type: application/json" \
  -d '{"login":"admin","password":"password"}' | jq

# Сохранить токен в переменную (после успешной авторизации)
TOKEN=$(curl -X POST "https://dev.roocms.com/api/v1/auth/login" -k -s \
  -H "Content-Type: application/json" \
  -d '{"login":"admin","password":"password"}' | \
  jq -r '.data.access_token')

# Запрос с авторизацией - получить текущего пользователя
curl -X GET "https://dev.roocms.com/api/v1/users/me" -k -s \
  -H "Authorization: Bearer $TOKEN" | jq

# Обновить профиль пользователя
curl -X PATCH "https://dev.roocms.com/api/v1/users/me" -k -s \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"first_name":"Иван","last_name":"Петров"}' | jq

# Загрузить файл (multipart/form-data)
curl -X POST "https://dev.roocms.com/api/v1/media/upload" -k -s \
  -H "Authorization: Bearer $TOKEN" \
  -F "file=@/path/to/your/file.jpg" | jq

# Создать резервную копию (только для админов)
curl -X POST "https://dev.roocms.com/api/v1/backup/create" -k -s \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"compression":"gzip","include_data":true}' | jq

# Получить список резервных копий
curl -X GET "https://dev.roocms.com/api/v1/backup/list" -k -s \
  -H "Authorization: Bearer $TOKEN" | jq

# Получить системные настройки (только для админов)
curl -X GET "https://dev.roocms.com/api/v1/admin/settings" -k -s \
  -H "Authorization: Bearer $TOKEN" | jq

# Обновить настройки сайта
curl -X PATCH "https://dev.roocms.com/api/v1/admin/settings" -k -s \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"site_name":"Новое название","site_domain":"newdomain.com"}' | jq

# Тест несуществующего эндпоинта (404)
curl -X GET "https://dev.roocms.com/api/v1/nonexistent" -k -s | jq

# Тест неправильного метода (405)
curl -X POST "https://dev.roocms.com/api/v1/health" -k -s | jq

# Выход из системы
curl -X POST "https://dev.roocms.com/api/v1/auth/logout" -k -s \
  -H "Authorization: Bearer $TOKEN" | jq
```

## Аутентификация и авторизация

### JWT токены
RooCMS API использует JSON Web Tokens для аутентификации:

- **Access Token**: Краткосрочный токен (15-60 минут) для доступа к защищенным ресурсам
- **Refresh Token**: Долгосрочный токен (7-30 дней) для обновления access токенов

### Заголовки аутентификации
```http
Authorization: Bearer {access_token}
```

### Роли пользователей
- **u** (user) - Обычный пользователь
- **m** (moderator) - Модератор
- **a** (admin) - Администратор  
- **su** (superuser) - Суперпользователь

### Middleware доступа
- `AuthMiddleware` - Проверка токена
- `RoleMiddleware@moderator_access` - Доступ для модераторов и выше
- `RoleMiddleware@admin_access` - Доступ для администраторов и выше
- `RoleMiddleware@superuser_access` - Доступ только для суперпользователей

## Обработка ошибок и статус коды

### HTTP статус коды
- `200` - Успешный запрос
- `201` - Ресурс создан
- `400` - Неверный запрос (ошибка в данных)
- `401` - Требуется аутентификация
- `403` - Доступ запрещен (недостаточно прав)
- `404` - Ресурс не найден
- `405` - Метод не разрешен
- `422` - Ошибка валидации
- `429` - Превышен лимит запросов
- `500` - Внутренняя ошибка сервера

### Примеры ответов с ошибками

#### 401 Unauthorized
```json
{
  "error": true,
  "message": "Authentication required",
  "status_code": 401,
  "timestamp": "2025-10-10 12:00:00"
}
```

#### 403 Forbidden
```json
{
  "error": true,
  "message": "Admin role required",
  "status_code": 403,
  "timestamp": "2025-10-10 12:00:00"
}
```

#### 404 Not Found
```json
{
  "error": true,
  "message": "User not found",
  "status_code": 404,
  "timestamp": "2025-10-10 12:00:00"
}
```

#### 422 Validation Error
```json
{
  "error": true,
  "message": "Validation failed",
  "status_code": 422,
  "timestamp": "2025-10-10 12:00:00",
  "validation_errors": {
    "email": ["Email field is required"],
    "password": ["Password must be at least 8 characters"]
  }
}
```

#### 405 Method Not Allowed
```json
{
  "error": true,
  "message": "Method not allowed",
  "status_code": 405,
  "allowed_methods": ["GET", "POST"],
  "timestamp": "2025-10-10 12:00:00"
}
```

#### 500 Internal Server Error
```json
{
  "error": true,
  "message": "Internal server error",
  "status_code": 500,
  "timestamp": "2025-10-10 12:00:00"
}
```

### Системные требования
- **PHP**: 8.1+ (совместимость с PHP 8.4)
- **База данных**: MySQL 5.7+ / MariaDB 10.3+ (тестировано на MariaDB 11.7.2)
- **Веб-сервер**: Apache 2.4+ / nginx 1.18+
- **Расширения PHP**: PDO, JSON, mbstring, openssl, fileinfo, gd, zip
- **Память PHP**: минимум 128MB, рекомендуется 256MB+

### Производительность
- **Время ответа**: 8-15ms (health check), 15-22ms (сложные запросы)
- **Память**: ~2-3MB peak usage в зависимости от операции
- **Запросы к БД**: 1-10 на запрос в зависимости от сложности
- **Throughput**: 5000+ запросов/секунду на стандартном сервере

### Структура проекта
```
api/
├── index.php                           # Точка входа API
├── router.php                          # Конфигурация маршрутов и DI
├── v1/                                 # API версии 1
│   ├── controller_base.php             # Базовый контроллер
│   ├── controller_auth.php             # Аутентификация
│   ├── controller_users.php            # Пользователи
│   ├── controller_media.php            # Медиафайлы
│   ├── controller_structure.php        # Публичная структура
│   ├── controller_adminStructure.php   # Админ структура
│   ├── controller_adminSettings.php    # Системные настройки
│   ├── controller_backup.php           # Резервные копии
│   ├── controller_health.php           # Мониторинг
│   ├── controller_csp.php              # Безопасность
│   ├── controller_debug.php            # Отладка
│   ├── middleware_auth.php             # JWT аутентификация
│   ├── middleware_role.php             # Контроль доступа
│   └── docs/                           # Документация
└── README.md                           # Эта документация

roocms/modules/
├── class_apiHandler.php                # Роутер и dispatcher
├── class_auth.php                      # Система аутентификации
├── class_user.php                      # Управление пользователями
├── class_structure.php                 # CMS структура
├── class_siteSettings.php              # Настройки системы
├── class_files.php                     # Файловая система
└── di/                                 # Dependency Injection
```

### Настройка веб-сервера

#### Apache (.htaccess)
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

# CORS headers
Header always set Access-Control-Allow-Origin "*"
Header always set Access-Control-Allow-Methods "GET, POST, PUT, PATCH, DELETE, OPTIONS"
Header always set Access-Control-Allow-Headers "Content-Type, Authorization"

# Handle preflight OPTIONS requests
RewriteCond %{REQUEST_METHOD} OPTIONS
RewriteRule ^(.*)$ index.php [L]

# Always route versioned API prefixes (e.g., /v1, /v1/...) to index.php
RewriteRule ^v[0-9]+(/.*)?$ index.php [L,QSA]

# Route non-existing paths to index.php (handles nested routes like /api/v1/name)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L,QSA]
```

#### nginx (example)
```nginx
server {
    # CORS headers
    add_header Access-Control-Allow-Origin "*" always;
    add_header Access-Control-Allow-Methods "GET, POST, PUT, PATCH, DELETE, OPTIONS" always;
    add_header Access-Control-Allow-Headers "Content-Type, Authorization" always;
    
    # Handle preflight OPTIONS requests
    if ($request_method = 'OPTIONS') {
        return 200;
    }
    
    # API routes
    location ^~ /api/ {
        # Always route versioned API prefixes (e.g., /api/v1, /api/v1/...)
        location ~ ^/api/v[0-9]+(/.*)?$ {
            rewrite ^ /api/index.php?$query_string last;
        }
        
        # General API front controller
        try_files $uri /api/index.php?$args;
    }
    
    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Логирование и отладка

#### Настройки логирования
- Логи API записываются в `SYSERRLOG` при `DEBUGMODE = true`
- Ошибки обрабатываются централизованно через `ApiHandler`
- JWT токены логируются при отладке (без sensitive данных)
- Валидационные ошибки записываются с деталями

#### Отладочная информация
```php
// В debug режиме (ROOCMS_BUILD_VERSION === 'alpha') возвращается
{
  "error": true,
  "message": "Internal server error",
  "status_code": 500,
  "timestamp": "2025-10-10 12:00:00",
  "debug": {
    "exception": "Database connection failed",
    "file": "/path/to/file.php",
    "line": 123,
    "trace": [...]
  }
}
```

### Безопасность

#### Основные меры
- **HTTPS only**: Все запросы должны использовать HTTPS
- **JWT токены**: Безопасная аутентификация с истечением
- **RBAC**: Детализированный контроль доступа по ролям
- **CSP**: Content Security Policy monitoring
- **Input validation**: Полная валидация и санитизация
- **Rate limiting**: Защита от злоупотреблений (планируется)
- **CORS**: Настраиваемая политика cross-origin запросов

#### Рекомендации по безопасности
1. Используйте HTTPS для всех API запросов
2. Регулярно ротируйте JWT secret ключи
3. Настройте firewall для ограничения доступа к админ эндпоинтам
4. Мониторьте CSP нарушения через `/v1/csp-report`
5. Используйте strong passwords для admin аккаунтов
6. Регулярно обновляйте RooCMS до последней версии

---

**Версия документации**: 2.1  
**Последнее обновление**: 2025-10-10  
**Статус API**: Стабильная версия v1 (2.0.0 alpha)  
**Поддержка**: [GitHub Issues](https://github.com/roocms/roocms) | [Сайт проекта](https://www.roocms.com)
