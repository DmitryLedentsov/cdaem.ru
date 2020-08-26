<?php

namespace common\modules\partners\assets\frontend;

use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package common\modules\partners\assets\frontend
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
