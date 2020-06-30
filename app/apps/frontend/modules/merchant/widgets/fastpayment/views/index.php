<?php
/*
    Виджет быстрой оплаты
    @var $model
    @var string $description
    @var array $data
    @var $service
    @var $processService
*/

use frontend\modules\merchant\widgets\robokassa\PaymentSystemsList;
use frontend\modules\merchant\widgets\robokassa\RenderForm;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;

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

        <p>Стоимость услуги: <strong><?= Yii::$app->formatter->asCurrency($price, 'RUB') ?></strong></p>

        <?php if (!Yii::$app->user->isGuest): ?>
            <div class="pay clearfix">
                <div class="pay-account" data-type="pay-account">
                    <h3>Оплата с личного счета</h3>
                </div>
                <div class="pay-system" data-type="pay-system">
                    <h4>Все способы оплаты</h4>
                </div>
            </div>
        <?php endif; ?>

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
            <p><?= 'У Вас должна быть необходимая сумма на внутреннем счете.'; ?></p>

            <div class="text-center pay-button">
                <input type="submit" value="Оплатить" class="btn btn-orange"/>
            </div>
        </div>

        <?php if (Yii::$app->user->isGuest): ?>
            <div class="form-group">
                <label class="control-label"><b>Укажите Ваш EMAIL:</b></label>
                <?= Html::textInput('data[email]', null, ['class' => 'form-control']); ?>
            </div>
        <?php endif; ?>

        <div class="payment-way" <?php if (Yii::$app->user->isGuest): ?>style="display: block;"<?php endif; ?>
             data-target="pay-system">

            <h3>Выберите способ оплаты:</h3>

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
                <h4>Все способы оплаты:</h4>
                <?= PaymentSystemsList::widget([
                    'selectOptions' => [
                        'class' => 'form-control',
                        'name' => 'system'
                    ]
                ]); ?>
            </div>
            <br/>

            <div class="text-center pay-button"
                 <?php if (Yii::$app->user->isGuest): ?>style="display: block;"<?php endif; ?>>
                <input type="submit" value="Оплатить" class="btn btn-orange"/>
            </div>
        </div>

    </div>

<?= Html::endForm() ?>