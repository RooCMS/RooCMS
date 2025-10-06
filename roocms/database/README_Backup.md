# RooCMS Database Backup System

> Documents are temporarily in Russian language.   
> They will be translated into English later.

Full system for backing up the database for RooCMS with support for MySQL/MariaDB, PostgreSQL and Firebird.

## Возможности

- ✅ **Создание резервных копий** с гибкими настройками
- ✅ **Восстановление из резервных копий** с контролем ошибок
- ✅ **Сжатие резервных копий** (gzip) для экономии места
- ✅ **CLI интерфейс** для автоматизации
- ✅ **REST API** для веб-интерфейса
- ✅ **Поддержка нескольких СУБД**: MySQL/MariaDB, PostgreSQL, Firebird
- ✅ **Безопасность**: проверка прав доступа, защита от path traversal
- ✅ **Логирование операций** для аудита
- ✅ **Dependency Injection** для легкого тестирования

## Компоненты

### 1. Основной класс `DbBackuper`
File: `roocms/class/class_dbBackuper.php`

Основной класс для работы с резервными копиями.

#### Основные методы:
- `create_backup(array $options)` - создание резервной копии
- `restore_backup(string $filename, array $options)` - восстановление из резервной копии
- `list_backups()` - список доступных резервных копий
- `delete_backup(string $filename)` - удаление резервной копии
- `get_backup_logs()` - получение журнала операций

### 2. CLI утилита
File: `roocms/database/backup_cli.php`\r

Консольная утилита для управления резервными копиями.

### 3. API контроллер
File: `api/v1/controller_backup.php`\r

REST API для веб-интерфейса управления резервными копиями.

## Использование

### CLI интерфейс

#### Создание резервной копии
```bash
# Базовое создание резервной копии
php backup_cli.php create

# С пользовательскими параметрами
php backup_cli.php create --filename my_backup --no-compress

# Только структура без данных
php backup_cli.php create --structure-only

# Исключение определенных таблиц
php backup_cli.php create --exclude-tables "logs,cache,sessions"
```

#### Восстановление из резервной копии
```bash
# Базовое восстановление
php backup_cli.php restore backup_2025-01-15_14-30-00.sql.gz

# С игнорированием ошибок
php backup_cli.php restore backup.sql --ignore-errors

# С удалением существующих таблиц
php backup_cli.php restore backup.sql --drop-existing
```

#### Управление резервными копиями
```bash
# Список всех резервных копий
php backup_cli.php list

# Удаление резервной копии
php backup_cli.php delete old_backup.sql

# Справка
php backup_cli.php help
```

### Программный интерфейс

```php
// Инициализация
$db = new Db();
$backup = new DbBackuper($db);

// Создание резервной копии
$result = $backup->create_backup([
    'compress' => true,
    'include_data' => true,
    'include_structure' => true,
    'exclude_tables' => ['logs', 'cache'],
    'filename' => 'my_backup',
    'add_timestamp' => true
]);

if($result['success']) {
    echo "Резервная копия создана: {$result['filename']}";
}

// Восстановление
$result = $backup->restore_backup('backup.sql.gz', [
    'ignore_errors' => false,
    'batch_size' => 1000
]);

// Список резервных копий
$backups = $backup->list_backups();
foreach($backups as $backup_info) {
    echo "{$backup_info['filename']} - {$backup_info['size_human']}";
}
```

### REST API

API требует административных прав доступа.

#### Создание резервной копии
```bash
POST /api/v1/backup/create
Content-Type: application/json
Authorization: Bearer <admin_token>

{
    "compress": true,
    "include_data": true,
    "include_structure": true,
    "exclude_tables": ["logs"],
    "filename": "api_backup",
    "add_timestamp": true
}
```

#### Список резервных копий
```bash
GET /api/v1/backup/list
Authorization: Bearer <admin_token>
```

#### Скачивание резервной копии
```bash
GET /api/v1/backup/download/{filename}
Authorization: Bearer <admin_token>
```

#### Восстановление из резервной копии
```bash
POST /api/v1/backup/restore
Content-Type: application/json
Authorization: Bearer <admin_token>

{
    "filename": "backup_2025-01-15_14-30-00.sql.gz",
    "ignore_errors": false,
    "batch_size": 1000
}
```

#### Статус системы резервного копирования
```bash
GET /api/v1/backup/status
Authorization: Bearer <admin_token>
```

#### Журнал операций
```bash
GET /api/v1/backup/logs
Authorization: Bearer <admin_token>
```

#### Удаление резервной копии
```bash
DELETE /api/v1/backup/delete/{filename}
Authorization: Bearer <admin_token>
```

## Конфигурация

### Параметры создания резервной копии
- `compress` (bool) - сжимать резервную копию (по умолчанию: true)
- `include_data` (bool) - включать данные таблиц (по умолчанию: true)
- `include_structure` (bool) - включать структуру таблиц (по умолчанию: true)
- `exclude_tables` (array) - список таблиц для исключения
- `filename` (string) - имя файла резервной копии
- `add_timestamp` (bool) - добавлять временную метку к имени файла (по умолчанию: true)

### Параметры восстановления
- `drop_existing` (bool) - удалять существующие таблицы (по умолчанию: false)
- `ignore_errors` (bool) - продолжать при ошибках (по умолчанию: false)
- `batch_size` (int) - размер пакета для обработки (по умолчанию: 1000)

## Хранение резервных копий

Резервные копии сохраняются в директории `roocms/database/backups/` (определена константой `_BACKUPS`) с защитным файлом `index.php` для предотвращения прямого доступа.

Это обеспечивает логическую группировку всех компонентов системы баз данных:
- Миграции: `roocms/database/migrations/`
- Резервные копии: `roocms/database/backups/`
- CLI утилиты: `roocms/database/backup_cli.php`, `migrate_cli.php`

Поддерживаемые форматы:
- `.sql` - обычный SQL дамп
- `.sql.gz` - сжатый SQL дамп (gzip)

## Безопасность

1. **Аутентификация**: API требует административных прав
2. **Валидация файлов**: проверка имен файлов для предотвращения path traversal
3. **Защита директорий**: файлы `index.php` в директориях с данными
4. **Логирование**: все операции записываются в журнал

## Поддерживаемые СУБД

### MySQL/MariaDB
- Полная поддержка всех функций
- Использует `SHOW TABLES`, `SHOW CREATE TABLE`
- Поддержка UTF-8 и collation

### PostgreSQL
- Поддержка основных функций
- Использует `information_schema` для метаданных
- Экранирование кавычек для PostgreSQL

### Firebird
- Базовая поддержка
- Использует системные таблицы `rdb$*`
- Адаптирован синтаксис под Firebird

# Logging

Система ведет подробные журналы всех операций:
- Создание резервных копий
- Восстановление
- Удаление файлов
- Ошибки и исключения

Журналы доступны через:
- Метод `get_backup_logs()` в классе
- API endpoint `/api/v1/backup/logs`

## Usage Examples

### Automatic Backup via Cron
```bash
# Ежедневно в 2:00
0 2 * * * cd /path/to/roocms && php roocms/database/backup_cli.php create
```

### Integration into Admin Panel
```javascript
// Создание резервной копии через AJAX
fetch('/api/v1/backup/create', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + adminToken
    },
    body: JSON.stringify({
        compress: true,
        filename: 'manual_backup'
    })
})
.then(response => response.json())
.then(data => {
    if(data.success) {
        console.log('Резервная копия создана:', data.data.filename);
    }
});
```

# Testing

To test the system, run the following commands:

```bash
# Простой тест
cd roocms/database && php simple_test.php

# Полная CLI справка
cd roocms/database && php backup_cli.php help
```

## Requirements

- PHP 8.1+
- PDO with driver for your RDBMS
- Write permissions on the `storage/backups/` directory
- For compression: zlib extension (gzencode)

## Limitations

1. Very large databases may require increasing PHP limits (memory_limit, execution_time)
2. Compression is only available with the zlib extension
3. Some specific functions of the RDBMS may not be supported (triggers, procedures)

---

Система резервного копирования интегрирована в архитектуру RooCMS и готова к использованию как через командную строку, так и через веб-интерфейс.