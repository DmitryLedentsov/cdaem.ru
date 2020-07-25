<?php
/* @var $this yii\web\View */

/* @var $model common\modules\agency\models\SpecialAdvert */

use kartik\datetime\DateTimePicker;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>


<h6 class="heading-hr"><span class="icon-info"></span> Основное</h6>
<div class="row">
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?php
        echo Html::beginTag('div', ['class' => 'form-group' . ($model->getErrors('date_start') ? ' has-error' : '')]);
        echo Html::activeLabel($model, '[' . $model->advert_id . ']date_start', ['class' => 'control-label']);
        echo DateTimePicker::widget([
            'model' => $model,
            'attribute' => '[' . $model->advert_id . ']date_start',
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]);
        echo Html::error($model, '[' . $model->advert_id . ']date_start', ['class' => 'help-block']);
        echo Html::endTag('div');
        ?>
    </div>
    <div class="col-md-3 col-md-4 col-sm-4 col-xs-12 col-lg-3">
        <?php
        echo Html::beginTag('div', ['class' => 'form-group' . ($model->getErrors('date_expire') ? ' has-error' : '')]);
        echo Html::activeLabel($model, '[' . $model->advert_id . ']date_expire', ['class' => 'control-label']);
        echo DateTimePicker::widget([
            'model' => $model,
            'attribute' => '[' . $model->advert_id . ']date_expire',
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'options' => ['readonly' => 'readonly'],
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
                'todayBtn' => true,
            ]
        ]);
        echo Html::error($model, '[' . $model->advert_id . ']date_expire', ['class' => 'help-block']);
        echo Html::endTag('div');
        ?>
    </div>
</div>

<br/>

<h6 class="heading-hr"><i class="icon-text-width"></i> Содержимое</h6>
<div class="row">
    <div class="col-md-6 col-md-6 col-sm-6 col-xs-12 col-lg-8">
        <?php
        echo Html::beginTag('div', ['class' => 'form-group' . ($model->getErrors('text') ? ' has-error' : '')]);
        echo Html::activeLabel($model, '[' . $model->advert_id . ']text', ['class' => 'control-label']);
        echo Html::activeTextarea($model, '[' . $model->advert_id . ']text', ['class' => 'form-control']);
        echo Html::error($model, '[' . $model->advert_id . ']text', ['class' => 'help-block']);
        echo Html::endTag('div');
        ?>
    </div>
</div>