<?php

namespace common\modules\users\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\users\assets
 */
class UserAsset extends AssetBundle
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
                'js_dev/users.js',
            ];
        } else {
            $this->js = [
                'js/users.js',
            ];
        }
    }
}
