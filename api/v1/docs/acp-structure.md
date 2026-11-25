# ACP Structure

[Общие правила и оглавление](../../README.md). Реализация: [ACPStructureController](../controller_acpStructure.php), [StructureManageService](../../../roocms/services/structureManage.php).

Все методы требуют доступа администратора.

## GET /v1/acp/structure

Query: `status` string draft/active/inactive; `parent_id` int — непосредственный родитель; `search` string — подстрока title/slug; `limit` int 1–200, по умолчанию 50; `offset` int >= 0, по умолчанию 0. Все параметры необязательны.

Пример: `GET /api/v1/acp/structure?limit=50&offset=0`.

200: `{pages:[page],pagination:{total,limit,offset,pages,current_page},filters:{status,parent_id,search}}`. Порядок наследуется от готового дерева: родитель перед потомком, исходная сортировка по sort. Фильтры применяются до пагинации, неизвестный status игнорируется.

Объект страницы: `id,slug,parent_id,status,nav,title,meta_title,meta_description,meta_keywords,noindex,page_type,sort,childs,created_at,updated_at,published_at,is_published`. ID, sort, childs, даты — числа, флаги — boolean. В списке дополнительно присутствуют `level` и `structure_path`, например `/services/design`. Путь обозначает вложенность; фактический URL сайта по-прежнему строится по slug.

Ошибка контроллера: 500 — ошибка списка. Сервис также может вернуть пустой список при внутренней ошибке.

## GET /v1/acp/structure/{id}

Обязательный положительный int `id`. Пример: `GET /api/v1/acp/structure/2`.

200: объект страницы для редактирования. `level` и `structure_path` не гарантируются в одиночном ответе: они добавляются только если присутствуют в исходных данных.

Ошибки: 400 — неверный id; 404 — страница отсутствует; 500 — ошибка чтения.

## POST /v1/acp/structure

Создаёт страницу. JSON:

| Поле | Тип | Обязательность / значение по умолчанию |
| --- | --- | --- |
| slug | string | Обязательно, непустое, проверка уникальности и формата |
| title | string | Обязательно, непустое |
| parent_id | int >= 1 | 1; родитель должен существовать |
| status | string | draft; draft/active/inactive |
| page_type | string | page; page/feed |
| nav, noindex | boolean/0/1 | false |
| meta_title, meta_description, meta_keywords | string | Необязательные |
| sort | int | 100 |
| published_at | string даты | Необязательно; преобразуется strtotime, в ответе timestamp |

Пример: `POST /api/v1/acp/structure`, тело `{"slug":"new-page","title":"Новая страница","parent_id":1,"status":"draft"}`.

201: созданная страница. Ошибки: 400 — данные не прошли проверки или исключение сервиса; 500 — страница не создана.

## PUT /v1/acp/structure/{id}

Обязательный положительный int `id`. Частично обновляет поля из таблицы создания; пропущенные поля не изменяются.

Пример: `PUT /api/v1/acp/structure/2`, тело `{"title":"О компании","nav":true}`.

200: обновлённая страница. Главная (ID 1 / slug index) должна сохранять slug index и parent_id 1. Страницу нельзя переносить под себя или потомка.

Ошибки: 400 — неверный id/значения/запрещённый перенос; 404 — страница отсутствует; исключения сервиса здесь возвращаются как 400.

## DELETE /v1/acp/structure/{id}

Обязательный положительный int `id`, без тела. Пример: `DELETE /api/v1/acp/structure/2`.

200: `{message:"Page deleted successfully",deleted_id:2}`. Ошибки: 400 — неверный id или страница имеет потомков; 403 — удаление главной ID 1; 404 — страница отсутствует. Другие исключения удаления возвращаются как 400.

## PATCH /v1/acp/structure/{id}/status

Обязательный положительный int `id`. JSON: обязательная строка `status`: draft/active/inactive.

Пример: `PATCH /api/v1/acp/structure/2/status`, тело `{"status":"active"}`.

200: `{message:"Status updated successfully",id,status}`. Ошибки: 400 — неверный id/статус; 404 — страница не найдена или изменение не выполнено сервисом; 500 — ошибка контроллера.

## PUT /v1/acp/structure/reorder

JSON: обязательный массив `pages`, каждый элемент содержит `id` и `sort`. Меняет только порядок, не родителя.

Пример: `PUT /api/v1/acp/structure/reorder`, тело `{"pages":[{"id":2,"sort":10},{"id":3,"sort":20}]}`.

200: `{message:"Pages reordered successfully",updated_count:2}`.

Ошибки: 400 — нет массива или обязательных полей элемента; 404 — неизвестная страница; 500 — ошибка записи.
