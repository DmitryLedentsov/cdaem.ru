Yii2 CDAEM.RU
=============

Сдаём.ру - это сайт, посвященный краткосрочной аренде недвижимости всей России и СНГ. 
Через сайт Вы можете забронировать квартиру на час, квартиру на ночь и квартиру на сутки, 
так же подобрать коттедж на сутки и на выходные.


Установка проекта
=================

Приложение работает с помощью [Docker](https://docker.com) и [Docker Compose](https://docs.docker.com/compose), 
поэтому необходимо установить данные утилиты.

При первом запуске приложения необходимо ввести команду:

```
make bootstrap
```

> Внимание, данная команда запускается только один раз.

Далее используются команды:

```
make start
make stop
make restart
```

Список всех команд можно посмотреть в Makefile.

>>> Для решения конфликта с портами (например 80) используется файл docker-compose.override.yml

Тестовый домен: **http://cdaem.loc** и **http://control.cdaem.loc**.


Database (local access)
=======================

 - MYSQL_ROOT_PASSWORD=cdaemru
 - MYSQL_DATABASE=cdaem.ru
 - MYSQL_USER=cdaemru
 - MYSQL_PASSWORD=cdaemru


Composer
========

Доступ к пакетному менеджеру предоставляется через команду:

```make composer```

```make composer cmd=install```


Yii
===

Доступ к консольным утилитам предоставляется через команду:

```make php-yii```

```make php-yii cmd=migrate```


# CRON-задания

Стартую автоматически и прописываются в файле `docker/cron`.
После добавления новых команд необходимо выполнить рестарт.

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