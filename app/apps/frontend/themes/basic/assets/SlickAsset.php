<?php

namespace frontend\themes\basic\assets;

use yii\web\AssetBundle;
use Yii;

/**
 * Менеджер ресурсов
 * @package frontend\themes\basic\assets
 */
class SlickAsset extends AssetBundle
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
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->css = [
            'widgets/slick/slick-theme.css',
            'widgets/slick/slick.css',
        ];

        $this->js = [
            'widgets/slick/jquery-migrate-1.2.1.min.js',
            'widgets/slick/slick.min.js',
            'widgets/slick/slick.pattern.js',
            'widgets/slick/slick.pattern2.js',
            'widgets/slick/slick.pattern3.js',
            'widgets/slick/slick.pattern4.js',
            'widgets/slick/slick.pattern5.js',
            'widgets/slick/slick.pattern6.js',
            'widgets/slick/slick.pattern7.js',
        ];
    }
}
