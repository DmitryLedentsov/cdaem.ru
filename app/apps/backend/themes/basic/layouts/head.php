<?php

/**
 * "Head" содержимое шаблона.
 * @var yii\base\View $this Представление
 * @var array $params Основные параметры представления
 */

use yii\web\View;
use yii\helpers\Html;

?>
    <title><?php echo Html::encode($this->title); ?></title>
<?php
$this->registerMetaTag([
    'charset' => Yii::$app->charset
]);
$this->registerMetaTag([
    'http-equiv' => 'X-UA-Compatible',
    'content' => 'IE=edge'
]);


$this->registerMetaTag([
    'http-equiv' => 'Refresh',
    'content' => '1200'
]);

$this->registerMetaTag([
    'name' => 'viewport',
    'content' => 'width=device-width, initial-scale=1'
]);

$this->registerMetaTag([
    'name' => 'format-detection',
    'content' => 'telephone=no'
]);


$this->registerLinkTag([
    'href' => Yii::$app->getRequest()->baseUrl . '/favicon.ico',
    'rel' => 'icon',
    'type' => 'image/x-icon'
]);
$this->registerLinkTag([
    'href' => Yii::$app->getRequest()->baseUrl . '/favicon.ico',
    'rel' => 'shortcut icon',
    'type' => 'image/x-icon'
]);

echo Html::csrfMetaTags();


$this->head();


\common\modules\admin\assets\AdminAsset::register($this);
