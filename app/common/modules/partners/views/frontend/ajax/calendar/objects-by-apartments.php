<?php
/*
    Форма для указания статуса аренды
    @var \yii\web\View this
    @var \common\modules\partners\models\frontend\form\CalendarForm $model
    @var \common\modules\partners\models\frontend\Apartments $apartments
    @var string $date
*/

use yii\helpers\Html;
use common\modules\partners\widgets\frontend\PreviewAdvertTmp;

?>
<?php if (!empty($apartments)): ?>
    <?= Html::beginForm('', 'post', ['id' => 'form-calendar']) ?>
    <?php foreach ($apartments as $apartment):
        $data = $model->getFormatData($apartment, $date); ?>

        <div class="item control-date clearfix">
            <div class="object">
                <?= PreviewAdvertTmp::widget([
                    'apartment' => $apartment,
                ]); ?>
            </div>
            <div class="options clearfix">

                <?php echo Html::activeHiddenInput($model, 'whichDate[' . $apartment->apartment_id . ']', [
                    'value' => $date,
                ]); ?>

                <?php echo Html::activeHiddenInput($model, 'calendarApartmentId[' . $apartment->apartment_id . ']', [
                    'value' => $apartment->apartment_id,
                ]); ?>

                <div class="form-group">
                    <label class="control-label">Тип:</label>
                    <?php echo Html::activeDropDownList($model, 'type[' . $apartment->apartment_id . ']', $model->getStatusArray(), [
                        'class' => 'form-control object-type',
                        'options' => [
                            $data['type'] => ['selected ' => true]
                        ],
                        'data' => [
                            'apartment_id' => $apartment->apartment_id
                        ]
                    ]);
                    ?>
                </div>

                <div class="options-date">
                    <div class="form-group">
                        <label class="control-label">Дата старта:</label>
                        <div class="clearfix">
                            <?php echo Html::activeTextInput($model, 'date_start[' . $apartment->apartment_id . ']', [
                                'class' => 'form-control date datepicker',
                                'readonly' => 'readonly',
                                'value' => $data['date_from'],
                            ]);
                            ?>
                            <?php echo Html::activeTextInput($model, 'time_start[' . $apartment->apartment_id . ']', [
                                'class' => 'form-control date timepicker',
                                'readonly' => 'readonly',
                                'value' => $data['time_from'],
                                'placeholder' => '00:00'
                            ]);
                            ?>
                        </div>
                        <div class="help-block"></div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="control-label">Дата завершения:</label>
                        <div class="clearfix">
                            <?php echo Html::activeTextInput($model, 'date_end[' . $apartment->apartment_id . ']', [
                                'class' => 'form-control date datepicker',
                                'readonly' => 'readonly',
                                'value' => $data['date_to'],
                            ]);
                            ?>
                            <?php echo Html::activeTextInput($model, 'time_end[' . $apartment->apartment_id . ']', [
                                'class' => 'form-control date timepicker',
                                'readonly' => 'readonly',
                                'value' => $data['time_to'],
                                'placeholder' => '00:00'
                            ]);
                            ?>
                        </div>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="form-group text-center">
        <input type="submit" class="btn btn-primary" value="Сохранить"/>
    </div>

    <?= Html::endForm() ?>

<?php else:
    echo Html::tag('div', 'Вы еще не добавили ни одного объявления. ' . Html::a('Добавить', ['/partners/default/create']), [
        'class' => 'alert alert-info'
    ]);
endif; ?>