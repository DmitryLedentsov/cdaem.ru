<?php

namespace common\modules\office;

use Yii;

/**
 * Общий модуль [[Office]]
 * Осуществляет работу c офисом.
 */
class Module extends \yii\base\Module
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
