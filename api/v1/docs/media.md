# Media

[Общие правила и оглавление](../../README.md). Реализация: [MediaController](../controller_media.php), [FilesCommonService](../../../roocms/services/filesCommon.php).

Типы: image/document/video/audio/archive/other. Статусы модели: uploaded/processing/ready/error/deleted. Для публичных запросов токен необязателен; переданный Bearer-токен позволяет определить владельца и привилегии.

Объект media основан на записи БД: основные поля `id,uuid,original_name,filename,file_path,mime_type,file_size,media_type,extension,status,description,tags,created_at,updated_at`. Сервис добавляет `file_size_human,created_at_formatted,updated_at_formatted`; заполненные metadata декодируются из JSON. В одиночных ответах добавляется массив `variants`. Поля `user_id` и `metadata` доступны в зависимости от прав, как описано ниже; типы исходных полей БД не нормализуются контроллером.

## GET /v1/media

Доступ: публичный, расширенные фильтры для m/a/su.

| Query | Тип | По умолчанию / смысл |
| --- | --- | --- |
| page | int >= 1 | 1 |
| limit | int 10–100 | 10 |
| type | string | Необязательный тип из списка выше |
| status | string | Необязательный; см. ограничение ниже |
| user_id | int | Фильтр владельца, только для m/a/su |
| search | string | Необязательный поиск |

Пример: `GET /api/v1/media?type=image&page=1&limit=20`.

200: `{data:[media],pagination:{current_page,per_page,total,total_pages,has_next,has_prev},filters}` внутри общего data. Поэтому список находится в `data.data`.

Гости и обычные пользователи видят список только ready, без user_id и metadata; фильтр владельца для них снимается. Модератор и выше получают расширенные данные.

Ошибки: 400 — неверный type; 500 — ошибка списка. В текущем коде проверка переданного непустого status обращается к private-константе Files::STATUS_TYPES вне класса: такой запрос не следует считать рабочим фильтром до отдельного исправления.

## GET /v1/media/{id}

Доступ: публичный. Обязательный int `id`. Пример: `GET /api/v1/media/12`.

200: объект media в data. Владелец и m/a/su получают полные данные, включая user_id и metadata, и могут читать не-ready записи. Остальным доступны только ready, без этих двух полей.

Ошибки: 404 — файл отсутствует или недоступен; 500 — ошибка чтения.

## GET /v1/media/{id}/file

Доступ: публичный с теми же правилами видимости, что при чтении файла. Обязательный int `id`; необязательный query `variant`: thumbnail/large/original.

Пример: `GET /api/v1/media/12/file?variant=thumbnail`.

200: бинарное содержимое, Content-Type файла, Content-Disposition attachment, Content-Length, ETag. Если указанный вариант не найден либо неизвестен, используется основной файл. Это не JSON-ответ.

Ошибки: 404 — запись/файл недоступны; 500 — ошибка скачивания.

## POST /v1/media/upload

Доступ: пользователь. Multipart-параметры:

| Поле | Тип | Обязательность / значение |
| --- | --- | --- |
| file | file | Обязательно |
| description | string | Необязательно |
| tags | string | Необязательно |
| entity_type | string | Необязательно, для привязки |
| entity_id | int | Необязательно, используется вместе с entity_type |
| relationship_type | string | attachment |

Пример: `curl https://dev.roocms.com/api/v1/media/upload -H "Authorization: Bearer <access_token>" -F "file=@photo.jpg" -F "description=Фото"`.

201: `{message:"File uploaded successfully",media:media}` в data. Допустимость файла определяется реальным содержимым и существующим сервисом загрузки.

Ошибки: 400 — файл отсутствует/ошибка загрузки/проверки сервиса; 401 — авторизация; 500 — внутренняя ошибка.

## PUT /v1/media/{id}

Доступ: пользователь, изменение владельцем либо m/a/su. Обязательный int `id`.

JSON: разрешены `original_name`, `description`, `tags`, `status`; остальные поля отбрасываются. Передайте хотя бы одно допустимое поле. Строковые поля соответствуют метаданным файла.

Пример: `PUT /api/v1/media/12`, тело `{"description":"Новое описание"}`.

200: `{message:"Media updated successfully",media:media}`.

Ошибки: 400 — нет допустимых полей; 403 — нет права изменения; 404 — нет записи; 500 — ошибка обновления. Изменение status затронуто тем же обращением к private-константе, что и фильтр списка; этот недостаток текущей реализации здесь только зафиксирован.

## DELETE /v1/media/{id}

Доступ: пользователь, владелец либо m/a/su. Обязательный int `id`, без тела.

Пример: `DELETE /api/v1/media/12`. 200: `{message:"Media deleted successfully",id:12}`.

Ошибки: 401 — авторизация; 403 — нет права удаления; 404 — нет записи; 500 — ошибка удаления.
