# Structure

[Общие правила и оглавление](../../README.md). Реализация: [StructureController](../controller_structure.php), [StructureService](../../../roocms/services/structure.php).

Все методы публичные. Статусы страниц: `draft,active,inactive`; типы: `page,feed`. Публичность маршрута сама по себе не означает автоматический фильтр только опубликованных страниц.

## GET /v1/structure/tree

| Query | Тип | По умолчанию |
| --- | --- | --- |
| parent_id | int >= 0 | 0 |
| max_level | int >= 0 | 0 — без ограничения |
| navigation | флаг наличия | отсутствует |
| published | флаг наличия | отсутствует |
| status | string | Без фильтра; принимаются draft/active/inactive |

Пример: `GET /api/v1/structure/tree?published=1&navigation=1`.

200: `{tree,count,filters:{parent_id,max_level,only_navigation,only_published,status_filter}}`. `tree` — последовательность страниц в порядке дерева, с `level`; JSON может содержать объект с ID в качестве ключей. Это не обязательно вложенные массивы children.

`navigation` и `published` включаются наличием непустого параметра, даже `=0`. `published` проверяет active и доступность страницы. Неизвестный status не применяется.

Ошибка: 500 — дерево не загружено.

## GET /v1/structure/page/{id}

Обязательный положительный int `id`. Пример: `GET /api/v1/structure/page/2`.

200: объект страницы (поля ниже). Ошибки: 400 — id <= 0; 404 — страница отсутствует; 500 — ошибка чтения.

## GET /v1/structure/page/slug/{slug}

Обязательная непустая строка `slug`. Пример: `GET /api/v1/structure/page/slug/about`.

200: объект страницы. Ошибки: 400 — пустой slug; 404 — нет страницы; 500 — ошибка чтения.

### Объект страницы

`id,parent_id,slug,status,title,meta_title,meta_description,meta_keywords,noindex,page_type,nav,sort,childs,level,access,created_at,updated_at,published_at,url,is_published`.

ID, sort, childs, level и даты — числа; `noindex,nav,access,is_published` — boolean. Даты — Unix timestamp. URL главной — `/`, остальные строятся по slug, например `/about`, независимо от вложенности в дереве.

Пример `data`:

```json
{"id":2,"parent_id":1,"slug":"about","status":"active","title":"О нас","meta_title":"О нас","meta_description":"","meta_keywords":"","noindex":false,"page_type":"page","nav":true,"sort":100,"childs":0,"level":1,"access":true,"created_at":1795600000,"updated_at":1795600000,"published_at":0,"url":"/about","is_published":true}
```

## GET /v1/structure/navigation

Query: `parent_id` int >= 0, по умолчанию 0; `max_level` int 1–10, по умолчанию 3.

Пример: `GET /api/v1/structure/navigation?max_level=3`.

200: `{navigation:[{id,title,slug,url,level,has_children,sort}],count,parent_id,max_level}`. Включает доступные active-страницы меню; элементы сортируются по sort. Ошибка: 500 — ошибка построения.

## GET /v1/structure/breadcrumbs/{id}

Обязательный положительный int `id`. Пример: `GET /api/v1/structure/breadcrumbs/2`.

200: `{breadcrumbs:[{title,slug,url,is_current}],count,page_id}`. Для дочерней страницы крошки идут от главной до текущей; для самой главной текущая реализация возвращает пустой список.

Ошибки: 400 — неверный id; 404 — нет страницы; 500 — ошибка крошек.

## GET /v1/structure/breadcrumbs/slug/{slug}

Обязательная непустая строка `slug`. Пример: `GET /api/v1/structure/breadcrumbs/slug/about`.

200: та же структура крошек, но вместо `page_id` возвращается `page_slug`. Ошибки: 400 — пустой slug; 404 — нет страницы; 500 — ошибка крошек.

## GET /v1/structure/seo/{id}

Обязательный положительный int `id`. Пример: `GET /api/v1/structure/seo/2`.

200: `{seo:{title,description,keywords,noindex,canonical,og_title,og_description,og_type},page_id}`. При отсутствии описания берётся site_description, og_type — website.

Ошибки: 400 — неверный id; 404 — нет страницы; 500 — ошибка SEO.

## GET /v1/structure/seo/slug/{slug}

Обязательная непустая строка `slug`. Пример: `GET /api/v1/structure/seo/slug/about`.

200: тот же объект SEO, с `page_slug` вместо `page_id`. Ошибки: 400 — пустой slug; 404 — нет страницы; 500 — ошибка SEO.

## GET /v1/structure/current

Query: необязательный `page` — int ID или string slug. Без него используется текущая страница контекста запроса.

Пример: `GET /api/v1/structure/current?page=about`.

200: `{current_page:page,page_identifier}`. Ошибки: 400 — указанный идентификатор не загружен; 500 — ошибка чтения.

## GET /v1/structure/search

Query: обязательный `query` string минимум 2 байта после trim; необязательные `type` (page/feed; иное игнорируется), `limit` (int 1–100, по умолчанию 20).

Пример: `GET /api/v1/structure/search?query=about&limit=20`.

200: `{results:[{id,slug,title,page_type,url,nav,level}],count,query,type_filter,limit}`. Поиск без учёта регистра по title, slug и SEO-полям, без автоматического ограничения опубликованными.

Ошибки: 400 — короткий/пустой запрос; 500 — ошибка поиска.

## GET /v1/structure/status/{status}

Обязательный `status`: draft/active/inactive. Пример: `GET /api/v1/structure/status/active`.

200: `{pages:[page],count,status}`. Ошибки: 400 — неизвестный статус; 500 — ошибка чтения.
