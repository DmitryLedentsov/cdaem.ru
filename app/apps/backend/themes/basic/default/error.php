<?php
/**
 * Представление страницы ошибок.
 * @var yii\base\View $this Предсталвение
 * @var string $name Название
 * @var string $message Сообщение
 * @var Exception $exception Исключение
 */

use yii\helpers\Html;

$this->title = 'ERROR' . ' - ' . $exception->statusCode;
$this->context->layout = '//min';
?>

<!-- Error wrapper -->
<div class="error-wrapper text-center">
    <h1><?php echo Html::encode($exception->statusCode); ?></h1>
    <h5><?php echo Html::encode(''); ?></h5>
    <h6><?php echo nl2br(Html::encode($exception->getMessage())); ?></h6>
    <!-- Error content -->
    <div class="error-content">
        <br/>
        <div class="row">
            <div class="col-md-6"><a href="/" class="btn btn-danger btn-block">Панель управления</a></div>
            <div class="col-md-6"><a href="<?php echo Yii::$app->params['siteDomain'] ?>"
                                     class="btn btn-success btn-block">Вернуться на сайт</a></div>
        </div>
    </div>
    <!-- /error content -->
</div>
