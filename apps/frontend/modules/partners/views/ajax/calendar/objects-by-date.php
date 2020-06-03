<?php
/*
    Стоимость сервиса и подтверждение покупки
    @var \yii\web\View this
    @var string $date
    @var frontend\modules\partners\models\Calendar $calendar
*/
use frontend\modules\partners\widgets\PreviewAdvertTmp;
use frontend\modules\partners\models\Calendar;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<?php if (!empty($calendar)): ?>

    <?php foreach ($calendar as $record): ?>

        <div class="item control-date clearfix">
            <div class="object">
                <?= PreviewAdvertTmp::widget([
                    'apartment' => $record->apartment,
                ]); ?>
            </div>
            <div class="options clearfix">
                <br/>
                <div>
                    <strong style="font-size: 150%" class="<?=(!$record->reserved) ? 'color-success' : 'color-danger'?>"><?=ArrayHelper::getValue($record->getStatusArray(), $record->reserved)?></strong>
                    <p>
                        С <?=Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($record->date_from)?> <br/>
                        По <?=Yii::$app->BasisFormat->helper('DateTime')->toFullDateTime($record->date_to)?>
                    </p>
                </div>

            </div>
        </div>
    <?php endforeach; ?>

<?php else:
    echo Html::tag('div', 'Нет информации...', [
        'class' => 'alert alert-info'
    ]);
endif; ?>