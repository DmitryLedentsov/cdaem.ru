<?php

namespace common\modules\users;

/**
 * Module [[Users]]
 * @package common\modules\users
 */
class Module extends \nepster\users\Module
{
    /**
     * Доступ ролей в панель управления
     */
    public $accessGroupsToControlpanel = ['admin', 'senior-dispatcher', 'ekaterina', 'dispatcher', 'seo', 'operator', 'milana'];

    /**
     * Вес аватара 2mb
     */
    public $maxSize = 2097152;

    public $requireEmailConfirmation = true;

    /**
     * Развер аватара
     */
    public $avatarResizeWidth = 300;

    /**
     * Интервал времени на который блокировать пользователя
     */
    public $intervalAuthBan = 600;

    /**
     * Путь к временной директории с аватарками пользователей
     */
    public $avatarsTempPath = '@frontend/web/tmp/avatars';

    /**
     * Путь к директории с аватарками пользователей
     */
    public $avatarPath = '@frontend/web/avatars';

    /**
     * Адрес директории на сайте с аватарками пользователей
     */
    public $avatarUrl = '/avatars';
}
