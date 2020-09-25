<?php

namespace common\modules\callback\assets\frontend;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\callback\assets\frontend
 */
class CallbackAsset extends AssetBundle
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
                'js_dev/callback.js'
            ];
        } else {
            $this->js = [
                'js/callback.js'
            ];
        }
    }
}
