<?php

namespace common\modules\agency\assets\frontend;

use yii\web\AssetBundle;
use Yii;

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
        'yii\web\JqueryAsset',
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