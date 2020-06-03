<?php

namespace common\modules\callback\assets\frontend;

use yii\web\AssetBundle;
use Yii;

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
        'yii\web\JqueryAsset',
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