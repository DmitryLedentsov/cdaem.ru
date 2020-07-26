<?php

namespace backend\modules\admin\assets;

use yii\web\AssetBundle;
use Yii;

/**
 * Менеджер ресурсов
 */
class AdminAsset extends AssetBundle
{
    public $sourcePath = '@backend/modules/admin/assets';

    public $css = [
    ];

    public $js = [
        'js/ui.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $view = Yii::$app->getView();
        $view->registerJsFile(Yii::$app->params['siteDomain'] . '/ion.sound/ion.sound.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
    }
}
