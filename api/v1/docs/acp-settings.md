# ACP Settings

[Общие правила и оглавление](../../README.md). Реализация: [ACPSettingsController](../controller_acpSettings.php).

Все методы требуют доступа администратора. Значения и метаданные настроек берутся из БД. Категории текущего интерфейса: `site`, `mailer`, `security`, `gd`; API не ограничивает список этими кодами.

## GET /v1/acp/settings

Все настройки, сгруппированные по категориям. Без параметров.

Пример: `GET /api/v1/acp/settings`. 200, пример `data`: `{"site":{"site_name":"RooCMS"},"mailer":{"mailer_port":587}}`.

Ошибка: 500 — не удалось прочитать настройки.

## GET /v1/acp/settings/meta

Все значения и метаданные полей. Без параметров.

Пример: `GET /api/v1/acp/settings/meta`. 200: `{settings:{category:{key:value}},meta:{key:metadata}}`.

Метаданные: `id,category,sort_order,title,description,type,options,max_length,is_required,is_serialized,created_at,updated_at`. `options` декодируется из JSON, если заполнен. Типы служебных значений метаданных соответствуют результату БД; клиент не должен считать все флаги native boolean.

Ошибка: 500 — не удалось прочитать значения/метаданные.

## GET /v1/acp/settings/group-{group}

`group`: обязательная строка в пути. Возвращает плоский объект значений категории.

Пример: `GET /api/v1/acp/settings/group-site`. 200, пример `data`: `{"site_name":"RooCMS"}`.

Ошибки: 404 — категория отсутствует или пуста; 500 — ошибка чтения.

## GET /v1/acp/settings/key-{key}

`key`: обязательная строка в пути.

Пример: `GET /api/v1/acp/settings/key-site_name`. 200: `{key:"site_name",value:"RooCMS",meta:metadata}`.

Ошибки: 404 — настройка отсутствует; 500 — ошибка чтения.

## PUT /v1/acp/settings/key-{key}

Меняет одно значение. Обязательный `key` в пути и поле `value` в JSON; `null` считается отсутствующим значением.

Пример: `PUT /api/v1/acp/settings/key-site_name`, тело `{"value":"RooCMS"}`.

200: сообщение `Setting updated successfully`, без `data`.

Ошибки: 400 — нет `value`; 404 — неизвестный ключ; 422 — ошибка значения в `details.validation_errors[key]`; 500 — отсутствуют метаданные или не удалось сохранить.

## PATCH /v1/acp/settings

Меняет несколько настроек. JSON — непустой плоский объект `key:value`, без группировки по категориям.

Пример: `PATCH /api/v1/acp/settings`, тело `{"site_name":"RooCMS","mailer_port":587}`.

200: сообщение `Settings updated successfully`, без `data`. Все значения проверяются до начала записи; ошибка валидации любого поля отменяет сохранение всего запроса.

Ошибки: 400 — пустые данные; 422 — неизвестные ключи и неверные значения в `details.validation_errors` с исходными именами настроек; 500 — ошибка сохранения.

### Проверки значений

Пустая строка или `null` допустимы для необязательных полей при общей валидации. Обязательность и максимальная длина берутся из метаданных; длина строки проверяется в байтах.

| Тип | Принимаемое значение |
| --- | --- |
| boolean | `true,false,0,1,"0","1"` |
| integer | Значение, проходящее FILTER_VALIDATE_INT; текущая проверка отвергает также 0 |
| string, text, html, color | Строка |
| date | Положительное числовое значение timestamp |
| email | Корректный email |
| select | Ключ из `meta.options` |
| image, file | Строка пути/URL; этот метод не загружает файл |

## GET /v1/acp/settings/reset/all

Сбрасывает все настройки к значениям по умолчанию с учётом защищённых ключей сервиса. Без параметров.

Пример: `GET /api/v1/acp/settings/reset/all`. 200: сообщение `All settings reset to default values`, без `data`. Ошибка: 500 — сброс не выполнен.

## GET /v1/acp/settings/reset/group-{group}

Обязательная строка `group` в пути. Сбрасывает категорию.

Пример: `GET /api/v1/acp/settings/reset/group-gd`. 200: сообщение `Group settings reset to default values`, без `data`.

Ошибки: 404 — категория отсутствует или пуста; 500 — сброс не выполнен.

## GET /v1/acp/settings/reset/key-{key}

Обязательная строка `key` в пути. Сбрасывает одну настройку.

Пример: `GET /api/v1/acp/settings/reset/key-site_name`. 200: сообщение `Setting reset to default value`, без `data`.

Ошибки: 404 — неизвестная настройка; 500 — сброс не выполнен.

Три reset-маршрута изменяют данные через GET в текущей реализации. Примеры показывают форму запроса, не являются проверками только для чтения.
