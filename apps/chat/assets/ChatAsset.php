<?php

namespace chat\assets;

use yii\web\AssetBundle;
use Yii;

/**
 * Менеджер ресурсов
 */
class ChatAsset extends AssetBundle
{
    public $sourcePath = '@chat/assets';

    public $css = [
        'css/chat.css',
    ];

    public $js = [
        'infinite-scroll/jquery.infinitescroll.min.js',
        'js/chat.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'yii\widgets\ActiveFormAsset',
    ];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        array_unshift($this->js, 'https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js');
    }
}