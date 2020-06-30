<?php
/**
 * Navbar
 * @var yii\base\View $this Представление
 */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- Navbar -->
<div class="navbar navbar-inverse" role="navigation">
    <div class="navbar-header">
        <a class="navbar-brand" href="/">Панель управления</a>
        <?php if (Yii::$app->user->id) : ?>
            <a class="sidebar-toggle"><i class="icon-paragraph-justify2"></i></a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-icons">
                <span class="sr-only">Toggle navbar</span>
                <i class="icon-grid3"></i>
            </button>
            <button type="button" class="navbar-toggle offcanvas">
                <span class="sr-only">Toggle navigation</span>
                <i class="icon-paragraph-justify2"></i>
            </button>
        <?php endif; ?>
    </div>

    <?php if (Yii::$app->user->id) : ?>
        <ul class="nav navbar-nav navbar-right collapse navbar-collapse" id="navbar-icons">
            <li class="user dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <?= \nepster\faceviewer\Widget::widget([
                        // шаблон отображения
                        'template' => '{face} <span>{username}</span> <i class="caret"></i>',
                        'data' => [
                            'username' => Yii::$app->user->identity->email,
                            'avatar_url' => Yii::$app->user->identity->profile->avatar_url,
                        ]
                    ]); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-right icons-right">
                    <li><?php echo Html::a('На сайт', Yii::$app->params['siteDomain']) ?></li>
                    <li><a href="<?php echo Url::toRoute(['/users/user/update', 'id' => Yii::$app->user->id]) ?>"><i
                                    class="icon-user"></i> Профиль</a></li>
                    <li><a href="<?php echo Url::toRoute(['/users/user/logout']) ?>"><i class="icon-exit"></i> Выход</a>
                    </li>
                </ul>
            </li>
        </ul>
    <?php endif; ?>
</div>
<!-- /navbar -->