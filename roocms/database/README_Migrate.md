# RooCMS Universal Database Migration System

> Documents are temporarily in Russian language.   
> They will be translated into English later.

Универсальная система миграций для RooCMS, поддерживающая MySQL, PostgreSQL и Firebird через собственный PDO класс без использования внешних фреймворков.

## 🚀 Возможности

- ✅ **Универсальная поддержка БД**: MySQL, PostgreSQL, Firebird
- ✅ **Автоматическое определение драйвера БД**
- ✅ **Версионирование миграций** с автоматической индексацией
- ✅ **Откат миграций (rollback)**
- ✅ **Транзакционная безопасность**
- ✅ **CLI интерфейс** для управления
- ✅ **Гибкие типы данных** с автоматическим преобразованием
- ✅ **Поддержка индексов** всех типов
- ✅ **Вставка и удаление данных**
- ✅ **Произвольные SQL запросы**

## 📁 Структура файлов

```
roocms/database/
├── migrate.php              # Основной класс DatabaseMigrator
├── migrate_cli.php          # CLI интерфейс для управления
├── README.md               # Данная документация
└── migrations/             # Папка с файлами миграций
    ├── migrate_20_01.php   # Базовые таблицы
    └── migrate_20_04.php   # Базовые данные
```

## 📋 Требования

- **PHP 8.1+** (использует современные возможности PHP)
- **PDO расширение** для работы с базами данных
- **Класс Db** из RooCMS (собственная PDO обертка)
- **Права на создание таблиц** в базе данных
- **Настроенный префикс** в `roocms/config/config.php` (`$db_info['prefix']`)

## 🏥 Database Health Monitoring

RooCMS предоставляет мощные инструменты для мониторинга здоровья базы данных:

### Методы проверки соединения:

#### 1. Проверка текущего соединения
```php
$db = new Db();

// Простая проверка
$is_connected = $db->ping(); // true/false

// Детальная информация о здоровье
$health = $db->get_health_status();
print_r($health);
/*
Array (
    'status' => 'healthy',
    'connection_alive' => true,
    'database_info' => [...],
    'table_count' => 15,
    'query_stats' => [...],
    'memory_usage' => 2097152,
    'php_version' => '8.1.0',
    'pdo_drivers' => ['mysql', 'sqlite', 'pgsql'],
    'check_time' => 1234567890
)
*/
```

#### 2. Проверка другого соединения
```php
// Проверка возможности подключения к другой БД
$can_connect = $db->check_connect(
    host: 'remote-host.com',
    user: 'username',
    pass: 'password',
    base: 'database_name',
    port: 3306,
    detailed: true // Получить детальную информацию
);
```

#### 3. API Health Check
```
GET /api/v1/health
```
Возвращает полную информацию о состоянии БД в JSON формате.



## 🔢 Мониторинг количества запросов

### Доступ к счетчику запросов:

```php
$db = new Db();

// Прямой доступ к счетчику
$query_count = $db->query_count;

// Полная статистика
$stats = $db->get_query_stats();
echo "Выполнено запросов: " . $stats['query_count'];

// Health check включает счетчик
$health = $db->get_health_status();
echo "Текущий счетчик: " . $health['query_count'];
```

### Что считается:
- ✅ Все запросы через метод `query()`
- ✅ Запросы через Query Builder (`select()`, `insert()`, `update()`, `delete()`)
- ✅ Запросы через `insert_array()`, `update_array()`, `insert_batch()`
- ✅ Проверки подключения через `ping()`

### Отладка счетчика:
Если счетчик не работает, проверьте:
1. Версию файла - убедитесь что используете обновленный класс
2. Правильность доступа - используйте `$db->query_count` (публичное поле)
3. Время проверки - счетчик увеличивается после выполнения запроса
4. Используйте `get_query_stats()` для детальной информации

## 🏷️ Работа с константами таблиц

Мигратор использует константы таблиц из `roocms/config/defines.php`:

### ✅ Правильно (используем константы):
```php
'tables' => [
    'TABLE_USERS' => [...],         // Константа из defines.php
    'TABLE_CONFIG_PARTS' => [...],  // Константа из defines.php  
    'TABLE_CONTENT' => [...],       // Константа из defines.php
]
```

### ❌ Неправильно (строки или префиксы):
```php
'tables' => [
    'users' => [...],               // Не константа!
    'roocms_users' => [...],        // Строка с префиксом!
]
```

### В raw_sql используйте константы:
```php
'raw_sql' => [
    "UPDATE " . TABLE_USERS . " SET status = 'active'",
    "DELETE FROM " . TABLE_CONTENT . " WHERE id = 1",
]
```

### ⚙️ Добавление новых таблиц

Если вам нужна новая таблица:

1. **Добавьте константу в `defines.php`**:
```php
const TABLE_MY_NEW = DB_PREFIX.'my_new_table';
```

2. **Используйте в миграции**:
```php
'tables' => [
    'TABLE_MY_NEW' => [
        'columns' => [...],
    ]
]
```

## 🎯 Быстрый старт

### 1. Инициализация

```php
<?php
// Подключение необходимых файлов
require_once 'roocms/class/class_db.php';
require_once 'roocms/database/migrate.php';

// Создание экземпляра мигратора
$db = new Db();
$migrator = new DatabaseMigrator($db);
```

### 2. Выполнение миграций

```php
// Выполнить все ожидающие миграции
$executed = $migrator->migrate();

// Проверить статус миграций
$status = $migrator->status();
```

### 3. Использование CLI интерфейса

```bash
# Показать справку
php roocms/database/migrate_cli.php help

# Выполнить все миграции
php roocms/database/migrate_cli.php migrate

# Показать статус
php roocms/database/migrate_cli.php status

# Откатить последнюю миграцию
php roocms/database/migrate_cli.php rollback

# Откатить 3 последние миграции
php roocms/database/migrate_cli.php rollback 3
```

## 🏗️ Создание миграций

### Именование файлов

Файлы миграций должны называться по шаблону:
```
migrate_YYYYMMDD_NN.php
```

Примеры:
- `migrate_20250115_01.php` - Первая миграция от 15 января 2025
- `migrate_20250115_02.php` - Вторая миграция того же дня  
- `migrate_20250120_01.php` - Первая миграция от 20 января 2025

### Базовая структура миграции

```php
<?php
if(!defined('RooCMS')) {
    exit('403:Access denied');
}

return [
    'up' => [
        // Операции выполнения миграции
    ],
    'down' => [
        // Операции отката миграции
    ],
];
```

### Создание таблиц

⚠️ **ВАЖНО**: Используем **константы таблиц** из `defines.php`!

```php
'up' => [
    'tables' => [
        // Используем константу USERS_TABLE из defines.php
        'USERS_TABLE' => [
            'columns' => [
                'id' => [
                    'type' => 'integer',
                    'auto_increment' => true,
                    'null' => false,
                ],
                'name' => [
                    'type' => 'string',
                    'length' => 255,
                    'null' => false,
                ],
                'status' => [
                    'type' => 'enum',
                    'values' => ['active', 'inactive'],
                    'default' => 'active',
                    'null' => false,
                ],
            ],
            'indexes' => [
                [
                    'type' => 'primary',
                    'columns' => 'id',
                ],
                [
                    'type' => 'unique',
                    'name' => 'name',
                    'columns' => 'name',
                ],
            ],
            'options' => [
                'engine' => 'InnoDB',
                'charset' => 'utf8mb4',
                'collate' => 'utf8mb4_unicode_ci',
            ],
        ],
    ],
],
```

### Изменение таблиц (ALTER TABLE)

```php
'up' => [
    'alter_tables' => [
        'existing_table' => [
            'add_columns' => [
                'new_field' => [
                    'type' => 'string',
                    'length' => 100,
                    'null' => true,
                ],
            ],
            'drop_columns' => [
                'old_field',
            ],
            'add_indexes' => [
                [
                    'type' => 'key',
                    'name' => 'new_field_idx',
                    'columns' => 'new_field',
                ],
            ],
        ],
    ],
],
```

### Вставка данных

```php
'up' => [
    'data' => [
        'table_name' => [
            ['id' => 1, 'name' => 'Первая запись', 'status' => 'active'],
            ['id' => 2, 'name' => 'Вторая запись', 'status' => 'inactive'],
        ],
    ],
],
```

### Удаление данных

```php
'up' => [
    'delete_data' => [
        'table_name' => [
            'where' => 'status = ?',
            'params' => ['inactive'],
        ],
    ],
],

# Или удаление всех записей
'delete_data' => [
    'table_name' => [], // Удалить все записи
],
```

### Произвольные SQL запросы

В `raw_sql` используйте константы таблиц:

```php
'up' => [
    'raw_sql' => [
        // Используем константы из defines.php
        "UPDATE " . USERS_TABLE . " SET last_login = NOW() WHERE status = 'active'",
        "ALTER TABLE " . USERS_TABLE . " ADD CONSTRAINT fk_profile FOREIGN KEY (profile_id) REFERENCES " . USER_PROFILES_TABLE . "(id)",
        
        // Более сложный пример с JOIN
        "UPDATE " . TAGS_TABLE . " t SET usage_count = (SELECT COUNT(*) FROM " . CONTENT_TAGS_TABLE . " ct WHERE ct.tag_id = t.id)",
    ],
],
```

## 🗂️ Поддерживаемые типы данных

### Основные типы

| Универсальный тип | MySQL            | PostgreSQL       | Firebird         |
|-------------------|------------------|------------------|-----------|
| `integer`         | INT(11)          | INTEGER          | INTEGER   |
| `bigint`          | BIGINT(20)       | BIGINT           | BIGINT    |
| `string`          | VARCHAR(255)     | VARCHAR(255)     | VARCHAR(255) |
| `text`            | TEXT             | TEXT             | BLOB SUB_TYPE TEXT |
| `longtext`        | LONGTEXT         | TEXT             | TEXT      |
| `boolean`         | TINYINT(1)       | BOOLEAN          | BOOLEAN   |
| `decimal`         | DECIMAL(10,2)    | DECIMAL(10,2)    | DECIMAL(18,2) |
| `float`           | FLOAT            | REAL             | FLOAT     |
| `double`          | DOUBLE           | DOUBLE PRECISION | DOUBLE PRECISION |
| `timestamp`       | TIMESTAMP        | TIMESTAMP        | TIMESTAMP |
| `datetime`        | DATETIME         | TIMESTAMP        | TIMESTAMP |
| `date`            | DATE             | DATE             | DATE      |
| `time`            | TIME             | TIME             | TIME      |

### Специальные типы

- **ENUM**: Автоматически конвертируется в CHECK ограничения для PostgreSQL
- **AUTO_INCREMENT**: SERIAL для PostgreSQL, IDENTITY для Firebird

### Параметры колонок

```php
'column_name' => [
    'type' => 'string',           // Тип данных
    'length' => 255,              // Длина (для string, integer)
    'precision' => 10,            // Точность (для decimal)
    'scale' => 2,                 // Масштаб (для decimal)
    'null' => false,              // Разрешить NULL
    'default' => 'значение',      // Значение по умолчанию
    'auto_increment' => true,     // Автоинкремент
    'values' => ['val1', 'val2'], // Значения для ENUM
],
```

## 🔍 Типы индексов

```php
'indexes' => [
    // Первичный ключ
    [
        'type' => 'primary',
        'columns' => 'id',
    ],
    
    // Уникальный индекс
    [
        'type' => 'unique',
        'name' => 'email_unique',
        'columns' => 'email',
    ],
    
    // Обычный индекс
    [
        'type' => 'key',
        'name' => 'name_idx',
        'columns' => 'name',
    ],
    
    // Композитный индекс
    [
        'type' => 'key',
        'name' => 'user_status_idx',
        'columns' => ['user_id', 'status'],
    ],
],
```

## ⚡ CLI Команды

### migrate
Выполняет все ожидающие миграции в правильном порядке.

```bash
php migrate_cli.php migrate
```

### rollback
Откатывает указанное количество последних миграций.

```bash
# Откатить 1 миграцию (по умолчанию)
php migrate_cli.php rollback

# Откатить 3 миграции
php migrate_cli.php rollback 3
```

### status
Показывает текущий статус миграций.

```bash
php migrate_cli.php status
```

Пример вывода:
```
📊 Статус миграций базы данных
==================================================
📈 Всего миграций: 3
✅ Выполнено: 2
⏳ Ожидает выполнения: 1

✅ Выполненные миграции:
   • migrate-200-1
   • migrate-200-2

⏳ Ожидающие выполнения:
   • migrate-200-3
```

### version
Показывает информацию о версии мигратора.

```bash
php migrate_cli.php version
```

### help
Показывает справку по всем доступным командам.

```bash
php migrate_cli.php help
```

## 🛡️ Безопасность и лучшие практики

### Транзакции
Каждая миграция выполняется в отдельной транзакции. При ошибке происходит автоматический откат всех изменений.

### Резервное копирование
⚠️ **Важно**: Всегда создавайте резервную копию базы данных перед выполнением миграций!

### Тестирование
1. Протестируйте миграции на копии продакшн данных
2. Убедитесь, что откат работает корректно
3. Проверьте совместимость со всеми используемыми БД

### Версионирование
- Используйте семантическое версионирование
- Не изменяйте уже выполненные миграции
- Создавайте новые миграции для исправлений

## 🐛 Отладка

### Включение отладки
Мигратор автоматически использует систему отладки RooCMS при установленной константе `DEBUGMODE`.

```php
define('DEBUGMODE', true);
```

### Логи ошибок
Все ошибки записываются в стандартные логи RooCMS и выводятся в консоль при использовании CLI.

### Проверка состояния
```php
// Получение детальной информации о миграциях
$status = $migrator->status();
print_r($status);

// Получение статистики выполненных запросов
$stats = $db->get_query_stats();
print_r($stats);
```

## 🔧 Расширенные возможности

### Создание пользовательских типов данных
Вы можете расширить класс `DatabaseMigrator` и переопределить методы конвертации типов для добавления собственных типов данных.

### Хуки и события
Система поддерживает выполнение произвольного кода через секцию `raw_sql`, что позволяет выполнять сложные операции миграции.

### Условная логика
```php
'up' => [
    'raw_sql' => [
        "SET @driver = (SELECT @@version_comment)",
        "CREATE TABLE IF NOT EXISTS temp_table AS SELECT 1 as id",
    ],
],
```

## 📚 Примеры полных миграций

### Пример 1: Создание системы пользователей
Смотрите файл `migrations/migrate-200-2.php` для полного примера создания таблиц пользователей с профилями.

### Пример 2: Изменение существующих таблиц
Смотрите файл `migrations/migrate-200-3.php` для примера добавления колонок и создания связанных таблиц.

### Пример 3: Миграция данных
```php
'up' => [
    'raw_sql' => [
        // Миграция данных из старой структуры в новую
        "INSERT INTO new_table (name, email) 
         SELECT CONCAT(first_name, ' ', last_name), email_address 
         FROM old_table 
         WHERE status = 'active'",
    ],
    'delete_data' => [
        'old_table' => [], // Очистить старую таблицу после миграции данных
    ],
],
```

## 🤝 Поддержка

- **Документация**: ----
- **Форум поддержки**: ---
- **Email**: info@roocms.com
- **GitHub Issues**: https://github.com/roocms/roocms

## 📄 Лицензия

GNU General Public License v3.0 - смотрите файл [LICENSE.md](../LICENSE.md) для подробностей.

## 🔄 История обновлений

### v2.0 - Интеграция с константами RooCMS
- ✅ **Использование констант таблиц** из `defines.php`  
- ✅ **Упрощенное именование** миграций: `migrate_YYYYMMDD_NN.php`
- ✅ **Элегантный код** с константами вместо префиксов
- ✅ **Полная интеграция** с архитектурой RooCMS
- ✅ Поддержка MySQL, PostgreSQL, Firebird
- ✅ Автоматическое преобразование типов данных
- ✅ Транзакционная безопасность
- ✅ CLI интерфейс для управления

---

**RooCMS Universal Migration System** - мощный и гибкий инструмент для управления структурой базы данных, созданный специально для RooCMS без использования внешних зависимостей. 

✨ *Элегантность констант, мощь универсальности, простота использования!*
