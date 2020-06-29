<?php

namespace frontend\themes\basic\assets;

use yii\web\AssetBundle;
use Yii;

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
        'yii\web\JqueryAsset',
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