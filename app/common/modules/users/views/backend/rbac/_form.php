<?php
/**
 * Форма группы
 * @var $this yii\web\View
 * @var $model nepster\users\rbac\models\AuthItem
 */

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\rbac\Item;

$permissions = $model->getRolePermissions();


$permissionsByModules = [];

foreach ($permissions as $id => $permission) {
    $module = getModuleName($id);
    $permissionsByModules[$module] = $permission;
}


$modules = array_keys($permissionsByModules);

$names = [
    'agency' => 'Агентство',
    'realty' => 'Недвижимость',
    'articles' => 'Статьи',
    'callback' => 'Обратный звонок',
    'helpdesk' => 'Техническая поддержка',
    'merchant' => 'Мерчант',
    'pages' => 'Статические страниц',
    'partners' => 'Доска Объявлений',
    'reviews' => 'Отзывы',
    'user' => 'Пользователи',
    'seotext' => 'Сео-Тексты',
    'logs' => 'Логи',
    'seo' => 'Сео спецификации'
];

$iModules = [];

foreach ($modules as $key => &$module) {
    $name = isset($names[$module]) ? $names[$module] : 'Без названия';
    $iModules[$module] = $name;
}


function getModuleName($id)
{
    return explode('-', $id)[0];
}
?>


<?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-2"><?= $form->field($model, 'name') ?></div>
        <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3"><?= $form->field($model, 'description') ?></div>
    </div>


    <ul class="nav nav-tabs">
        <?php
            $c = 0;
            foreach ($iModules as $module => $name):
                ++$c;
        ?>
            <li class="<?php echo ($c == 1) ? 'active' : ''?>"><a href="#<?=$module?>" data-toggle="tab"><?=$name?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content">
        <?php
            $c = 0;
            foreach ($iModules as $module => $name):
                ++$c;
        ?>
            <div class="tab-pane fade in <?php echo ($c == 1) ? 'active' : ''?>" id="<?=$module?>" style="padding: 15px">
                <?php foreach ($permissions as $id => $permission): ?>
                    <?php if ($module != getModuleName($id)) { continue; } ?>
                    <?php $field = $form->field($model, 'permissions[' . $id . ']'); ?>
                    <?php echo $field->begin(); ?>
                    <?php
                        echo Html::activeCheckbox($model, 'permissions[' . $id . ']', [
                            'label' => Yii::t('users.rbac', $permission->description),
                            'class' => 'pull-left',
                        ]);
                    ?>
                    <?php echo $field->end(); ?>

                <?php endforeach; ?>

            </div>
        <?php endforeach; ?>
    </div>

    <br />

    <?php echo Html::submitButton(Yii::t('users', 'SEND'), ['class' => 'btn btn-success']); ?>

<?php ActiveForm::end(); ?>


<?php
$this->registerCss('
    label b {margin-left: 5px;}
    label p {font-weight: normal; margin-left: 20px;}
');