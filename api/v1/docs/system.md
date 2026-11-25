# Системные методы

[Общие правила и оглавление](../../README.md). Источники: [router.php](../../router.php), [HealthController](../controller_health.php), [CspController](../controller_csp.php).

## GET /

Доступ: публичный. Корень API без параметров. Пример: `GET /api/`.

200, ответ без общей обёртки data:

```json
{"success":true,"message":"RooCMS API","timestamp":"2026-11-25 12:00:00","version":"<version>","endpoints":{"health":"GET /api/v1/health"}}
```

`endpoints` содержит зарегистрированный в корневом обработчике справочник методов, а не только health. Версия берётся из конфигурации сборки.

## GET /v1/health

Доступ: публичный. Без параметров. Пример: `GET /api/v1/health`.

200: `{status:"healthy",checks:{api,database},system_info:{timestamp,timezone}}` в data. Проверки включают status и диагностические сведения. При статусе error одной из проверок — 503 и `status:"unhealthy"`; обёртка при этом остаётся успешной `success:true`, поскольку это результат диагностики.

## GET /v1/health/details

Доступ: публичный. Без параметров. Пример: `GET /api/v1/health/details`.

200: `{"api check":{...},"database check":{...},"system_info":{...},"php_info":{...},"roocms_info":{...},"users in system":0}` в data. Возвращает сведения PHP, памяти, версии RooCMS и количество пользователей. Пробелы в именах ключей — часть текущего ответа.

Общая ошибка подключения BaseController к БД может дать 503 до выполнения диагностики.

## POST /v1/csp-report

Доступ: публичный. Принимает JSON-отчёт CSP, записывает нарушение в журнал.

Обязательный объект `csp-report`. Content-Type: application/csp-report или application/json.

Пример тела:

```json
{"csp-report":{"document-uri":"https://dev.roocms.com/","violated-directive":"script-src","blocked-uri":"inline"}}
```

Пример пути: `POST /api/v1/csp-report`. 200: `{"status":"reported"}` в data.

Ошибки: 400 — некорректный JSON или отсутствующий csp-report.
