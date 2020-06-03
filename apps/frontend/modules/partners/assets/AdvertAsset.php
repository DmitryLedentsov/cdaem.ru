<?php

namespace frontend\modules\partners\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\modules\partners\assets
 */
class AdvertAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@frontend/themes/basic/assets';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {

            $this->css = [
                'css_dev/search.css',
            ];

        } else {

            $this->css = [
                'css/search.css',
            ];
        }
    }
}