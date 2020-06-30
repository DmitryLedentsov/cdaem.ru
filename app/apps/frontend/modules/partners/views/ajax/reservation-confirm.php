<?php
/*
    Форма подтверждения заявки
    @var \yii\web\View this
    @var string $type
    @var int $reservationId
    @var float $advertPrice
    @var double $advertPrice
*/

use yii\helpers\Html;


if ($userType == 'landlord') {
    $ownerPercentage = Yii::$app->getModule('partners')->ownerReservationPercent;
    $priceToPay = $advertPrice / 100 * $ownerPercentage;
} else if ($userType == 'renter') {
    $clientPercentage = Yii::$app->getModule('partners')->clientReservationPercent;
    $priceToPay = $advertPrice / 100 * $clientPercentage;
} else {
    $priceToPay = 0;
}


?>
<div id="modal-reservation-confirm" class="modal fade" data-title="Подтверждение">
    <div class="form-group required fort">
        <?php if ($type == 'confirm'): ?>
            <div class="alert alert-info">
                <b>Внимание</b> <br/>

                Вы подтверждаете заявку на бронирование <strong>№<?= $reservationId ?></strong>
            </div>

            <?php if ($userType == 'landlord') : ?>
                <p>
                    Сумма к оплате:
                    <b><?= Yii::$app->formatter->asCurrency($priceToPay, Yii::$app->getModule('merchant')->viewMainCurrency) ?></b>
                    <br>
                    Получаете при заселении Клиента:
                    <b><?= Yii::$app->formatter->asCurrency($advertPrice - $priceToPay, Yii::$app->getModule('merchant')->viewMainCurrency) ?></b>
                </p>
                <p>
                    После подтверждения заявки с Вашего счета будут автоматически списаны средства в размере
                    <b><?= $this->context->module->ownerReservationPercent ?>%</b> от суммы стоимости бронирования
                    Вашего объекта.
                    Согласно правилам, Клиент бронируя ваше жильё вносит 5% от суммы брони, что является гарантией
                    бронирования!
                    При заезде в ваше жильё Клиент, вносит оплату на 5% меньше, от стоимости бронирования вашего
                    объекта!
                </p>
                <p style="color:red">
                    -При отмене бронирования Клиентом или истечение времени заявки, деньги возвращаются на ваш баланс.
                </p>
            <?php endif; ?>

            <?php if ($userType == 'renter') : ?>

                <p>
                    Сумма к оплате:
                    <b><?= Yii::$app->formatter->asCurrency($priceToPay, Yii::$app->getModule('merchant')->viewMainCurrency) ?></b>
                    <br>
                    Платите Владельцу при заселении:
                    <b><?= Yii::$app->formatter->asCurrency($advertPrice - $priceToPay, Yii::$app->getModule('merchant')->viewMainCurrency) ?></b>
                </p>
                <p>
                    После подтверждения заявки с Вашего счета будут автоматически списаны средства в размере
                    <b><?= $this->context->module->clientReservationPercent ?>%</b> от суммы стоимости бронирования
                    Вашего объекта,
                    что является гарантией бронирования! При заезде в жильё Владельца, вносите оплату на 5% меньше,
                    от стоимости бронирования вашего объекта!
                </p>
            <?php endif; ?>

            <p>Если на Вашем счету не окажется нужной суммы, Вы не сможете подтвердить заявку. Для этого пополните свой
                счет.</p>
            <p>Подробная информация доступна в разделе
                <strong><?= Html::a('пользовательское соглашение', ['/pages/default/index', 'url' => 'agreement'], ['target' => '_blanck']) ?></strong>.
            </p>

        <?php else: ?>

            <div class="alert alert-warning">
                <b>Внимание</b> <br/>
                Вы отказываетесь подтверждать заявку на бронирование <strong>№<?= $reservationId ?></strong>
            </div>

            <label class="control-label"><h4>Укажите причину отказа:</h4></label>
            <div style="margin-top:20px">
                <?php
                //$new = 1;
                //echo Html::textarea('reason', null, ['class' => 'form-control', 'id' => 'reservation-confirm-reason']);
                // echo Html::radioList('abc', null, $new, ['class' => 'form-control', 'id' => 'reservation-confirm-reason']);
                //echo Html::radio('reason', ['class' => 'form-control', 'id' => 'reservation-confirm-reason']);
                //echo Html::radio('reason', 'Какаляка маляка укакакакакакакака', ['label' => 'Я согласен' , 'class' => 'form-control', 'id' => 'reservation-confirm-reason', 'value' => 'Квартира занята']);
                $name = "reason";
                $items = ['Квартира занята' => "Квартира занята", 'Эти даты заняты' => "Эти даты заняты", 'Не хочу сдавать' => "Не хочу сдавать", 'Нет возможности' => "Нет возможности", 'Уже забронировал другой Клиент' => "Уже забронировал другой Клиент"];
                $selection = 1;

                echo Html::radioList($name, $selection, $items, [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        $disabled = false;
                        return Html::radio($name, $checked, [
                            'value' => $value,
                            'label' => Html::encode($label),
                            'disabled' => $disabled,
                            'class' => 'form-control2', 'id' => 'reservation-confirm-reason',
                        ]);
                    },
                ]);
                ?></div>
            <div class="help-block"></div>

        <?php endif; ?>
    </div>
    <br/>
    <div class="form-group text-center">
        <?php if ($user_id == 0) : ?>
            <input type="submit" value="<?= $type == 'confirm' ? 'Подтверждаю' : 'Отказ' ?>" class="btn btn-primary"
                   id="button-reservation-confirm" data-type="<?= $type ?>" data-reservation="<?= $reservationId ?>"
                   data-user-id="<?= $user_id ?>" data-priced="<?= "" . $priceToPay . " Рублей " ?>"
                   data-department="<?= $department ?>" data-user-type="<?= $userType ?>"/>
        <?php else: ?>
            <div class="alert alert-warning">
                <b>Внимание</b> <br/>
                <p>Другой Владелец подтвердил бронь Вашего Клиента быстрее. Заявка аннулирована !</p>
            </div>
        <?php endif; ?>


    </div>
</div>