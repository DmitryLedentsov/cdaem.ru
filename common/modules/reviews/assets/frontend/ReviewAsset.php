<?php

namespace common\modules\reviews\assets\frontend;

use yii\web\AssetBundle;
use Yii;

/**
 * Менеджер ресурсов
 * @package common\modules\reviews\assets\frontend
 */
class ReviewAsset extends AssetBundle
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
        'yii\captcha\CaptchaAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {

            $this->js = [
                'js_dev/reviews.js'
            ];

        } else {

            $this->js = [
                'js/reviews.js'
            ];
        }
    }
}