# Backup

[Общие правила и оглавление](../../README.md). Реализация: [BackupController](../controller_backup.php), [BackupService](../../../roocms/services/backup.php).

Все методы требуют доступа администратора. Операции create/restore/delete изменяют данные или файлы; примеры не предназначены для автоматического выполнения при проверке документации.

Ответы сервисов дополнительно обёрнуты BaseController: например, список резервных копий находится в `data.data`.

## POST /v1/backup/create

Все JSON-поля необязательны:

| Поле | Тип | По умолчанию |
| --- | --- | --- |
| compress | boolean | true |
| include_data | boolean | true |
| include_structure | boolean | true |
| universal_format | boolean | true |
| exclude_tables | array строк | [] |
| filename | string или null | null — автоматическое имя; разрешены латинские буквы, цифры, _, ., - |

Пример: `POST /api/v1/backup/create`, тело `{"compress":true,"include_data":true}`.

200: `{success:true,data:result,message:"Backup created successfully"}` в общем data; result — результат создания файла модулем DbBackuper. Ошибки проверки опций и создания перехватываются контроллером как 500.

## POST /v1/backup/restore

JSON: обязательная строка `filename` существующей копии; необязательные `drop_existing` boolean=false, `ignore_errors` boolean=false, `batch_size` int>0=1000.

Пример: `POST /api/v1/backup/restore`, тело `{"filename":"backup.sql.gz","drop_existing":false}`.

200: `{success:true,data:result,message:"Database restored successfully"}` в общем data. Сервис принимает имена .sql и .sql.gz без частей пути.

Ошибки: 400 — filename не передан; 500 — неверные опции/имя или ошибка восстановления.

## GET /v1/backup/list

Query, все необязательные: `date_from`, `date_to` — строки дат, интерпретируемые strtotime; `limit` — числовой предел списка. Фильтр sort не реализован.

Пример: `GET /api/v1/backup/list?limit=10&date_from=2026-11-01`.

200: `{success:true,data:[backup],count}` в общем data. Записи содержат имя, путь, размер, дату создания и другие сведения модуля резервирования. Ошибка: 500 — ошибка списка.

## DELETE /v1/backup/delete/{filename}

Обязательная строка `filename` в пути. Пример: `DELETE /api/v1/backup/delete/backup.sql.gz`.

200: `{success:true,message:"Backup deleted successfully",filename}` в общем data.

Ошибки: 400 — параметр пути не распознан; 500 — недопустимое имя, отсутствующий файл или ошибка удаления.

## GET /v1/backup/download/{filename}

Обязательная строка `filename` в пути. Пример: `GET /api/v1/backup/download/backup.sql.gz`.

200: бинарный файл, Content-Type application/octet-stream и Content-Disposition attachment, без JSON. Парсер пути распознаёт .sql/.sql.gz/.sql.bz2; фактическая доступность зависит от списка существующих копий.

Ошибки: 400 — неверное имя; 404 — файл не найден; 500 — ошибка скачивания.

## GET /v1/backup/logs

Необязательный query `limit` int, по умолчанию 100.

Пример: `GET /api/v1/backup/logs?limit=100`.

200: `{success:true,data:[],count:0}` в общем data. Сейчас сервис — заглушка, всегда возвращает пустой журнал; limit фактически не влияет на результат.

Ошибка контроллера: 500 — ошибка чтения.

## GET /v1/backup/status

Без параметров. Пример: `GET /api/v1/backup/status`.

200: `{success:true,data:{backup_count,total_size,total_size_human,latest_backup,oldest_backup,compression_enabled,backup_directory_writable,timestamp}}` в общем data.

Ошибка: 500 — ошибка определения состояния.
