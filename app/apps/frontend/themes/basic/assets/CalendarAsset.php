<?php

namespace frontend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\themes\basic\assets
 */
class CalendarAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/themes/basic/assets';

    /**
     * @inheritdoc
     */
    public $depends = [
        \yii\web\JqueryAsset::class,
        'frontend\themes\basic\assets\DateTimeAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->css = [
            'widgets/fullCalendar/fullcalendar.css',
        ];

        $this->js = [
            'widgets/fullCalendar/fullcalendar.min.js',
            'widgets/fullCalendar/calendar-app.js?v=' . filemtime(Yii::getAlias($this->sourcePath) . DIRECTORY_SEPARATOR . 'widgets/fullCalendar/calendar-app.js'),
        ];


        /*if (YII_DEBUG) {


        } else {


        }*/
    }
}
