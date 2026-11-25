# Auth

[Общие правила и оглавление](../../README.md). Реализация: [AuthController](../controller_auth.php).

## POST /v1/auth/login

Доступ: публичный. Вход по логину и паролю.

JSON: обязательные строки `login`, `password`. Пример: `{"login":"demo","password":"example-password"}`.

200: `{access_token, refresh_token, token_type:"Bearer", expires_in, refresh_expires_in, user:{user_id,role,login,email,is_verified}}`. Сроки — в секундах.

Ошибки: 422 — отсутствующие поля или неверные типы; 401 — неверные учётные данные; 403 — неактивная или заблокированная учётная запись; 500 — ошибка входа.

## POST /v1/auth/register

Доступ: публичный. Создаёт учётную запись, выдаёт токены и отправляет приветственное письмо.

JSON: обязательные строки `login`, `email`, `password`; необязательная строка `password_confirmation`, совпадающая с паролем. Пример: `{"login":"demo","email":"demo@example.com","password":"example-password","password_confirmation":"example-password"}`.

201: объект токенов и пользователя той же формы, что при входе. Проверки регистрации и минимальная длина пароля берутся из существующих сервисов и настроек.

Ошибки: 422 — обязательные поля, типы, несовпадение подтверждения; 409 — конфликт регистрационных данных; 400 — требования к паролю; 500 — ошибка регистрации.

## POST /v1/auth/refresh

Доступ: публичный, вместо access token нужен refresh token в теле.

JSON: обязательная строка `refresh_token`. Пример: `{"refresh_token":"<refresh_token>"}`.

200: `{access_token,refresh_token,token_type:"Bearer",expires_in,refresh_expires_in}`. Клиент должен сохранять возвращённые токены.

Ошибки: 400 — токен отсутствует; 422 — неверный тип; недействительность токена и состояние аккаунта возвращают код из сервиса; 500 — внутренняя ошибка.

## POST /v1/auth/logout

Доступ: пользователь. Отзывает текущий access token. Тело не требуется.

Пример: `POST /api/v1/auth/logout` с Bearer-заголовком. 200: `{"success":true}` внутри общего `data`, сообщение `Logout successful`.

Ошибки: 401 — нет действующего access token; 500 — ошибка отзыва.

## POST /v1/auth/logout/all

Доступ: пользователь. Отзывает все токены пользователя. Тело не требуется.

Пример: `POST /api/v1/auth/logout/all` с Bearer-заголовком. 200: сообщение `Logout all devices successful`, без `data`.

Ошибки: 401 — авторизация; 500 — ошибка отзыва.

## POST /v1/auth/refresh/revoke

Доступ: пользователь. Отзывает конкретный refresh token текущего пользователя.

JSON: обязательная строка `refresh_token`. Пример: `{"refresh_token":"<refresh_token>"}`.

200: сообщение `Refresh token revoked`, без `data`. Ошибки: 400 — токен не передан; 422 — неверный тип; 401 — авторизация; 500 — ошибка отзыва.

## PUT /v1/auth/password

Доступ: пользователь. Меняет собственный пароль, отзывает токены; после успеха нужен повторный вход.

JSON: обязательные строки `current_password`, `new_password`; необязательная `new_password_confirmation`. Пример: `{"current_password":"old-example-password","new_password":"new-example-password"}`.

200: сообщение `Password updated successfully. Please login again.`, без `data`.

Ошибки: 422 — обязательные поля или типы; 400 — несовпадение подтверждения или требования к новому паролю; другие ошибки проверки текущего пароля — по коду сервиса; 401 — авторизация; 500 — внутренняя ошибка.

## POST /v1/auth/password/recovery

Доступ: публичный. Отправляет код восстановления на email.

JSON: обязательная строка `email`. Пример: `{"email":"demo@example.com"}`.

200: сообщение `Recovery code sent to your email`, `data` — пустой массив, а в DEBUGMODE для существующего пользователя может содержать `recovery_code`. Ответ для неизвестного email также успешный.

Ошибки: 400 — отсутствующий или некорректный email; 422 — неверный тип; 500 — ошибка отправки.

## POST /v1/auth/password/reset

Доступ: публичный. Меняет пароль по полученному коду.

JSON: обязательные строки `token`, `password`; необязательная `password_confirmation`. `token` — код восстановления. Пример: `{"token":"<recovery_code>","password":"new-example-password"}`.

200: сообщение `Password reset successfully`, без `data`; существующие токены отзываются.

Ошибки: 422 — обязательные поля или типы; 400 — неверный/просроченный код, несовпадение подтверждения или требования к паролю; 429 — исчерпаны попытки; 500 — внутренняя ошибка.
