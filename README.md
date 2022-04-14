# Files Microservice

Микросервис файлов — микросервис, предназначенный для хранения файлов.

## Использование

1. Склонируйте репозиторий на локальную машину
    ```bash
    $ git clone https://git.cic.kz/micro/file-service.git
    ```

2. Установите все зависимости
   ```bash
   $ composer install
   ```

3. Настройте файл переменных среды (.env) и [файл конфигураций Files-Microservice](#файл-конфигураций)

4. Запустите проект
    ```bash
   $ php -S localhost:* -t ./public
    ```

## Файл конфигураций

[Файл конфигураций](https://gitlab.com/Wedyarit/files-microservice/-/blob/master/config/app.php) содержит в себе перечисления:

- [ALLOWED_EXTENSIONS](#разрешенные-расширения) (разрешенные расширения)
- [MAX_SIZE](#максимальный-размер) (максимальный размер)

### Разрешенные расширения

Пример разрешенных разрешений для загрузки:

```php
    'ALLOWED_EXTENSIONS' => 'png,pdf,docx,gif,jpg',
```

### Максимальный размер

Пример максимального размера загружаемого файла (в килобайтах):

```php
    'MAX_SIZE' => 10240
```

## Файлы

Взаимодействие с файлами.

`POST`⠀⠀/file <br>
Создание файла<br>
Необходимое разрешение: `create-file`

``` 
header:
Accept application/json
Content-Type multipart/form-data

body:
file: File
delete_at: ?Timestamp 
```

<br>`GET`⠀⠀/file/{file_uuid} <br>
Получение информации о файле<br>
Необходимое разрешение: `view-file`

<br>`GET`⠀⠀/file/{file_uuid}/download <br>
Генерирует ответ для скачивания файла.<br>
Необходимое разрешение: `view-file`

<br>`GET`⠀⠀/file <br>
Получение полного списка файлов<br>
Необходимое разрешение: `view-files`

<br>`PUT|PATCH`⠀⠀/file/{file_uuid} <br>
Частичное обновление файла<br>
Необходимое разрешение: `update-file`
```json
{
  "delete_at": "Timestamp"
}
```

<br>`DELETE`⠀⠀/file/{file_uuid} <br>
Удаление файла<br>
Необходимое разрешение: `delete-file`

<br>

---
