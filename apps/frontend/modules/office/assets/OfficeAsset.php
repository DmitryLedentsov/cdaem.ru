<?php

namespace frontend\modules\office\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\modules\office\assets
 */
class OfficeAsset extends AssetBundle
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

        if (YII_DEBUG) {

            $this->css = [
                'css_dev/office.css',
            ];

            $this->js = [
                'js_dev/office.js',
            ];

        } else {

            $this->css = [
                'css/office.css',
            ];

            $this->js = [
                'js/office.js',
            ];

        }
    }
}