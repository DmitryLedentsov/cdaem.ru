<?php

namespace frontend\modules\office;

use Yii;

/**
 * Office module
 */
class Module extends \common\modules\office\Module
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // FIX TWIG BUG
        // Кастомные компоненты не инициализируются в твиге
        // для этого инициализируем компонент перед рендером шаблонов
        Yii::$app->service;
    }
}
