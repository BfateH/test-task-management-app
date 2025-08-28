## Тестовое задание Управление задачами с API
**Для запуска проекта**
- composer install
- Настроить .env (по сути просто продублировать .env.example)
- php artisan migrate
- php artisan key:generate
- php artisan serve

## Краткая документация API для управления задачами

Базовый URL
http://localhost/api

## Эндпоинты API
**Аутентификация**
- POST /api/login
- POST /api/logout

**Работа с задачами**
- GET /api/tasks
- GET /api/tasks?status=new&executor_id=1&producer_id=2&created_from=2023-01-01&created_to=2023-12-31&due_date_from=2023-06-01&due_date_to=2023-06-30&actual_date_from=2023-07-01&actual_date_to=2023-07-31&name=Важная задача&description=срочно&in_archive=0
- GET /api/tasks/create
- POST /api/tasks
- GET /api/tasks/1/edit
- PATCH /api/tasks/1
- PATCH /api/tasks/1/change-status
- GET /api/tasks/1/next-status
- GET /api/tasks/1/archive
- DELETE /api/tasks/1

**Для работы с задачами через API нужно передавать заголовок Authorization: Bearer ваш_токен_авторизации
Который выдается при POST /api/login**

## Создано так же мини-приложение без API 
Находится на главной /

**Дополнительно**
- При тестировании через Postman или другую программу добавить заголовок Accept application/json
- Используется база данных Sqlite
