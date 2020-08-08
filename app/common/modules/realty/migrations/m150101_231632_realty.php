<?php

use yii\db\Schema;
use yii\db\Migration;

class m150101_231632_realty extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Apartments Rent Type
        $this->createTable('{{%realty_rent_type}}', [
            'rent_type_id' => Schema::TYPE_PK . ' COMMENT "№"',
            'name' => 'varchar(255) NOT NULL COMMENT "Название"',
            'short_name' => 'varchar(255) NOT NULL COMMENT "Короткое название"',
            'slug' => 'varchar(255) NOT NULL COMMENT "Транслитерация"',
            'icons' => 'text COMMENT "иконки в JSON формате"',
            'short_description' => 'text COMMENT "Краткое описание"',
            'meta_title' => 'text NOT NULL COMMENT "Meta Title"',
            'meta_description' => 'text NOT NULL COMMENT "Meta Description"',
            'meta_keywords' => 'text NOT NULL COMMENT "Meta Keywords"',
            'agency_rules' => 'text COMMENT "Правила заселения агенства"',
            'visible' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Отображается на сайте"',
            'sort' => 'tinyint NOT NULL DEFAULT 1 COMMENT "Сортировка"',
        ], $tableOptions . ' COMMENT = "Типы аренды апартаментов"');


        $this->batchInsert('{{%realty_rent_type}}', ['rent_type_id', 'name', 'short_name', 'slug', 'short_description', 'meta_title', 'meta_description', 'meta_keywords', 'visible', 'sort', 'icons'], [
            [
                1,
                'Квартира на час',
                'На час',
                'kvartira_na_chas',
                '<h2><strong>Квартиры на часы в Москве</strong></h2><p>Квартира на час: Специалисты Сдаём.ру подберут вам квартиру на час, квартиру на сутки или ночь по выгодным для Вас условиям!<br />Бронирование квартир: <strong>тел .+7(903)799-1-799</strong></p>',
                'Огромный выбор для аренды квартира на Час, на Ночь или квартира на Сутки. Так же можно арендовать  коттеджи  на сутки. Звоните - мы Вам поможем с поиском жилья!',
                'Сдаём.ру посвящён помощи в аренде квартир на сутки для приезжих, квартира на час для влюблённых пар, а так же  квартира на ночь для тех кто экономит деньги. Наш сайт и доска объявлений, поможет Вам в аренде квартир в любом округе Москвы и на различное время краткосрочной аренды на сутки, ночь, час.',
                'квартира на час, снять квартиру на часы,снять квартиру на час, квартира на часы сутки,квартира на час сутки,квартира на час в москве, аренда квартир  на сутки,снять квартиру на сутки москва,сниму квартиру на сутки недорого,квартиры на сутки в москве,квартира на сутки москва недорого,аренда квартир на сутки москва,доска объявлений,бесплатные доски.',
                1,
                1,
                '{"small-white":"<span class=\"icon-rent-type icon-rent-type-small-white-hour\"><\/span>","small-blue":"<span class=\"icon-rent-type icon-rent-type-small-blue-hour\"><\/span>","big-gray":"<span class=\"icon-rent-type icon-rent-type-big-gray-hour\"><\/span>","big-white":"<span class=\"icon-rent-type icon-rent-type-big-white-hour\"><\/span>"}',
            ],
            [
                2,
                'Квартира на сутки',
                'На сутки',
                'kvartira_na_sutki',
                '<h2><strong>Квартиры на сутки в Москве</strong></h2><p>Специалисты Сдаём.ру подберут вам квартиру на час, квартиру на сутки или ночь по выгодным для Вас условиям!<br />Бронирование квартир: <strong>тел. +7(903)799-1-799</strong></p>',
                'Аренда квартир на сутки от Cдаём.ру. Поможем Вам  с посуточной аренды  квартир в любом округе Москвы, по привлекательным ценам.',
                'Сдаём.ру подберёт и забронирует Вам - квартира на сутки, качественная аренда квартир на сутки от собственников, удобное территориальное расположения и привлекательная цена  делает наши  посуточные квартиры в Москве более привлекательными.',
                'квартира на сутки,снять квартиру на сутки,квартиры на сутки в москве,квартира на сутки недорого,квартира на сутки без посредников,аренда квартир на сутки,квартиры посуточно,снять квартиру посуточно,аренда квартир посуточно,квартиры посуточно однокомнатные,1 квартира посуточно',
                1,
                1,
                '{"small-white":"<span class=\"icon-rent-type icon-rent-type-small-white-day\"><\/span>","small-blue":"<span class=\"icon-rent-type icon-rent-type-small-blue-day\"><\/span>","big-gray":"<span class=\"icon-rent-type icon-rent-type-big-gray-day\"><\/span>","big-white":"<span class=\"icon-rent-type icon-rent-type-big-white-day\"><\/span>"}',
            ],
            [
                3,
                'Квартира на ночь',
                'На ночь',
                'na_noch',
                '<h2><strong>Квартиры на ночь в Москве</strong></h2><p>Специалисты Сдаём.ру подберут вам квартиру на час, квартиру на сутки или ночь по выгодным для Вас условиям!<br/>Бронирование квартир: <strong>тел .+7(903)799-1-799</strong></p>',
                'Аренда квартир на ночь от Cдаём.ру. Поможем Вам  снять квартиру на ночь, в любом округе Москвы по привлекательным ценам',
                'Сдаём.ру подберёт и забронирует Вам - квартира на ночь. Мы поможем снять квартиру на ночь в Москве. Удобное территориальное расположения и адекватная цена делает аренду квартир на ночь более востребоваными.',
                'квартира на ночь,снять квартиру на ночь,квартира в москве на ночь,квартира на ночь недорого,снять квартиру на ночь москва ,квартира на ночь в москве,квартиры на ночь,квартира ночь,квартиры ночь',
                1,
                1,
                '{"small-white":"<span class=\"icon-rent-type icon-rent-type-small-white-night\"><\/span>","small-blue":"<span class=\"icon-rent-type icon-rent-type-small-blue-night\"><\/span>","big-gray":"<span class=\"icon-rent-type icon-rent-type-big-gray-night\"><\/span>","big-white":"<span class=\"icon-rent-type icon-rent-type-big-white-night\"><\/span>"}',
            ],
            [
                4,
                'Квартира на месяц',
                'На месяц',
                'kvartira_na_mesyac',
                '<h2><strong>Квартиры на месяц</strong></h2>\r\n<p>\r\nСпециалисты Сдаём.ру подберут вам квартиру на месяц, квартиру на сутки или ночь по выгодным для Вас условиям!<br />\r\nБронирование квартир: <strong>тел. +7(903)799-1-799</strong></p>',
                'Аренда квартир на месяц от Cдаём.ру. Поможем Вам  с арендой  квартир на месяц в любом округе Москвы, по привлекательным ценам.',
                'Сдаём.ру подберёт и забронирует Вам - квартира на месяц, качественная аренда квартир на месяц от собственников, удобное территориальное расположения и привлекательная цена  делает наши  посуточные квартиры в Москве более привлекательными.',
                'квартира на месяц, квартира на месяц в Москве, квартира месяц, снять квартиру на месяц, месячная аренда квартиры',
                1,
                1,
                '{"small-white":"<span class=\"icon-rent-type icon-rent-type-small-white-month\"><\/span>","small-blue":"<span class=\"icon-rent-type icon-rent-type-small-blue-month\"><\/span>","big-gray":"<span class=\"icon-rent-type icon-rent-type-big-gray-month\"><\/span>","big-white":"<span class=\"icon-rent-type icon-rent-type-big-white-month\"><\/span>"}',
            ],
            [
                5,
                'Комната на сутки',
                'На сутки',
                'komnata_na_sutki',
                '<h2><strong>Комнаты на сутки</strong></h2>\r\n<p>\r\nСпециалисты Сдаём.ру подберут вам комнату на сутки, квартиру на сутки или ночь по выгодным для Вас условиям!<br />\r\nБронирование квартир: <strong>тел. +7(903)799-1-799</strong></p>',
                'Аренда комнаты на сутки от Cдаём.ру. Поможем Вам  с посуточной аренды  комнат в любом округе Москвы, по привлекательным ценам.',
                'Сдаём.ру подберёт и забронирует Вам - комнату на сутки, качественная аренда комнат на сутки от собственников, удобное территориальное расположения и привлекательная цена  делает наши  посуточные комнаты в Москве более привлекательными.',
                'комната на сутки, комната на сутки в Москве, комната сутки, снять комнату на сутки, посуточная аренда комнат',
                1,
                1,
                '',
            ],
            [
                6,
                'койко-место',
                'койко-место',
                'koyko_mesto',
                'койко-место',
                'койко-место',
                'койко-место',
                'койко-место',
                1,
                1,
                '',
            ],
            [
                7,
                'Загородный дом',
                'Коттедж',
                'zagorodniy_dom',
                'Наш коттедж наполнен оригинальными и очень декоративными предметами интерьера, каждое помещение индивидуально и представляет особый художественный интерес.<br />\r\nДом имеет 3 спальни, 3 санузла, кухню с просторной столовой, гостиную с камином, рабочий кабинет, спортивный зал, игровую комнату и полностью оборудован для спокойного загородного отдыха.<br />\r\nМы будем счастливы видеть Вас и Ваших близких в числе своих персональных гостей!<br />\r\nК услугам гостей у нас:<br />\r\n<ol>\r\n	<li>\r\n		Сауна со свежим бельем и всеми принадлежностями</li>\r\n	<li>\r\n		Солярий</li>\r\n	<li>\r\n		Средневековые английские шахматы</li>\r\n	<li>\r\n		Бесплатный WI-FI и городской телефон</li>\r\n	<li>\r\n		Настольный теннис</li>\r\n	<li>\r\n		Постельное белье премиум-класса</li>\r\n	<li>\r\n		Подушки на выбор овечья шерсть или гречишная лузга</li>\r\n	<li>\r\n		Вода в спальнях</li>\r\n	<li>\r\n		Фены и сейфы в каждой спальне</li>\r\n	<li>\r\n		Косметика в ванных комнатах</li>\r\n	<li>\r\n		Поутюжим одежду и почистим обувь</li>\r\n	<li>\r\n		Помощь в приготовлении блюд и сервировке стола</li>\r\n	<li>\r\n		Подадим кофе и чай в любое время по Вашему желанию</li>\r\n	<li>\r\n		Доставка продуктов по Вашему заказу</li>\r\n	<li>\r\n		Полный набор бытовой техники и посуды</li>\r\n	<li>\r\n		Камин затопим для Вас в любое время по Вашему желанию</li>\r\n	<li>\r\n		Помощь в любых хозяйственных вопросах, например в переносе тяжестей</li>\r\n	<li>\r\n		Помощь в распаковке/упаковке багажа</li>\r\n	<li>\r\n		Ухоженный сад с уютной беседкой и фонтаном</li>\r\n	<li>\r\n		Мангал и помощь гостям в приготовлении блюд на открытом огне</li>\r\n	<li>\r\n		Детские саночки</li>\r\n	<li>\r\n		Организация персонального фейерверка по Вашему заказу</li>\r\n	<li>\r\n		Обслуживание круглосуточное</li>\r\n</ol>\r\n&nbsp;<br />\r\n<strong>Бутылка прекрасного вина в подарок!</strong><br />\r\nНаш дом пользуется большой популярностью у дизайнеров ювелирных изделий, меховых и свадебных салонов, а также у представителей шоу-бизнеса как уникальная декорация для проведения профессиональных фотосессий.<br />\r\n',
                'Загородный дом',
                'Сдаём.ру подберёт и забронирует Вам - квартира на сутки, аренда квартир на сутки, квартира сутки, посуточные квартиры, квартира на сутки в Москве,посуточные квартиры в Москве.',
                'квартира на сутки ,квартира сутки,квартира на сутки в Москве,аренда квартир на сутки,квартира на ночь,квартира на ночь в Москве,посуточная аренда,на сутки, доска бесплатных объявлений,доска объявлений',
                1,
                0,
                '',
            ],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%realty_rent_type}}');
    }
}
