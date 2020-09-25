<?php

namespace frontend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\themes\basic\assets
 */
class ModalHelpAsset extends AssetBundle
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

        $this->css = [
            'widgets/modal-help/jquery.arcticmodal-0.3.css',
            'widgets/modal-help/simple.css',
        ];

        $this->js = [
            'widgets/modal-help/jquery.arcticmodal-0.3.min.js',
            'widgets/modal-help/jquery.cookie.js',
            'widgets/modal-help/script-modal.js',
        ];
    }
}
