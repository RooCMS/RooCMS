# Users

[Общие правила и оглавление](../../README.md). Реализация: [UsersController](../controller_users.php).

Все методы требуют access token, кроме подтверждения email по коду. Доступ к чужому профилю дополнительно ограничивается сервисом: полные данные доступны владельцу и администраторам; остальные получают только доступный для них профиль.

Одиночный профиль содержит `id,role,is_active,is_banned,ban_expired,ban_reason,last_activity,nickname,first_name,last_name,gender,avatar,bio,birthday,website,is_public`, а также `role_name,role_description,role_level`. При полном доступе дополнительно возвращаются `login,email,is_verified,created_at,updated_at,is_deleted,deleted_at`. Пароль не возвращается. Флаги и даты одиночного профиля не приводятся контроллером к типам и могут приходить как строки БД; поля профиля могут быть null. Чужой ограниченный профиль должен быть публичным, активным и неудалённым.

## GET /v1/users

Доступ: пользователь. Список с фильтрами.

| Query | Тип | По умолчанию / смысл |
| --- | --- | --- |
| page | int | 1, минимум 1 |
| limit | int | 10, диапазон 10–100 |
| search | string | Поиск; поиск по email доступен ролям m/a/su |
| role | string | u/m/a/su; неизвестное значение игнорируется |
| is_active, is_banned | boolean/0/1 | Фильтр состояния, если передан |
| is_deleted | boolean/0/1 | Учитывается только для m/a/su |

Пример: `GET /api/v1/users?page=1&limit=20&role=u&is_active=1`.

200: `{items:[user],meta:{current_page,per_page,total,total_pages,has_next,has_prev}}`. Для обычных пользователей из списка удаляется `email`. Параметр `offset` не используется: общий обработчик вычисляет его из `page`.

Ошибки: 401 — авторизация; 500 — ошибка списка.

## GET /v1/users/me

Доступ: пользователь. Без параметров. Полные данные собственного аккаунта и профиля.

Пример: `GET /api/v1/users/me`. 200: объект пользователя в `data`. Ошибка: 401 — авторизация.

## GET /v1/users/{user_id}

Доступ: пользователь. `user_id` — обязательный int в пути.

Пример: `GET /api/v1/users/12`. 200: доступный вызывающему объект пользователя; 404 — профиль отсутствует или недоступен. Несмотря на комментарий внутри контроллера, маршрут требует авторизации.

## PATCH /v1/users/me

Доступ: пользователь. Частичное обновление своего аккаунта и профиля, в одной транзакции.

| JSON-поле | Тип / ограничение |
| --- | --- |
| email | string, корректный уникальный email |
| nickname | string, проверка уникальности |
| first_name, last_name, bio | string |
| gender | string: male/female/other; допускается пустое значение |
| birthday | string в формате Y-m-d; допускается пустое значение |
| website | string, URL с http:// или https://; допускается пустое значение |
| is_public | scalar, рекомендуется boolean |

Все поля необязательны, но нужно передать хотя бы одно разрешённое. Неизвестные поля отбрасываются. `avatar`, пароль, роль и логин этим методом не меняются.

Пример: `PATCH /api/v1/users/me`, тело `{"first_name":"Иван","is_public":true}`.

200: сообщение `Profile updated`, без `data`. Ошибки: 400 — нет допустимых полей; 422 — типы/форматы; 409 — занятый email или nickname; 401 — авторизация; 500 — ошибка сохранения.

## DELETE /v1/users/me

Доступ: пользователь. Удаляет свой аккаунт через сервис управления пользователями. Без тела.

Пример: `DELETE /api/v1/users/me`. 200: сообщение `User account deleted`, без `data`.

Ошибки: 401 — авторизация; 500 — ошибка удаления.

## POST /v1/users/me/avatar

Доступ: пользователь. `multipart/form-data`, обязательное файловое поле **avatar**.

Пример: `curl https://dev.roocms.com/api/v1/users/me/avatar -H "Authorization: Bearer <access_token>" -F "avatar=@avatar.jpg"`.

201: `{"message":"Avatar uploaded successfully","avatar_path":"<path>"}` в `data`.

Ошибки: 400 — файл отсутствует, ошибка загрузки или отклонён сервисом; 401 — авторизация; 500 — внутренняя ошибка. Ограничения изображения определяются сервисом и настройками обработки аватаров.

## DELETE /v1/users/me/avatar

Доступ: пользователь. Без тела.

Пример: `DELETE /api/v1/users/me/avatar`. 200: `{"message":"Avatar deleted successfully"}` в `data`.

Ошибки: 404 — аватара нет; 401 — авторизация; 500 — ошибка удаления.

## POST /v1/users/me/verify-email

Доступ: пользователь. Отправляет письмо подтверждения. Без тела.

Пример: `POST /api/v1/users/me/verify-email`. 200: сообщение `Verification email sent` и данные сервиса отправки.

Ошибки: 400 — email уже подтверждён; 401 — авторизация; 500 — ошибка отправки.

## GET /v1/users/verify-email/{verification_code}

Доступ: публичный. `verification_code` — обязательная строка из письма.

Пример: `GET /api/v1/users/verify-email/<verification_code>`. 200: сообщение `Email verified successfully`, без `data`.

Ошибки: недействительный код — код и сообщение сервиса; 500 — внутренняя ошибка.
