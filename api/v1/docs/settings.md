# Публичные настройки

[Общие правила и оглавление](../../README.md). Реализация: [SettingsController](../controller_settings.php).

## GET /v1/settings

Доступ: публичный. Без параметров. Возвращает текущие значения категории `site` из БД, без метаданных.

Пример запроса: `GET /api/v1/settings`.

Пример `data`:

```json
{"site_name":"RooCMS","site_domain":"dev.roocms.com","site_description":"CMS"}
```

Состав ключей определяется БД. Общая ошибка подключения к БД — 503.
