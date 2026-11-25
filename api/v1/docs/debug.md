# Debug

[Общие правила и оглавление](../../README.md). Реализация: [DebugController](../controller_debug.php).

Оба метода требуют доступа администратора.

## GET /v1/acp/debug/logs

Без параметров. Читает журнал отладки.

Пример: `GET /api/v1/acp/debug/logs`.

200: `{logs:[log]}` в data. Содержимое записи определяется DebugService и сохранённым журналом.

Ошибка: 500 — `Failed to read debug logs` в стандартной ошибке.

## POST /v1/acp/debug/clear

Без тела. Очищает журнал отладки.

Пример: `POST /api/v1/acp/debug/clear`.

200: `{status:"success",message:"Debug logs cleared successfully"}` в data.

Особенность текущего обработчика: при исключении возвращается HTTP 500, но через json_response — с `success:true` снаружи и `data:{status:"error",message:"Failed to clear debug logs: ..."}`. Это отличается от общего формата error_response.
