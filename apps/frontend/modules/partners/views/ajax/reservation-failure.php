<?php
/*
    Форма незаезда
    @var \yii\web\View this
    @var int $reservationId
*/
use yii\helpers\Html;
?>
<div id="modal-reservation-failure" class="modal fade" data-title="Незаезд">
    <div class="form-group required">

        <label class="control-label">Укажите причину незаезда:</label>
        <?php
            echo Html::textarea('cause_text', null, ['class' => 'form-control', 'id' => 'reservation-failure-reason'])
        ?>
        <div class="help-block"></div>
    </div>
    <br/>
    <div class="form-group text-center">
        <input type="submit" value="Отправить" class="btn btn-primary" id="button-reservation-failure-confirm" data-id="<?=$reservationId?>" />
    </div>
</div>