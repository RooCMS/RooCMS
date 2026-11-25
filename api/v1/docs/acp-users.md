# ACP Users

[Общие правила и оглавление](../../README.md). Реализация: [ACPUsersController](../controller_acpUsers.php).

Все методы требуют доступа администратора. Список пользователей доступен через [GET /v1/users](users.md), отдельного GET списка по `/v1/acp/users` нет.

Формат объекта пользователя соответствует полному одиночному профилю в [Users](users.md); административный запрос может получить и удалённую запись. Пароль не включается.

## GET /v1/acp/users/roles

Без параметров. Пример: `GET /api/v1/acp/users/roles`.

200: объект ролей по кодам `u,m,a,su`; каждая содержит `key,name,description,level`. Ошибка: 500 — ошибка чтения.

## GET /v1/acp/users/{user_id}

`user_id` — обязательный int. Данные аккаунта и профиля для редактирования, включая административные поля.

Пример: `GET /api/v1/acp/users/12`. 200: объект пользователя в `data`. Ошибки: 404 — пользователь отсутствует; 500 — ошибка чтения.

## PUT /v1/acp/users/{user_id}

Частично обновляет аккаунт и профиль. `user_id` — обязательный int; JSON не должен быть пустым.

| JSON-поле | Тип / смысл |
| --- | --- |
| email | string, корректный уникальный email |
| role | string: u/m/a/su |
| is_active, is_verified, is_banned | boolean/0/1 |
| ban_expired | int timestamp, 0 для бессрочной блокировки |
| ban_reason | string |
| nickname, first_name, last_name, bio | string |
| gender | male/female/other или пустая строка |
| birthday | string Y-m-d или пустая строка |
| website | string URL с http:// или https:// или пустая строка |
| is_public | boolean/0/1 |

Неизвестные поля отбрасываются. Пароль меняется отдельным методом; `avatar` не принимается.

Пример: `PUT /api/v1/acp/users/12`, тело `{"first_name":"Иван","is_active":true}`.

200: сообщение `User updated successfully`, без `data`. Ошибки: 400 — пустое тело; 404 — нет пользователя; 409 — конфликт email/nickname; 422 — недопустимые значения; 500 — ошибка сохранения.

## PUT /v1/acp/users/{user_id}/password

`user_id` — int. JSON: обязательное поле `password`, строка нового пароля; текущий пароль не требуется.

Пример: `PUT /api/v1/acp/users/12/password`, тело `{"password":"new-example-password"}`.

200: сообщение `Password changed successfully`, без `data`. Ошибки: 400 — нет поля или пароль не отвечает требованиям; 404 — нет пользователя; 500 — ошибка изменения.

## PATCH /v1/acp/users/{user_id}/role

`user_id` — int. JSON: обязательное `role` со значением `u,m,a,su`.

Пример: `PATCH /api/v1/acp/users/12/role`, тело `{"role":"m"}`.

200: сообщение `Role changed successfully`, без `data`. Ошибки: 400 — нет role; 404 — нет пользователя; 422 — неизвестная роль; 500 — ошибка сохранения.

## PATCH /v1/acp/users/{user_id}/activate

`user_id` — int, тело не требуется. Пример: `PATCH /api/v1/acp/users/12/activate`.

200: сообщение `User activated successfully`, без `data`. Ошибки контроллера: 500 — операция не выполнена.

## PATCH /v1/acp/users/{user_id}/deactivate

`user_id` — int, тело не требуется. Пример: `PATCH /api/v1/acp/users/12/deactivate`.

200: сообщение `User deactivated successfully`, без `data`. Ошибки контроллера: 500 — операция не выполнена.

## PATCH /v1/acp/users/{user_id}/verify

`user_id` — int, тело не требуется. Подтверждает email административно.

Пример: `PATCH /api/v1/acp/users/12/verify`. 200: сообщение об успешном подтверждении, без `data`. Ошибка: 500 — операция не выполнена.

## PATCH /v1/acp/users/{user_id}/unverify

`user_id` — int, тело не требуется. Снимает подтверждение email.

Пример: `PATCH /api/v1/acp/users/12/unverify`. 200: сообщение об успешном снятии подтверждения, без `data`. Ошибка: 500 — операция не выполнена.

## DELETE /v1/acp/users/{user_id}/avatar

`user_id` — int, тело не требуется. Пример: `DELETE /api/v1/acp/users/12/avatar`.

200: сообщение об удалении аватара, без `data`. Ошибки: 404 — пользователь/аватар отсутствует; 500 — операция не выполнена.

## DELETE /v1/acp/users/{user_id}

`user_id` — int, тело не требуется. Пример: `DELETE /api/v1/acp/users/12`.

200: сообщение `User deleted successfully`, без `data`. Ошибки: 404 — пользователь отсутствует или уже удалён; другие ошибки предметной области возвращаются с кодом сервиса; 500 — операция не выполнена.

Для всех методов также действуют общие ошибки доступа 401/403. Часть методов изменения состояния перехватывает исключения сервиса как 500, поэтому отсутствие пользователя не всегда возвращает 404.
