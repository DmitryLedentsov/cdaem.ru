<?php
/*
    Виджет быстрой оплаты
    @var $model
    @var string $description
    @var array $data
    @var $service
    @var $processService
*/

use yii\helpers\Html;
use yii\helpers\Json;
use common\modules\merchant\widgets\frontend\robokassa\PaymentSystemsList;

$price = $service->getPrice();

if (!empty($processService) && is_object($processService)) {
    $processServiceData = Json::decode($processService->data);
    if (isset($processServiceData['price']) && is_numeric($processServiceData['price']) && $processServiceData['price']) {
        $price = $processServiceData['price'];
    }
}
?>

<?= Html::beginForm(['/merchant/default/service'], 'POST', ['class' => 'payment-form']) ?>


    <div class="payment-service">

<?php if ($description) {
    echo $description;
} ?>

        <p style="margin-bottom: 19px;">Стоимость услуги: <strong><?= Yii::$app->formatter->asCurrency($price, 'RUB') ?></strong></p>

        <?php if (Yii::$app->user->isGuest): ?>
            <div class="form-group">
                <?= Html::textInput('data[email]', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Укажите Ваш E-mail',
                    'style' => 'margin: 14px 0px;'
                ]); ?>
            </div>
        <?php endif; ?>

<!--        <p style="font-weight: bold; font-size: large;padding-bottom: 26px;">-->
<!--            Выберите способ оплаты</p>-->
        <h5 class="modal-pay-subtitle">Выберите способ оплаты</h5>

        <div class="modal-pay-logo">
            <?php if (!Yii::$app->user->isGuest): ?>
                <label class="personal_account">
                    <img src="/_new/images/apartment/personal-account-logo.png" class="personal-account-logo" alt="Личный счёт">
                    <input type="radio" class="logo-pay qiwi-pay" name="pay-way-logo">
                </label>
            <?php endif; ?>

            <label class="qiwi">
                <img src="/_new/images/apartment/qiwi-logo.png" class="qiwi-logo" alt="Qiwi">
                <input type="radio" class="logo-pay qiwi-pay" name="pay-way-logo">
            </label>
            <label class="yandex">
                <img src="/_new/images/apartment/yandex-logo.png" class="yandex-logo" alt="YandexPay">
                <input type="radio" class="logo-pay yandex-pay" name="pay-way-logo">
            </label>
            <label class="visa">
                <img src="/_new/images/apartment/visa-logo.png" class="visa-logo" alt="Visa">
                <input type="radio" class="logo-pay visa-pay" name="pay-way-logo">
            </label>
        </div>

        <h5 class="modal-pay-subtitle">Все способы оплаты</h5>

        <?php
        if (!empty($processService) && is_object($processService)) {
            echo Html::hiddenInput('processId', $processService->id);
        } else {
            echo Html::hiddenInput('service', $service::NAME);
        }
        ?>
        <?= Html::hiddenInput('type', 'pay-account'); ?>
        <?= Html::hiddenInput('system', 'BANKOCEAN2R'); ?>

        <?php
        echo Html::hiddenInput('data', null);
        if ($data) {
            foreach ($data as $param => $value) {
                echo Html::hiddenInput('data[' . $param . ']', $value);
            }
        }
        ?>

        <div class="payment-way" data-target="pay-account">
            <!--<p><?/*= 'У Вас должна быть необходимая сумма на внутреннем счете.'; */?></p>

            <div class="text-center pay-button">
                <input type="submit" value="Оплатить" class="btn btn-orange"/>
            </div>-->
        </div>

<!--        todo проверить этот блок. он похоже не для авторизованных, они тоже покупают сервисы? Где? -->
        <div class="payment-way" <?php if (Yii::$app->user->isGuest): ?>style="display: block;"<?php endif; ?>
             data-target="pay-system">
            <div class="method">
                <span class="payments-method-logo visa" data-system="BANKOCEAN3R"></span>
                <span class="payments-method-logo mastercard" data-system="BANKOCEAN3R"></span>
                <span class="payments-method-logo mts" data-system="MixplatMTSRIBR"></span>
                <!--<span class="payments-method-logo megafon" data-system="MegafonR"></span>-->
                <span class="payments-method-logo beeline" data-system="MixplatBeelineRIBR"></span>
            </div>
            <div class="method">
                <span class="payments-method-logo qiwi" data-system="Qiwi50RIBRM"></span>
                <span class="payments-method-logo yamoney" data-system="YandexMerchantRIBR"></span>
                <span class="payments-method-logo webmoney" data-system="WMRRM"></span>
            </div>
            <div class="extra clearfix">
<!--                <h4>Все способы оплаты:</h4>-->
<!--                <p>Все способы оплаты:</p>-->
                <?= PaymentSystemsList::widget([
                    'selectOptions' => [
                        'class' => 'form-control',
                        'name' => 'system'
                    ]
                ]); ?>
            </div>
            <br/>

            <!--
            <?php if (!Yii::$app->user->isGuest): ?>
                <div class="pay clearfix">
                    <div class="pay-account" data-type="pay-account">
                        <a href="#">Оплата с личного счета</a>
                    </div>
                </div>
            <?php endif; ?>
            -->


            <div class="modal-footer modal-bottom">
                <div class="modal-submit">
                    <label>
                        <input type="checkbox" class="submit" name="submit">
                        <span class="modal-submit-text">
                           Оплачивая вы соглашаетесь с правилами <br>
                            <a href="">политики конфиденциальности</a>
                        </span>
                    </label>
                </div>
<!--                <button type="button" class="btn btn-warning btn-special">оплатить</button>-->

<!--                <div class="text-center pay-button"
                     <?php /*if (Yii::$app->user->isGuest): */?>style="display: block;"<?php /*endif; */?>>
                    <input type="submit" value="Оплатить" class="btn btn-orange"/>
                </div>
-->
                <div class="text-center pay-button" style="display: block;">
                    <input type="submit" value="Оплатить" class="btn btn-orange"/>
                </div>
            </div>

        </div>

    </div>

<?= Html::endForm() ?>
