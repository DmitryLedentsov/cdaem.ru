<?php

/**
 * Мини шаблон
 * @var yii\base\View $this Представление
 * @var string $content Контент
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

\backend\themes\basic\assets\BaseAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?= $this->render('//layouts/head'); ?>
</head>
<body class="full-width page-condensed">
    <?php $this->beginBody(); ?>
    <?= $this->render('//layouts/navbar'); ?>
    <!-- Page container -->
    <div class="page-container">
        <?php echo $content; ?>
        <?= $this->render('//layouts/footer'); ?>
    </div>
    <!-- /page container -->
    <?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>