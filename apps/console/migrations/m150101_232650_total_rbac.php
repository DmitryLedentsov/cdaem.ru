<?php

use yii\db\Migration;
use yii\db\Schema;

class m150101_232650_total_rbac extends Migration
{
    public function safeUp()
    {

        $auth = Yii::$app->authManager;

        // РАЗРЕШЕНИЯ
        # ---------------------------------------------------


        # АГЕНСТВО
        #=============================================================

        # --- Объявления // advert
        $permission = $auth->createPermission('agency-advert-view');
        $permission->description = '<b>Разрешить просмотр объявлений в агенстве</b><p>Разрешение на просмотр любых объявлений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-advert-create');
        $permission->description = '<b>Разрешить создавать объявления в агенстве</b><p>Разрешение на создание любых объявлений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-advert-update');
        $permission->description = '<b>Разрешить редактировать объявления в агенстве</b><p>Разрешение на редактирование любых объявлений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-advert-delete');
        $permission->description = '<b>Разрешить удалять объявления в агенстве</b><p>Разрешение на удаление любых объявлений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-advert-multi-update');
        $permission->description = '<b>Разрешить массовое редактирование объявлений</b><p>Разрешение на массовое редактирование любых объявлений в разделах агенства.</p>';
        $auth->add($permission);


        # --- Апартаменты // apartment
        $permission = $auth->createPermission('agency-apartment-view');
        $permission->description = '<b>Разрешить просмотр апартаментов в агенстве</b><p>Разрешение на просмотр любых апартаментов в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-apartment-create');
        $permission->description = '<b>Разрешить создавать апартаменты в агенстве</b><p>Разрешение на создание любых апартаментов в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-apartment-update');
        $permission->description = '<b>Разрешить редактировать апартаменты в агенстве</b><p>Разрешение на редактирование любых апартаментов в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-apartment-delete');
        $permission->description = '<b>Разрешить удалять апартаменты в агенстве</b><p>Разрешение на удаление любых апартаментов в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-apartment-multi-control');
        $permission->description = '<b>Разрешить массовое управление апартаментами агенства</b><p>Разрешение на любое действие из массового управления апартаментов в разделах агенства.</p>';
        $auth->add($permission);


        # --- Спецпредложения // special-advert
        $permission = $auth->createPermission('agency-special-advert-view');
        $permission->description = '<b>Разрешить просмотр спецпредложений в агенстве</b><p>Разрешение на просмотр любых спецпредложений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-special-advert-create');
        $permission->description = '<b>Разрешить создавать спецпредложения в агенстве</b><p>Разрешение на создание любых спецпредложений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-special-advert-update');
        $permission->description = '<b>Разрешить редактировать спецпредложения в агенстве</b><p>Разрешение на редактирование любых спецпредложений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-special-advert-delete');
        $permission->description = '<b>Разрешить удалять спецпредложения в агенстве</b><p>Разрешение на удаление любых спецпредложений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-special-advert-multi-create');
        $permission->description = '<b>Разрешить массовое создание спецпредложений в агенстве</b><p>Разрешение на массовое создание любых спецпредложений в агенстве.</p>';
        $auth->add($permission);


        # --- Реклама // advertisement
        $permission = $auth->createPermission('agency-advertisement-view');
        $permission->description = '<b>Разрешить просмотр рекламы в агенстве</b><p>Разрешение на просмотр любых рекламных объявлений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-advertisement-create');
        $permission->description = '<b>Разрешить создавать рекламные объявления в агенстве</b><p>Разрешение на создание любых рекламных объявлений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-advertisement-update');
        $permission->description = '<b>Разрешить редактировать рекламные объявления в агенстве</b><p>Разрешение на редактирование любых рекламных объявлений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-advertisement-delete');
        $permission->description = '<b>Разрешить удалять рекламные объявления в агенстве</b><p>Разрешение на удаление любых рекламных объявлений в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-advertisement-multi-create');
        $permission->description = '<b>Разрешить массовое создание рекламных объявлений в агенстве</b><p>Разрешение на массовое создание любых рекламных объявлений в агенстве.</p>';
        $auth->add($permission);


        # --- Резервации // reservation
        $permission = $auth->createPermission('agency-reservation-create');
        $permission->description = '<b>Разрешить создание заявок на бронь в агенстве</b><p>Разрешение на создание любых заявок на бронь в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-reservation-view');
        $permission->description = '<b>Разрешить просмотр заявок на бронь в агенстве</b><p>Разрешение на просмотр любых заявок на бронь в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-reservation-update');
        $permission->description = '<b>Разрешить редактировать заявки на бронь в агенстве</b><p>Разрешение на редактирование любых заявок на бронь в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-reservation-delete');
        $permission->description = '<b>Разрешить удалять заявки на бронь в агенстве</b><p>Разрешение на удаление любых заявок на бронь в разделах агенства.</p>';
        $auth->add($permission);


        # --- Хочу снять // want-pass
        $permission = $auth->createPermission('agency-want-pass-view');
        $permission->description = '<b>Разрешить просмотр заявок на "Хочу сдать" в агенстве</b><p>Разрешение на просмотр любых заявок на "Хочу сдать" в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-want-pass-update');
        $permission->description = '<b>Разрешить редактировать заявки на "Хочу сдать" в агенстве</b><p>Разрешение на редактирование любых заявок на "Хочу сдать" в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-want-pass-delete');
        $permission->description = '<b>Разрешить удалять заявки на "Хочу сдать" в агенстве</b><p>Разрешение на удаление любых заявок на "Хочу сдать" в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-want-pass-multi-control');
        $permission->description = '<b>Разрешить массовое управление заявками на "Хочу сдать" в агенстве</b><p>Разрешение на массовое управление любыми заявками на "Хочу сдать" в агенстве.</p>';
        $auth->add($permission);


        # --- Быстро подберем // select
        $permission = $auth->createPermission('agency-select-view');
        $permission->description = '<b>Разрешить просмотр заявок на "Быстро подберем" в агенстве</b><p>Разрешение на просмотр любых заявок на "Быстро подберем" в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-select-update');
        $permission->description = '<b>Разрешить редактировать заявки на "Быстро подберем" в агенстве</b><p>Разрешение на редактирование любых заявок на "Быстро подберем" в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-select-delete');
        $permission->description = '<b>Разрешить удалять заявки на "Быстро подберем" в агенстве</b><p>Разрешение на удаление любых заявок на "Быстро подберем" в разделах агенства.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-select-multi-control');
        $permission->description = '<b>Разрешить массовое управление заявками на "Быстро подберем" в агенстве</b><p>Разрешение на массовое управление любыми заявками на "Быстро подберем" в агенстве.</p>';
        $auth->add($permission);



        # --- Реквизиты // details-history
        $permission = $auth->createPermission('agency-details-history-view');
        $permission->description = '<b>Разрешить просмотр заявки на отправку реквизитов в агенстве</b><p>Разрешение на просмотр любых заявок на отправку реквизитов агенства пользователям сайта.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-details-history-update');
        $permission->description = '<b>Разрешить редактировать заявки на отправку реквизитов в агенстве</b><p>Разрешение на просмотр любых заявок на отправку реквизитов агенства пользователям сайта.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-details-history-delete');
        $permission->description = '<b>Разрешить удалять заявки на отправку реквизитов в агенстве</b><p>Разрешение на удаление любых заявок на отправку реквизитов агенства пользователям сайта.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('agency-details-history-send');
        $permission->description = '<b>Разрешить отправлять реквизиты агенства пользователям</b><p>Разрешение на отправку реквизитов агенства пользователям сайта.</p>';
        $auth->add($permission);




        # Обратный звонок
        #=============================================================

        $permission = $auth->createPermission('callback-view');
        $permission->description = '<b>Разрешить просмотр заявок на обратный звонок</b><p>Разрешение на просмотр любых заявок на обратный звонок.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('callback-update');
        $permission->description = '<b>Разрешить редактировать заявоки на обратный звонок</b><p>Разрешение на редактирование любых заявок на обратный звонок.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('callback-delete');
        $permission->description = '<b>Разрешить удалять заявоки на обратный звонок</b><p>Разрешение на удаление любых заявок на обратный звонок.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('callback-multi-control');
        $permission->description = '<b>Разрешить массовое управление заявками на обратный звонок</b><p>Разрешение на массовое управление заявками на обратный звонок.</p>';
        $auth->add($permission);




        # Недмижимость
        #=============================================================

        # --- Апартаменты // apartment
        $permission = $auth->createPermission('realty-rent-type-view');
        $permission->description = '<b>Разрешить просмотр типов аренды</b><p>Разрешение на просмотр любых типов аренды.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('realty-rent-type-create');
        $permission->description = '<b>Разрешить создавать типы аренды</b><p>Разрешение на создание любых типов аренды.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('realty-rent-type-update');
        $permission->description = '<b>Разрешить редактировать типы аренды</b><p>Разрешение на редактирование любых типов аренды.</p>';
        $auth->add($permission);




        # Статьи
        #=============================================================

        $permission = $auth->createPermission('articles-view');
        $permission->description = '<b>Разрешить просмотр статей</b><p>Разрешение на просмотр любых статей.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('articles-create');
        $permission->description = '<b>Разрешить создавать статьи</b><p>Разрешение на создание любых статей.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('articles-update');
        $permission->description = '<b>Разрешить редактировать статьи</b><p>Разрешение на редактирование любых статей.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('articles-delete');
        $permission->description = '<b>Разрешить удалять статьи</b><p>Разрешение на удаление любых статей.</p>';
        $auth->add($permission);




        # Страницы
        #=============================================================

        $permission = $auth->createPermission('pages-view');
        $permission->description = '<b>Разрешить просмотр статических страниц</b><p>Разрешение на просмотр любых статических страниц.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('pages-create');
        $permission->description = '<b>Разрешить создавать статические страницы</b><p>Разрешение на создание любых статических страниц.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('pages-update');
        $permission->description = '<b>Разрешить редактировать статические страницы</b><p>Разрешение на редактирование любых статических страниц.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('pages-delete');
        $permission->description = '<b>Разрешить удалять статические страницы</b><p>Разрешение на удаление любых статических страниц.</p>';
        $auth->add($permission);




        # Отзывы
        #=============================================================

        $permission = $auth->createPermission('reviews-view');
        $permission->description = '<b>Разрешить просмотр отзывов</b><p>Разрешение на просмотр любых отзывов.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('reviews-create');
        $permission->description = '<b>Разрешить создавать отзывы</b><p>Разрешение на создание любых отзывов.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('reviews-update');
        $permission->description = '<b>Разрешить редактировать отзывы</b><p>Разрешение на редактирование любых отзывов.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('reviews-delete');
        $permission->description = '<b>Разрешить удалять отзывы</b><p>Разрешение на удаление любых отзывов.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('reviews-multi-control');
        $permission->description = '<b>Разрешить массовое управление отзывами</b><p>Разрешение на массовое управление любых отзывов.</p>';
        $auth->add($permission);




        # Менрчант
        #=============================================================

        $permission = $auth->createPermission('merchant-payment-view');
        $permission->description = '<b>Разрешить просмотр истории денежного оборота</b><p>Разрешение на просмотр любой истории денежного оборота.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('merchant-service-view');
        $permission->description = '<b>Разрешить просмотр истории покупки сервисов</b><p>Разрешение на просмотр любой истории покупки сервисов.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('merchant-account-management');
        $permission->description = '<b>Разрешить Управление счетом</b><p>Разрешение управлять счетом любого пользователя (пополнение и списание средств со счета).</p>';
        $auth->add($permission);




        # Партнеры (доска объявлений)
        #=============================================================

        # --- Апартаменты // apartment
        $permission = $auth->createPermission('partners-apartment-view');
        $permission->description = '<b>Разрешить просмотр апартаментов на доске объявлений</b><p>Разрешение на просмотр любых апартаментов в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-apartment-create');
        $permission->description = '<b>Разрешить создавать апартаменты на доске объявлений</b><p>Разрешение на создание любых апартаментов в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-apartment-update');
        $permission->description = '<b>Разрешить редактировать апартаменты на доске объявлений</b><p>Разрешение на редактирование любых апартаментов в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-apartment-delete');
        $permission->description = '<b>Разрешить удалять апартаменты на доске объявлений</b><p>Разрешение на удаление любых апартаментов в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-apartment-multi-control');
        $permission->description = '<b>Разрешить массовое управление апартаментами на доске объявлений</b><p>Разрешение на любое действие из массового управления апартаментов в разделах доски объявлений.</p>';
        $auth->add($permission);


        # --- Объявления // advert
        $permission = $auth->createPermission('partners-advert-view');
        $permission->description = '<b>Разрешить просмотр объявлений на доске объявлений</b><p>Разрешение на просмотр любых объявлений в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-advert-create');
        $permission->description = '<b>Разрешить создавать объявления на доске объявлений</b><p>Разрешение на создание любых объявлений в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-advert-update');
        $permission->description = '<b>Разрешить редактировать объявления на доске объявлений</b><p>Разрешение на редактирование любых объявлений в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-advert-delete');
        $permission->description = '<b>Разрешить удалять объявления на доске объявлений</b><p>Разрешение на удаление любых объявлений в разделах доски объявлений.</p>';
        $auth->add($permission);


        # --- Резервации // reservation
        $permission = $auth->createPermission('partners-reservation-view');
        $permission->description = '<b>Разрешить просмотр резерваций на доске объявлений</b><p>Разрешение на просмотр резерваций в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-reservation-update');
        $permission->description = '<b>Разрешить редактировать резервации на доске объявлений</b><p>Разрешение на редактирование резерваций в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-reservation-delete');
        $permission->description = '<b>Разрешить удалять резервации на доске объявлений</b><p>Разрешение на удаление резервации в разделах доски объявлений.</p>';
        $auth->add($permission);


        # --- Резервации к объявленям // advert reservations
        $permission = $auth->createPermission('partners-advert-reservation-view');
        $permission->description = '<b>Разрешить просмотр резерваций к объявлениям на доске объявлений</b><p>Разрешение на просмотр резерваций к объявленям в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-advert-reservation-update');
        $permission->description = '<b>Разрешить редактировать резервации к объявлениям на доске объявлений</b><p>Разрешение на редактирование резерваций к объявлениям в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-advert-reservation-delete');
        $permission->description = '<b>Разрешить удалять резервации к объявлениям на доске объявлений</b><p>Разрешение на удаление резерваций к объявлениям в разделах доски объявлений.</p>';
        $auth->add($permission);


        # --- Заявки "Незаезд" // reservation failures
        $permission = $auth->createPermission('partners-reservation-failure-view');
        $permission->description = '<b>Разрешить просмотр заявок "Незаезд"</b><p>Разрешение на просмотр заявок "Незаезд" в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-reservation-failure-update');
        $permission->description = '<b>Разрешить редактировать заявки "Незаезд"</b><p>Разрешение на редактирование заявок "Незаезд" в разделах доски объявлений.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('partners-reservation-failure-delete');
        $permission->description = '<b>Разрешить удалять заявки "Незаезд"</b><p>Разрешение на удаление заявок "Незаезд" в разделах доски объявлений.</p>';
        $auth->add($permission);




        # Техническая поддержка
        #=============================================================

        $permission = $auth->createPermission('helpdesk-view');
        $permission->description = '<b>Разрешить просмотр обращений в техническую поддержку</b><p>Разрешение на просмотр любых обращений в техническую поддержку.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('helpdesk-update');
        $permission->description = '<b>Разрешить редактировать обращения в техническую поддержку</b><p>Разрешение на редактирование любых обращений в техническую поддержку.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('helpdesk-delete');
        $permission->description = '<b>Разрешить удалять обращения в техническую поддержку</b><p>Разрешение на удаление любых обращений в техническую поддержку.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('helpdesk-answer');
        $permission->description = '<b>Разрешить отвечать на обращения в техническую поддержку</b><p>Разрешение отвечать на любое обращение в техническую поддержку.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('helpdesk-multi-control');
        $permission->description = '<b>Разрешить массовое управление действиями технической поддержки</b><p>Разрешение на любое действие из массового управления технической поддержки.</p>';
        $auth->add($permission);




        # Сеотекст
        #=============================================================

        $permission = $auth->createPermission('seotext-view');
        $permission->description = '<b>Разрешить просмотр сео-текста</b><p>Разрешение на просмотр любых сео-текстов.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('seotext-update');
        $permission->description = '<b>Разрешить редактировать сео-тексты</b><p>Разрешение на редактирование любых сео-текстов.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('seotext-delete');
        $permission->description = '<b>Разрешить удалять сео-тексты</b><p>Разрешение на удаление любых сео-текстов.</p>';
        $auth->add($permission);

        $permission = $auth->createPermission('seotext-create');
        $permission->description = '<b>Разрешить создавать сео-тексты</b><p>Разрешение на создание любых сео-текстов.</p>';
        $auth->add($permission);


        # Сеотекст
        #=============================================================
        $permission = $auth->createPermission('logs-view');
        $permission->description = '<b>Разрешить просмотр логов</b><p>Разрешение на просмотр любых логов</p>';
        $auth->add($permission);
    }

    public function safeDown()
    {
        return false;
    }
}
