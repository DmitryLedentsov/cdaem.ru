Yii2 CDAEM.RU
=============

Сдаём.ру - это сайт, посвященный краткосрочной аренде недвижимости всей России и СНГ. 
Через сайт Вы можете забронировать квартиру на час, квартиру на ночь и квартиру на сутки, 
так же подобрать коттедж на сутки и на выходные.


Требования
=========
Минимальные системные требования для работы приложения PHP 5.6


Структура
=========
```
apps/           Приложения
    backend/         Панель управления
    console/         Консольные команды
    frontend/        Основное приложение
    chat/            Приложение для чата. Использует node.js
common/          Общее приложение
environments/    Окружения
grunt/           Сборщик клиента
tests/           Тесты (не используются)
vendor/          Некоторые сторонние библиотеки
```


Установка проекта
=================

Установка YII2
composer install
php yii init 
php yii migrate

# Запустить cron задания

```
*/10 * * * * php /app/yii service/processes
```

```
0 */1 * * * php /app/yii service/calculate-position
```

```
0 */1 * * * php /app/yii partners/collector/reservation-verify-payment
```

```
0 */1 * * * php /app/yii partners/collector/reservation-verify-failure
```

```
0 0 1 * * php /app/yii agency/collector/images
```

```
0 0 1 * * php /app/yii partners/collector/images
```

```
0 0 * * * php /app/yii partners/collector/reservation
```

```
*/10 * * * * php /app/yii partners/calendar
```


**service/processes**

    ЗАПУСК РАЗ В 10 МИНУТ
    Сценарий достает все записи из специальной таблицы сервисов c полем process != 1, проверяет условие активности сервиса,
    например сравнением дат и запускает или останавливает определенный процесс сервиса.

**service/calculate-position**

    ЗАПУСК РАЗ В 1 ЧАС
    Сценарий пересчитывает все позиции объявлений

**partners/collector/reservation-verify-payment**

    ЗАПУСК РАЗ В 1 ЧАС
    Верификация оплаты подтверждения заявки

**partners/collector/reservation-verify-failure**

    ЗАПУСК РАЗ В 1 ЧАС
    Возврат средств по заявкам "Незаезд"

**agency/collector/images**

    ЗАПУСК РАЗ В 1 МЕСЯЦ
    Сценарий очищает не нужный мусор в виде картинок в разделах агенства

**partners/collector/images**

    ЗАПУСК РАЗ В 1 МЕСЯЦ
    Сценарий очищает не нужный мусор в виде картинок в разделах доски объявлений

**partners/collector/reservation**

    ЗАПУСК РАЗ В 1 ДЕНЬ
    Осуществляет поиск старых заявок, проставляет необходимые статусы старым заявкам

**partners/calendar**

    ЗАПУСК РАЗ В 10 МИНУТ
    Осуществляет поиск необходимых записей в календаре и устанавливает или снимает флаг "Сейчас свободно"



# Symlink
ln -s /home/cdaemru/www/dev/apps/frontend/web/ion.sound ion.sound



# Резервное копирование базы данных
# Production - бэкап базы данных (каждые 6 часов)
0 */6 * * * root mysqldump -uroot -hlocalhost cdaem.ru  | gzip -c > /home/cdaemru/mysql_backup/b-$(date -u +\%Y-\%m-\%d_\%H-\%M).gz > /dev/null 2>&1





Настройка чата
==============

Создать файл конфигурации apps/chat/mysql.json

```{
       "host": "localhost",
       "user": "root",
       "password": "",
       "database": "cdaemru"
   }
```

```
/opt/nodejs/lib/node_modules/forever/bin/forever start server.js
```



Schema
======
```
    common/schema/ - Дополнительные данные для базы данных (MySql/Sql).
```


Модули
======

```
    agency      - 
    realty      -
    articles    - 
    callback    - 
    geo         - 
    helpdesk    - 
    merchant    - 
    office      - 
    pages       - 
    partners    - 
    reviews     - 
    users       - 
```


Дополнительно
=============
В админке прокинуть симлинк в директории apps/backend/web/ion.sound:
```
ln -s /path/to/apps/frontend/web/ion.sound ion.sound
```


Чистка логов
============
DELETE FROM log WHERE category = 'Twig_Error_Runtime' ;
DELETE FROM log WHERE category = 'yii\\web\\HttpException:404';
DELETE FROM log WHERE category = 'yii\debug\Module::checkAccess';
DELETE FROM log WHERE category = 'application';
DELETE FROM log WHERE category = 'yii\\base\\ErrorException:1';
DELETE FROM log WHERE category = 'yii\\base\\ErrorException:8';
DELETE FROM log WHERE category = 'yii\\base\\UnknownMethodException';
DELETE FROM log WHERE category = 'yii\\db\\Command::execute';
DELETE FROM log WHERE category = 'yii\\db\\Connection::open';
DELETE FROM log WHERE category = 'yii\\db\\Exception';
DELETE FROM log WHERE category = 'yii\\validators\\FileValidator::getSizeLimit';
DELETE FROM log WHERE category = 'yii\\validators\\FileValidator::validateValue';
DELETE FROM log WHERE category = 'yii\\web\\HttpException:400';
DELETE FROM log WHERE category = 'yii\\web\\HttpException:403';
DELETE FROM log WHERE category = 'yii\\web\\HttpException:404';
DELETE FROM log WHERE category = 'yii\\web\\Session::open';
DELETE FROM log WHERE category = 'yii\\web\\User::login';
DELETE FROM log WHERE category = 'yii\\web\\User::loginByCookie';
DELETE FROM log WHERE category = 'yii\\web\\User::logout';
```


SSL
============

server {
    listen 80;
    server_name cdaem.ru;
    return 301 https://$server_name$request_uri;  # enforce https
}

server {
    listen 80;
    server_name *.cdaem.ru;
    return 301 https://$host$request_uri;
}

server{

    server_name *.cdaem.ru cdaem.ru;

    listen 443;

    ssl on;
    ssl_certificate /var/ssl/ssl-bundle.crt;
    ssl_certificate_key /var/ssl/hit-start.key;

    #enables all versions of TLS, but not SSLv2 or 3 which are weak and now deprecated.
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;

    #Disables all weak ciphers
    ssl_ciphers "-------"
    ssl_prefer_server_ciphers on;

    ...
}