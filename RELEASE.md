# RooCMS v2.0.0 Alpha

> **Статус релиза:** Alpha 🚀  
> **Дата релиза:** TBD  
> **Совместимость:** PHP 8.1+  
> **Версия:** 2.0.0 alpha  

![API Status](https://img.shields.io/badge/API-Healthy-brightgreen)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net/)
![Database](https://img.shields.io/badge/DB-MySQL%20%7C%20PostgreSQL%20%7C%20Firebird-orange)

### Notice
> Documents are temporarily in Russian language.   
> They will be translated into English later.

## Обзор релиза

RooCMS 2.0 Alpha представляет собой крупное обновление с полным переписыванием архитектуры. Основной фокус на современные технологии, безопасность, производительность и API-first подход для создания не только веб-сайтов, но и любых приложений.

## Update from previous versions

- Not supported

## Миграции базы данных

- CLI-инструмент: `roocms/database/migrate_cli.php`
- Поддерживаемые команды: `migrate`, `rollback [steps]`, `status`, `version`, `help`
- Файлы миграций: `roocms/database/migrations/migrate_YYYYMMDD_NN.php`

Примеры:
```bash
# Выполнить все ожидающие миграции
php roocms/database/migrate_cli.php migrate

# Откатить последние 2 миграции
php roocms/database/migrate_cli.php rollback 2

# Посмотреть статус
php roocms/database/migrate_cli.php status
```

## Система резервного копирования

- CLI-инструмент: `roocms/database/backup_cli.php`
- API endpoints: `/api/v1/backup/*`
- Поддерживаемые команды: `create`, `restore`, `list`, `delete`, `status`, `help`
- Многоуровневая защита файлов резервных копий

Примеры:
```bash
# Создать резервную копию
php roocms/database/backup_cli.php create --compress

# Восстановить из резервной копии
php roocms/database/backup_cli.php restore --filename=backup_20241226_143022.sql.gz

# Список всех резервных копий
php roocms/database/backup_cli.php list
```

Рекомендации:
- Всегда делайте бэкап перед миграциями
- Тестируйте на стейдже, затем на продакшн
- Не редактируйте уже выполненные файлы миграций — создавайте новые

### Known Issues (Alpha)

- Ограниченная поддержка PostgreSQL/Firebird в edge-кейсах типов и индексов
- Возможны предупреждения/ограничения при откате сложных миграций


## 🚀 Ключевые особенности

### 🔥 Новая архитектура
- ✅ **Полное переписывание на PHP 8.1+** с использованием современных возможностей языка
- ✅ **API-First подход** - RooCMS теперь не только CMS, но и мощная API платформа
- ✅ **Файловая система управления** - trait-based архитектура для обработки различных типов файлов
- ✅ **Без фреймворков и ORM** - чистый PHP код для максимальной производительности
- ✅ **Строгая типизация** и современные PHP 8+ практики

### 🌐 RESTful API
- ✅ **Полнофункциональный REST API** (`/api/v1/`) для интеграции с любыми приложениями
- ✅ **Система роутинга** с поддержкой динамических параметров (`{id}`, `{param}`)
- ✅ **Middleware система** для аутентификации и авторизации
- ✅ **Health Check эндпоинты** для мониторинга состояния системы
- ✅ **Backup API эндпоинты** для управления резервными копиями
- ✅ **Стандартизированные JSON ответы** с обработкой ошибок

### 🗄️ Универсальная работа с БД
- ✅ **PDO parameter binding** для безопасной работы с базой данных без SQL инъекций
- ✅ **Мульти-база поддержка** - MySQL, PostgreSQL, Firebird в одном коде
- ✅ **Система миграций** с CLI интерфейсом и автоматическим версионированием
- ✅ **Database Health Monitoring** - мониторинг состояния БД в реальном времени
- ✅ **Система резервного копирования** - полнофункциональная система backup/restore с CLI и API
- ✅ **Транзакционная безопасность** в операциях

### 🔐 Безопасность и аутентификация
- ✅ **Современная система аутентификации** с токенами и ролями
- ✅ **CSP (Content Security Policy)** поддержка
- ✅ **Защита от SQL инъекций** через PDO parameter binding
- ✅ **Валидация и санитизация** всех входных данных

### 🎨 Система тем и фронтенд
- ✅ **Модульная система тем** с поддержкой множественных тем
- ✅ **Два типа шаблонизаторов** - PHP и HTML с плейсхолдерами
- ✅ **Alpine.js интеграция** для интерактивности без сложности
- ✅ **CSP совместимость** фронтенд компонентов
- ✅ **Модульная архитектура JS** с разделением по страницам
- ✅ **Tailwind CSS 4.x** для быстрой разработки пользовательского интерфейса
- ✅ **Современный стек** без зависимостей от тяжелых фреймворков
> Мы не навязываем использование данного подхода, потому что ядро само по предлагает готовый API, что позволяет использовать любой стек фронтенда на ваше усмотрение. Вы можете использовать React, Vue, Angular, vanilla HTML+JS или встроенный PHP шаблонизатор. Но для Вашего удобства мы предоставляем готовый стек на основе Tailwind CSS 4.x и Alpine.js и собственного шаблонизатора на основе PHP. Мы постарались сделать все максимально простым и удобным для использования.

### ⚡ Производительность
- ✅ **Оптимизированные запросы** с подсчетом и мониторингом
- ✅ **Минимальное потребление памяти** (~2MB peak usage)
- ✅ **Быстрое время отклика** (8-15ms для API запросов)
- ✅ **Снижение потребления ресурсов** для высоконагруженных сценариев



## 📊 Производительность и статистика

### Бенчмарки API (на тестовом сервере)
- **Время отклика Health Check**: 8-15ms
- **Потребление памяти**: ~2MB peak usage
- **Запросы к БД**: ---
- **Поддерживаемые RPS**: 1000+ (зависит от сервера)

### Статистика кода
- **Общий размер**: ~762KB PHP кода (без зависимостей)
- **Классов**: 23 основных класса + 14 трейтов + 4 интерфейса = 41 компонент
- **API контроллеров**: 11 контроллеров с ~70+ публичными методами
- **Поддерживаемых БД**: 3 (MySQL, PostgreSQL, Firebird)
- **Сервисов**: 10 бизнес-сервисов (Auth, User, Settings, Backup, Email, Files, etc.)

## Совместимость
- ✅ **PHP 8.1, 8.2, 8.3, 8.4**
- ✅ **MySQL 5.7+, 8.0+**
- ✅ **MariaDB 10.10+, 11.x**
- ✅ **PostgreSQL 14+, 15+, 16+**
- ✅ **Firebird 3.0+**
- ✅ **Apache 2.4, nginx**

## 🔮 Планы развития (Roadmap)

- 🔄 **Полное удаление legacy кода** - убрать поддержку vanilla запросов
- ➕ **Расширенная система пользователей** - профили, группы, активность
- ➕ **Система контента** - посты, категории, теги, мультиязычность
- 🔄 **Файловый менеджер API** - загрузка и управление файлами
- ➕ **Кэширование** - Redis/Memcached поддержка
- ➕ **Плагины система** - расширяемость через плагины
- ➕ **Расширение тем** - дополнительные темы оформления и компоненты
- ➕ **WebSocket поддержка** - реальное время уведомления
- ➕ **Docker контейнеризация** - готовые Docker образы

## 🧪 Плагины и расширения (в будущем)

### Планируемые официальные плагины
- 🔧 **roocms-blog** - Система блогов с комментариями
- 🔧 **roocms-shop** - E-commerce функциональность  
- 🔧 **roocms-forum** - Форум и обсуждения
- 🔧 **roocms-analytics** - Веб-аналитика и статистика
- 🔧 **roocms-seo** - SEO оптимизация и мета-теги

### Система плагинов (в разработке)
- 📦 **Composer интеграция** - установка через Composer
- 🔌 **Hook система** - события и фильтры
- ⚙️ **Конфигурация плагинов** - настройки через админ панель
- 🔒 **Безопасность плагинов** - песочница и проверка кода


## 🆕 Что нового в версии 2.0

### Полностью новые компоненты
- ✨ **Система резервного копирования** - полная замена старой системы с CLI и API
- ✨ **Dependency Injection контейнер** - управление зависимостями по SOLID принципам
- ✨ **Модульная система настроек** - динамические настройки с валидацией типов
- ✨ **Cryptography аутентификация** - современная система токенов и ролей
- ✨ **Health Check система** - мониторинг состояния всех компонентов

### Улучшенные компоненты
- 🔄 **API роутер** - полная переработка с поддержкой middleware
- 🔄 **Система тем** - поддержка множественных движков рендеринга
- 🔄 **Безопасность** - многоуровневая защита и CSP

### ⚠️ Важная информация

- ℹ️ **Alpha статус** - Некоторые функции могут работать нестабильно, тестируйте перед продакшеном
- ℹ️ **Миграции обязательны** - Для работы системы необходимо выполнить миграции БД
- ℹ️ **Новая документация** - API документация доступна в `api/README.md` и Swagger схемах
- ℹ️ **Обратная несовместимость** - версия 2.0 не совместима с предыдущими версиями


## 🛠️ Технические требования

### Minimal requirements
- **PHP**: 8.1+ 
- **Веб-сервер**: Apache 2.4+ / nginx
- **База данных**: MySQL 5.7+ / MariaDB 10.10+ / PostgreSQL 14+ / Firebird
- **Браузер**: Современный браузер с поддержкой JavaScript ES6+
- **Расширения PHP**: PDO, JSON, mbstring, openssl, curl, gd

### Рекомендуемая конфигурация
- **PHP**: 8.4
- **База данных**: MariaDB 11.7
- **Память**: 1GB+ RAM
- **Веб-сервер**: Apache 2.4 с mod_rewrite

### 🎨 Фронтенд технологии
- **Tailwind CSS 4.x**: Utility-first CSS фреймворк для быстрой разработки UI
- **Alpine.js**: Легковесный JavaScript фреймворк (~15KB)
- **Модульная архитектура**: Разделение кода по страницам и компонентам
- **CSP Ready**: Полная совместимость с Content Security Policy
- **Без сборщиков**: Готовые к использованию файлы без webpack/gulp
- **Современный ES6+**: Использование современных возможностей JavaScript
- **Любые технологии**: React, Vue, Angular, vanilla HTML+JS или встроенный PHP шаблонизатор

## 🚀 Quick start

### 1. Установка
```bash
# Скачать релиз
wget (ссылка позднее)

# Распаковать в корень сайта
unzip roocms-2.0.0-alpha.zip -d /var/www/html/

# Настроить права доступа
chmod -R 755 /var/www/html/roocms/
chmod -R 777 /var/www/html/storage/
```

### 2. Настройка базы данных
```bash
# Выполнить миграции
cd /var/www/html/
php roocms/database/migrate_cli.php migrate

# Проверить статус
php roocms/database/migrate_cli.php status
```

### 3. Check API
```bash
# Проверить здоровье системы
curl -X GET https://your-domain.com/api/v1/health -k

# Получить детальную информацию
curl -X GET https://your-domain.com/api/v1/health/details -k
```

## 📚 Примеры использования

### API запросы
```javascript
// JavaScript - проверка API
fetch('https://your-domain.com/api/v1/health')
  .then(response => response.json())
  .then(data => {
    if (data.success && data.data.status === 'healthy') {
      console.log('RooCMS работает нормально');
    }
  });
```

```php
// PHP - Оптимизированные прямые SQL запросы (используется в ядре)
$db = new Db();
$users = $db->fetch_all(
  'SELECT * FROM users WHERE status = ? ORDER BY created_at DESC LIMIT 10',
  ['active']
);
```

```bash
# CLI - управление миграциями
php roocms/database/migrate_cli.php migrate    # Выполнить миграции
php roocms/database/migrate_cli.php rollback 2 # Откатить 2 миграции
php roocms/database/migrate_cli.php status     # Показать статус

# CLI - управление резервными копиями
php roocms/database/backup_cli.php create --compress --universal
php roocms/database/backup_cli.php restore --filename=backup.sql.gz
php roocms/database/backup_cli.php list        # Список всех бэкапов
```

```html
<!-- HTML - use Alpine.js in your themes -->
<div x-data="{ open: false }">
  <button @click="open = !open" class="button">
    Show/Hide
  </button>
  <div x-show="open" x-transition>
    <p>Content with animation</p>
  </div>
</div>
```

## 🔗 Полезные ссылки

- 📖 [Основная документация](README.md)
- 🏗️ [Структура проекта](structure.md)
- 🔌 [API документация](api/README.md)
- 🗄️ [Система миграций](roocms/database/README_Migrate.md)
- 💾 [Система резервного копирования](roocms/database/README_Backup.md)
- 📊 [Swagger API схема](api/v1/docs/swagger.yaml)
- 📮 [Postman коллекция](api/v1/docs/postman.json)
- ⚖️ [Лицензия GPL v3](LICENSE.md)
- 🌐 [Официальный сайт](https://www.roocms.com)
- 📧 [Поддержка](mailto:info@roocms.com)

## 🤝 Участие в разработке

We welcome your contribution to the development of RooCMS:

1. **Bug reports**: Create an issue with a detailed description
2. **Pull requests**: Небольшие, сфокусированные изменения с тестами
3. **Следуйте правилам**: PHP 8.1+, без фреймворков/ORM, строгая типизация

## 🔒 Security

If you have found a security vulnerability, please report it responsibly to: **info@roocms.com**

---

<div align="center">

**RooCMS v2.0.0 Alpha** - Modern CMS and API platform  
© 2010-2025 alex Roosso. All rights reserved.

[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://www.php.net/)

</div>