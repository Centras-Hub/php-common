# PHP-Common

<b>PHP-Common</b> - пакет, который вмещает в себя общие функции всех микросервисов.

## Функционал

- Утилиты
    - Проверка и обработка исключений
    - Сообщения исключений
    - Оболочка над [Guzzle](https://laravel.com/docs/9.x/http-client)
    - Организация query-запросов [QueryBuilder](#query-builder)

## Содержание

- [Установка](#установка)
- [QueryBuilder](#query-builder)

## Установка

1. Создайте `auth.json` файл в корневой паке вашего проекта. Добавьте `access-token`. Подробнее узнать [здесь](https://docs.gitlab.com/ee/user/profile/personal_access_tokens.html). Не забудьте добавить файл `auth.json` в `gitignore`.
```json
  {
    "gitlab-token": {
        "git.cic.kz": "your_token"
    }
  }
```
2. Выполните команду:

```bash
composer config repositories.git.cic.kz/86 '{"type": "composer", "url": "https://git.cic.kz/api/v4/group/86/-/packages/composer/packages.json"}'
```

После выполнения команды в файле `composer.json` должна появится запись:
  ```json
    "repositories": [{
            "type" : "composer",
            "url"  : "https://git.cic.kz/api/v4/group/86/-/packages/composer/packages.json"
    }],
  ```
3. Установите пакет через [composer](https://getcomposer.org/):

  ```bash
    $ composer req centras/php-common:1.0.1
  ```

4. Перейдите в `bootstrap/app.php` и замените стандартный `Handler` на `phpcommon/Handler/Handler::class`:

  ```php
    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        phpcommon\Handler\Handler::class
    );
  ```

5. Зарегистрируйте глобальные Middleware в `bootstrap/app.php` (при необходимости)
  ```php
   $app->middleware([
        phpcommon\Middleware\AccessLevelMiddleware::class,
        phpcommon\Middleware\UserMiddleware::class
   ]);
  ```

6. Зарегистрируйте Schedule для Heartbeat в app/Console/Kernel.php:
  ```php
   $schedule->call(function () {
       Scheduler::heartbeat();
   })->everyMinute();
  ```
   _Не забудьте импортировать Scheduler (use phpcommon\Utils\Scheduler)_

## Query Builder

Конструктор запросов - утилита, предназначенная для фильтрации, пагинации, сортировки возвращаемых запросом данных. Конструктор принимает несколько статичных параметров.

### Колонки

Позволяют отбирать определенные колонки модели. В случае указания некорректной колонки, будет выведена соответствующее исключение.

Параметр: **columns**

- /users?columns=email,uuid,first_name

```json
"data": {
"email": "Emanuel.Schowalter@hotmail.com",
"uuid": "113eb3b3-2554-464d-9224-62e61c930f1a"
"first_name": "Emanuel"
}
```

### Колонки

Позволяют отбирать определенные колонки модели. В случае указания некорректной колонки, будет выведено соответствующее исключение.

Параметр: **columns**

- /users?columns=email,uuid,first_name

```json
{
  "email": "Emanuel.Schowalter@hotmail.com",
  "uuid": "113eb3b3-2554-464d-9224-62e61c930f1a"
  "first_name": "Emanuel"
}
```
<br>

Подобный функционал также осуществим и со связями-зависимостями.
- /users?columns=email,social_networks.name,social_networks.url

```json
{
  "email": "Emanuel.Schowalter@hotmail.com",
  "social_networks": [
    {
      "name": "Telegram",
      "url": "https://telegram.org"
    }
  ]
}
```


### Включения

Позволяют отбирать определенные зависимости модели. В случае указания некорректной связи, будет выведено соответствующее исключение.

Параметр: **includes**

- /users?includes=social_networks

```json
{
  "email": "Emanuel.Schowalter@hotmail.com",
  "uuid": "113eb3b3-2554-464d-9224-62e61c930f1a"
  "first_name": "Emanuel",
  "social_networks": [
    {
      "id": 1,
      "name": "Twitter",
      "url": "https://twitter.com/"
    }
  ]
}
```


### Лимиты

Лимиты позволяют определять количество записей на странице. Значение по умолчанию: 15 записей.

Параметр: **limit**

- /users?limit=5

### Страницы

Применяется с лимитами, определяет номер страницы. Позволяет разделять большой объем данных на страницы с фиксированным количеством записей на каждой. Значение по умолчанию: 15 записей.

Параметр: **page**

- /users?page=1

### Сортировка

Позволяет определять порядок записей. 

Параметр: **order_by**

- /users?order_by=id:asc,uuid:desc

### Фильтрация

Позволяет фильтровать входные данные. 

Параметр: **динамический**
Ключом параметра является название колонки.

Примеры запросов фильтрации:
- /users?first_name=Emanuel
- /users?first_name!=Emanuel
- /users?first_name=*uel
- /users?first_name=Em*
- /users?first_name=\*an*
- /users?balance=1.546
- /users?balance=<2.55
- /users?balance=>1.699
- /users?balance<2.45
- /users?balance>1.324
- /users?first_name=[null]
- /users?first_name!=[null]
