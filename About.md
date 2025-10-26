# О RooCMS

[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net/)
![Database](https://img.shields.io/badge/DB-MySQL%20%7C%20PostgreSQL%20%7C%20Firebird-orange)

---

## 📖 Что такое RooCMS?

**RooCMS** — это современная, открытая и свободная система управления контентом (CMS), разработанная на чистом PHP без использования внешних фреймворков. Система спроектирована с фокусом на производительность, безопасность и гибкость, предоставляя мощный API-first подход для создания не только веб-сайтов, но и любых приложений.

### 🎯 Ключевая философия

RooCMS следует принципу **"framework-free"** — никаких внешних зависимостей, никаких ORM, только чистый, оптимизированный PHP код. Это обеспечивает:

- ⚡ **Максимальную производительность** — прямые SQL запросы вместо тяжеловесных абстракций
- 🔒 **Полный контроль** — вы всегда знаете, что происходит в вашей системе
- 🚀 **Быструю разработку** — минимум overhead, максимум эффективности
- 🎓 **Простоту изучения** — понятный код без магии фреймворков

---

## 🌟 Основные возможности

### 🔥 Архитектурные преимущества

- **PHP 8.1+** — использование современных возможностей языка (union types, named arguments, attributes)
- **API-First подход** — полнофункциональный RESTful API для интеграции с любыми приложениями
- **Без фреймворков** — чистый PHP для максимальной производительности
- **Строгая типизация** — type safety на уровне PHP 8+ для надёжности кода
- **SOLID принципы** — чистая архитектура с dependency injection контейнером
- **Модульная структура** — trait-based архитектура для расширяемости

### 🗄️ Работа с базами данных

- **Мультибазовая поддержка** — MySQL, MariaDB, PostgreSQL, Firebird
- **PDO parameter binding** — защита от SQL-инъекций на уровне драйвера
- **Система миграций** — автоматическое версионирование схемы БД с CLI
- **Database Health Monitoring** — мониторинг состояния соединения в реальном времени
- **Backup система** — полнофункциональная система резервного копирования и восстановления
- **Оптимизация запросов** — прямые SQL запросы вместо query builder

### 🌐 RESTful API

- **Полнофункциональный REST API** — версионирование через URL (`/api/v1/`)
- **14 API контроллеров** — Health, Auth, Users, Settings, Backup, Media, Structure, Moderate, Debug, CSP и др.
- **JWT аутентификация** — безопасные access/refresh токены
- **Middleware система** — цепочки обработки запросов (auth, roles, validation)
- **Динамические маршруты** — параметры в URL (`{id}`, `{slug}`, `{param}`)
- **RBAC контроль доступа** — роли пользователей (user, moderator, admin, superuser)
- **Стандартизированные ответы** — единый JSON формат с обработкой ошибок
- **Health Check** — мониторинг состояния API и системы
- **CORS поддержка** — кросс-доменные запросы

### 🔐 Безопасность

- **JWT токены** — криптографически безопасная аутентификация
- **CSP (Content Security Policy)** — защита от XSS атак
- **RBAC** — детализированный контроль доступа по ролям
- **Input validation** — полная валидация и санитизация входных данных
- **Password hashing** — современные алгоритмы хеширования паролей
- **Multi-layer backup protection** — многоуровневая защита резервных копий
- **Path traversal protection** — защита от атак обхода каталогов

### 📁 Файловая система

- **Trait-based архитектура** — модульная обработка файлов по типам
- **Мультиформатная поддержка** — изображения, документы, видео, аудио, архивы
- **Автоматическая обработка** — извлечение метаданных, генерация превью
- **Варианты изображений** — множественные размеры (thumb, small, medium, large)
- **GD интеграция** — продвинутая обработка изображений (resize, crop, watermark)
- **Database tracking** — полное отслеживание файлов в БД

### 🎨 Система тем

- **Модульная архитектура** — поддержка множественных тем
- **Два типа рендеринга** — PHP и HTML шаблонизаторы
- **Tailwind CSS 4.x** — utility-first CSS фреймворк
- **Alpine.js** — легковесная интерактивность (~15KB)
- **CSP совместимость** — безопасные inline скрипты
- **Responsive design** — адаптивность из коробки

### 💾 Резервное копирование

- **CLI и API интерфейсы** — управление через командную строку или REST API
- **Полное сохранение структуры** — все объекты БД (ключи, индексы, constraints)
- **Универсальный формат** — кросс-базовый формат для миграций между СУБД
- **Gzip компрессия** — сжатие 9:1 для экономии места
- **Транзакционная безопасность** — rollback поддержка
- **Batch processing** — эффективная обработка больших объёмов данных

---

## 🚀 Технические характеристики

### Системные требования

**Минимальные:**
- **PHP**: 8.1 или выше
- **Веб-сервер**: Apache 2.4+ / nginx 1.18+
- **База данных**: MySQL 5.7+ / MariaDB 10.10+ / PostgreSQL 14+ / Firebird
- **Память**: 128MB RAM (рекомендуется 256MB+)
- **PHP расширения**: PDO, JSON, mbstring, openssl, curl, gd, fileinfo, zip, exif

**Рекомендуемые:**
- **PHP**: 8.4
- **Веб-сервер**: Apache 2.4
- **База данных**: MariaDB 11.7
- **Память**: 1GB+ RAM

### Производительность

- ⚡ **Время отклика**: 8-15ms (health check), 15-22ms (сложные запросы)
- 💾 **Память**: ~2-3MB peak usage на запрос
- 🔄 **Throughput**: 1000+ RPS на стандартном сервере
- 📊 **Запросы к БД**: оптимизированы для минимального количества

### Совместимость

- ✅ PHP 8.1, 8.2, 8.3, 8.4
- ✅ MySQL 5.7+, 8.0+
- ✅ MariaDB 10.10+, 11.x
- ✅ PostgreSQL 14+, 15+, 16+
- ✅ Firebird 3.0+
- ✅ Apache 2.4, nginx

---

## 🏗️ Архитектура

### Принципы разработки

1. **Framework-free** — без внешних фреймворков и зависимостей
2. **ORM-free** — прямые SQL запросы через PDO для производительности
3. **API-first** — REST API как основной интерфейс
4. **SOLID** — чистая архитектура с dependency injection
5. **Security-first** — безопасность на каждом уровне
6. **Performance-first** — оптимизация производительности в приоритете

### Структура проекта

```
yourdomain.com/
├── api/                    # RESTful API интерфейс
│   ├── v1/                 # API версия 1
│   ├── index.php           # Точка входа
│   └── router.php          # Конфигурация маршрутов
├── roocms/                 # Ядро системы
│   ├── config/             # Конфигурация
│   ├── database/           # Миграции и бэкапы
│   ├── helpers/            # Вспомогательные функции
│   ├── modules/            # Основные классы (23 класса + 16 трейтов + 4 интерфейса)
│   ├── services/           # Бизнес-логика (18 сервисов)
│   ├── backend.php         # Инициализация backend
│   ├── bootstrap.php       # Инициализация системы
│   └── frontend.php        # Инициализация фронтенда
├── themes/                 # Темы оформления
│   └── default/            # Стандартная тема
├── storage/                # Хранилище данных
│   ├── assets/             # Ресурсы
│   └── logs/               # Логи системы
├── up/                     # Загруженные файлы
└── index.php               # Главная точка входа
```

### Основные компоненты

#### Ядро системы
- **DependencyContainer** — IoC контейнер для управления зависимостями
- **ApiHandler** — роутер для обработки REST API запросов
- **Db** — PDO обёртка для работы с базами данных
- **Auth** — система аутентификации и авторизации
- **SiteSettings** — динамическая система настроек

#### Модули
- **Files** — файловая система с trait-based архитектурой
- **GD** — обработка изображений
- **Mailer** — отправка email
- **Structure** — управление структурой сайта
- **User/Role** — управление пользователями и ролями

#### Сервисы (бизнес-логика)
- **AuthenticationService** — аутентификация пользователей
- **RegistrationService** — регистрация новых пользователей
- **BackupService** — резервное копирование
- **FilesService** — управление файлами
- **EmailService** — отправка email
- **UserService, UserManageService, UserListService** — операции с пользователями
- **SiteSettingsService, SiteSettingsManageService** — управление настройками
- **StructureService, StructureManageService** — управление структурой сайта
- **ModerateService** — модерация контента
- **DebugService** — отладка и логирование
- **UserValidationService, UserRecoveryService** — валидация и восстановление пользователей

---

## 📦 Установка

### Быстрый старт

1. **Скачайте RooCMS**
   ```bash
   # Доступно на GitHub releases
   wget https://github.com/RooCMS/RooCMS/releases/latest
   ```

2. **Распакуйте в корень сайта**
   ```bash
   unzip roocms-2.0.0.zip -d /var/www/yourdomain.com/
   ```

3. **Настройте права доступа**
   ```bash
   chmod -R 755 /var/www/yourdomain.com/roocms/
   chmod -R 777 /var/www/yourdomain.com/storage/
   chmod -R 777 /var/www/yourdomain.com/up/
   ```

4. **Создайте базу данных**
   ```sql
   CREATE DATABASE roocms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'roocms_user'@'localhost' IDENTIFIED BY 'secure_password';
   GRANT ALL PRIVILEGES ON roocms_db.* TO 'roocms_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

5. **Выполните миграции**
   ```bash
   cd /var/www/yourdomain.com/
   php roocms/database/migrate_cli.php migrate
   ```

6. **Проверьте работу**
   ```bash
   # Откройте браузер
   https://yourdomain.com
   
   # Или проверьте API
   curl https://yourdomain.com/api/v1/health
   ```

---

## 🔧 Использование

### API примеры

#### JavaScript (fetch)
```javascript
// Проверка состояния системы
fetch('https://yourdomain.com/api/v1/health')
  .then(response => response.json())
  .then(data => {
    if (data.success && data.data.status === 'healthy') {
      console.log('RooCMS работает нормально');
    }
  });

// Авторизация пользователя
fetch('https://yourdomain.com/api/v1/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    login: 'username',
    password: 'password'
  })
})
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const token = data.data.access_token;
      // Сохраните токен для дальнейших запросов
    }
  });

// Запрос с авторизацией
fetch('https://yourdomain.com/api/v1/users/me', {
  headers: {
    'Authorization': `Bearer ${token}`
  }
})
  .then(response => response.json())
  .then(data => console.log(data));
```

#### PHP (curl)
```php
// Создание клиента API
$ch = curl_init('https://yourdomain.com/api/v1/health');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

if ($data['success']) {
    echo "API работает: " . $data['data']['status'];
}

// Авторизация
$ch = curl_init('https://yourdomain.com/api/v1/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'login' => 'username',
    'password' => 'password'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

if ($data['success']) {
    $token = $data['data']['access_token'];
}
```

### CLI инструменты

#### Миграции базы данных
```bash
# Выполнить все ожидающие миграции
php roocms/database/migrate_cli.php migrate

# Откатить последние 2 миграции
php roocms/database/migrate_cli.php rollback 2

# Посмотреть статус миграций
php roocms/database/migrate_cli.php status

# Узнать текущую версию
php roocms/database/migrate_cli.php version
```

#### Резервное копирование
```bash
# Создать резервную копию с компрессией
php roocms/database/backup_cli.php create --compress --universal

# Восстановить из резервной копии
php roocms/database/backup_cli.php restore --filename=backup_20241226_143022.sql.gz

# Список всех резервных копий
php roocms/database/backup_cli.php list

# Статус системы бэкапа
php roocms/database/backup_cli.php status

# Удалить резервную копию
php roocms/database/backup_cli.php delete --filename=old_backup.sql.gz
```

---

## 🎨 Создание тем

RooCMS поддерживает два типа шаблонизаторов:

### PHP темы (рекомендуется)

```php
// themes/mytheme/theme.json
{
  "name": "My Theme",
  "slug": "mytheme",
  "version": "1.0.0",
  "type": "php",
  "author": "Your Name"
}

// themes/mytheme/pages/index.php
<?php
echo $themes->render('layouts/base.php', [
    'title' => 'Главная страница',
    'content' => 'Hello World!'
]);
?>
```

### HTML темы

```html
<!-- themes/mytheme/pages/index.html -->
<!DOCTYPE html>
<html>
<head>
    <title>{{page_title}}</title>
    {{asset: css/style.css}}
</head>
<body>
    <!-- include: partials/header.html -->
    
    <main>
        {{content}}
    </main>
    
    <!-- include: partials/footer.html -->
    {{asset: js/app.js}}
</body>
</html>
```

---

## 🔌 Расширение функциональности

### Создание собственного API контроллера

```php
<?php
// api/v1/controller_myfeature.php

class MyFeatureController extends BaseController 
{
    public function index(): void 
    {
        // Получить все записи
        $items = $this->db->fetch_all('SELECT * FROM my_table');
        $this->success(['items' => $items]);
    }
    
    public function show(int $id): void 
    {
        // Получить запись по ID
        $item = $this->db->fetch_one(
            'SELECT * FROM my_table WHERE id = ?',
            [$id]
        );
        
        if (!$item) {
            $this->error('Запись не найдена', 404);
            return;
        }
        
        $this->success(['item' => $item]);
    }
    
    public function store(): void 
    {
        // Создать новую запись (требуется авторизация)
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $id = $this->db->query(
            'INSERT INTO my_table (name, description) VALUES (?, ?)',
            [$data['name'], $data['description']]
        );
        
        $this->success(['id' => $id], 201);
    }
}
```

### Регистрация маршрутов

```php
// api/router.php

// Публичные маршруты
$api->get('/v1/myfeature', 'MyFeatureController@index');
$api->get('/v1/myfeature/{id}', 'MyFeatureController@show');

// Защищённые маршруты (требуют авторизации)
$api->post('/v1/myfeature', 'MyFeatureController@store', ['AuthMiddleware']);

// Административные маршруты
$api->delete('/v1/myfeature/{id}', 'MyFeatureController@delete', 
    ['AuthMiddleware', 'RoleMiddleware@admin_access']);
```

---

## 📚 Документация

- 📖 [Основная документация](README.md)
- 🏗️ [Структура проекта](structure.md)
- 🔌 [API документация](api/README.md)
- 🗄️ [Система миграций](roocms/database/README_Migrate.md)
- 💾 [Система бэкапов](roocms/database/README_Backup.md)
- 📊 [Swagger API схема](api/v1/docs/swagger.yaml)
- 📮 [Postman коллекция](api/v1/docs/postman.json)

---

## 🤝 Сообщество и поддержка

### Полезные ссылки

- 🌐 **Официальный сайт**: [https://roocms.com](https://roocms.com)
- 📦 **GitHub репозиторий**: [https://github.com/RooCMS/RooCMS](https://github.com/RooCMS/RooCMS)
- 📥 **Релизы**: [https://github.com/RooCMS/RooCMS/releases](https://github.com/RooCMS/RooCMS/releases)
- 📧 **Email поддержка**: info@roocms.com

### Участие в разработке

Мы приветствуем ваше участие в развитии RooCMS:

1. **Сообщения об ошибках** — создайте issue с подробным описанием
2. **Pull requests** — небольшие, сфокусированные изменения с тестами
3. **Следуйте правилам**:
   - PHP 8.1+ возможности
   - Без фреймворков и ORM
   - Строгая типизация
   - PSR-12 стандарт кода
   - Подробные комментарии

### Сообщество

- 💬 Обсуждения на GitHub
- 🐛 Issue tracker для багов
- 💡 Feature requests приветствуются
- 📖 Wiki с дополнительной документацией

---

## 🔒 Безопасность

### Политика безопасности

Если вы обнаружили уязвимость безопасности, пожалуйста, сообщите об этом ответственно:

- 📧 **Email**: info@roocms.com
- 🔐 **Не публикуйте** уязвимости в публичных issue
- ⏱️ **Ожидайте ответа** в течение 48 часов

### Меры безопасности

- ✅ HTTPS only для всех запросов
- ✅ JWT токены с истечением
- ✅ PDO prepared statements
- ✅ CSP заголовки
- ✅ Input validation и sanitization
- ✅ RBAC контроль доступа
- ✅ Rate limiting (планируется)

---

## ⚖️ Лицензия

RooCMS распространяется под лицензией **GNU General Public License v3.0**.

**Это означает:**

- ✅ Свободное использование в коммерческих и некоммерческих проектах
- ✅ Право модификации исходного кода
- ✅ Право распространения оригинальной и модифицированной версий
- ❗ Обязательство предоставлять исходный код при распространении
- ❗ Сохранение той же лицензии для производных работ

**Подробнее**: [LICENSE.md](LICENSE.md) | [GNU GPL v3](https://www.gnu.org/licenses/gpl-3.0.html)

---

## 📈 Статистика проекта

### Код

- **Размер кода**: ~762KB PHP кода (без зависимостей)
- **Классы**: 23 основных класса
- **Трейты**: 16 trait компонентов
- **Интерфейсы**: 4 интерфейса
- **API контроллеры**: 14 контроллеров
- **Сервисы**: 18 бизнес-сервисов

### История

- **Первая версия**: 2010
- **Текущая версия**: 2.0.0 alpha
- **Автор**: alex Roosso (alexandr Belov)
- **Годы разработки**: 15+ лет
- **Открытый исходный код**: с 2010 года

---

## 🎯 Для кого RooCMS?

### ✅ Идеально подходит для:

- **Разработчиков PHP**, которые ценят контроль и производительность
- **API-first проектов**, где нужен мощный бэкенд без лишнего веса
- **Высоконагруженных систем**, где критична производительность
- **Образовательных целей**, чтобы понять как работает CMS изнутри
- **Корпоративных решений**, где нужна полная настройка и безопасность

### ⚠️ Возможно не подходит для:

- Начинающих без опыта работы с PHP
- Проектов, требующих готовые решения "из коробки" без настройки
- Тех, кто предпочитает использовать Laravel/Symfony экосистемы

---

## 🚀 Дорожная карта (Roadmap)

### Версия 2.0 stable (планируется)

-  Полное удаление legacy кода
-  Расширенная система пользователей (профили, группы, активность)
-  Система контента (посты, категории, теги)


### Версия 2.1 (планируется)

-  Файловый менеджер с графическим интерфейсом
-  Кэширование (Redis/Memcached)
-  Rate limiting для API
-  Мультиязычность
-  Плагины система
-  WebSocket поддержка
-  Docker контейнеризация

### Будущие возможности

-  Официальные плагины (blog, shop, forum)


---

## 🙏 Благодарности

RooCMS существует благодаря:

- **Open Source сообществу** — за вдохновение и идеи
- **Contributors** — всем, кто участвовал в развитии проекта
- **PHP сообществу** — за отличный язык программирования

---

## 📞 Контакты

- 🌐 **Веб-сайт**: [https://roocms.com](https://roocms.com)
- 📧 **Email**: info@roocms.com
- 💻 **GitHub**: [https://github.com/RooCMS/RooCMS](https://github.com/RooCMS/RooCMS)
- 📦 **Releases**: [https://github.com/RooCMS/RooCMS/releases](https://github.com/RooCMS/RooCMS/releases)

---

<div align="center">

**RooCMS v2.0.0 Alpha** — Современная CMS и API платформа

© 2010-2025 alex Roosso. All rights reserved.

[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net/)

**Создано с ❤️ для PHP сообщества**

</div>
