<?php

namespace frontend\modules\partners;

use Yii;

/**
 * Общий модуль [[Partners]]
 * Осуществляет всю работу с апартаментами пользователей
 */
class Module extends \common\modules\partners\Module
{
    /**
     * Количество записей топ адвертов на одной странице
     * @var int
     */
    public $pageSizeTop = 20;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->params['defaultPageSizeTopAdverts'] = 20;
        // FIX TWIG BUG
        // Кастомные компоненты не инициализируются в твиге
        // для этого инициализируем компонент перед рендером шаблонов
        Yii::$app->service;
    }
}
