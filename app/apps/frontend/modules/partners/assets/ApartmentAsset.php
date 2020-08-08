<?php

namespace frontend\modules\partners\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\modules\partners\assets
 */
class ApartmentAsset extends AssetBundle
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
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {
            $this->js = [
                'js_dev/partners.js'
            ];
        } else {
            $this->js = [
                'js_dev/partners.js'
            ];
        }
    }
}
