<?php

namespace common\modules\admin\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class AdminAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/admin/assets';

    public $css = [
    ];

    public $js = [
        'js/ui.js',
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
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
