<?php

namespace frontend\themes\basic\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 * @package frontend\themes\basic\assets
 */
class AppAsset extends AssetBundle
{

    public $sourcePath = '@frontend/themes/basic/assets';
    public $css = [
        'https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic&subset=latin,cyrillic',
        'https://fonts.googleapis.com/css?family=Roboto',
    ];
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

            $this->css = array_merge($this->css, [
                'css_dev/reset.css',
                'widgets/pnotify/pnotify.custom.min.css',
                'widgets/browsers/jquery.reject.css',
                'css/styles.css',
                'css/media.css',
                'font-awesome/css/fontawesome-all.css',
                'css/jquery.fancybox.css',
            ]);

            $this->js = [
                'widgets/jquery-autocomplete/jquery.autocomplete.js',
                'widgets/browsers/jquery.reject.min.js',
                'widgets/bootstrap-dropdown/dropdown.js',
                'widgets/bootstrap-tab/tab.js',
                'widgets/bootstrap-modal/modal.js',
                'widgets/bootstrap-select/bootstrap-select.min.js',
                'widgets/pnotify/pnotify.custom.min.js',
                'widgets/scroll/jquery-scrolltofixed-min.js',
                'widgets/inputmask-multi/jquery.maskedinput.min.js',
                'js_dev/formApi.js',
                'js_dev/URI.js',
                'js_dev/ui.js',
                'js_dev/jquery.inputmask.js',
                'js_dev/jquery.fancybox.min.js',

            ];
        } else {

            $this->css = array_merge($this->css, [
                'widgets/pnotify/pnotify.custom.min.css',
                'widgets/browsers/jquery.reject.css',
                'css/styles.css',
                'css/media.css',
                'css/reset.css',
                'font-awesome/css/fontawesome-all.css',
                'css/jquery.fancybox.css',
            ]);

            $this->js = [
                'js/ui.js',
                'js/minimenu.js',
                'js/jquery.inputmask.js',
                'js/jquery.fancybox.min.js',
            ];
        }

        $this->sourcePath = __DIR__;
    }

    /**
     * Получить url адрес папки с ресурсами
     * @return string
     */
    public static function getAssetUrl()
    {
        $obj = new self();
        return \Yii::$app->assetManager->getPublishedUrl($obj->sourcePath);
    }

}
