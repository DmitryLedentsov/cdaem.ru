<?php

namespace common\modules\agency\assets\frontend;

use Yii;
use yii\web\AssetBundle;

/**
 * Agency Asset
 * @package common\modules\agency\frontend\assets
 */
class AgencyAsset extends AssetBundle
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
        'frontend\themes\basic\assets\DateTimeAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (YII_DEBUG) {
            $this->js = [
                'js_dev/agency.js',
            ];
        } else {
            $this->js = [
                'js/agency.js?v=' . filemtime(Yii::getAlias($this->sourcePath) . DIRECTORY_SEPARATOR . 'js/agency.js'),
            ];
        }
    }
}
