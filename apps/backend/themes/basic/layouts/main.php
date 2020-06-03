<?php
/**
 * Основной шаблон
 * @var yii\base\View $this Представление
 * @var string $content Контент
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

\backend\themes\basic\assets\BaseAsset::register($this);
use yii\web\View;
$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?= $this->render('//layouts/head'); ?>
</head>
<body class="sidebar-right">
<?php $this->beginBody(); ?>
    <?= $this->render('//layouts/navbar'); ?>
    <!-- Page container -->
    <div class="page-container">
        <?= $this->render('//layouts/sidebar'); ?>
        <!-- Page content -->
        <div class="page-content">
            <?php echo $content; ?>
            <p><br/></p>
            <?= $this->render('//layouts/footer'); ?>
        </div>
        <!-- /page content -->
    </div>
    <!-- /page container -->

<?php

    $this->registerJs('var _settings = {siteDomain:  "'. Yii::$app->params['siteDomain'] . '/geo/select-city"}', View::POS_HEAD) ?>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>