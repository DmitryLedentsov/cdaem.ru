<?php
/*
    Стоимость сервиса и подтверждение покупки
    @var \yii\web\View this
    @var string $service
    @var array $calculation
    @var datetime $date
*/

use yii\helpers\Html;
use common\modules\partners\models\Service;

$serviceLogo = '';

switch ($service::NAME) {
    case Service::SERVICE_APARTMENT_CONTACTS_OPEN:
        $serviceLogo = '<span class="service-icon-small contacts-open"></span>';
        break;
    case Service::SERVICE_ADVERTISING_TOP_SLIDER:
        $serviceLogo = '<span class="service-icon-small top-slider"></span>';
        break;
    case Service::SERVICE_ADVERTISING_IN_SECTION:
        $serviceLogo = '<span class="service-icon-small a-one-section"></span>';
        break;
    case Service::SERVICE_ADVERT_TOP_POSITION:
        $serviceLogo = '<span class="service-icon-small top-position"></span>';
        break;
    case Service::SERVICE_ADVERT_SELECTED:
        $serviceLogo = '<span class="service-icon-small selected"></span>';
        break;
    case Service::SERVICE_ADVERT_IN_TOP:
        $serviceLogo = '<span class="service-icon-small top"></span>';
        break;
}
?>

<div class='calculate clearfix'>
    <div class='service-logo'>
        <?= $serviceLogo ?>
        <p class='name'><?= $service->getName() ?></p>
        <p class='selected'>
            Объектов выбрано: <b><?= $calculation['countSelected'] ?></b>
        </p>
    </div>
    <div class='service-info'>

        Активация услуги: <b><?= Yii::$app->BasisFormat->helper('DateTime')->toFullDate($date) ?></b><br/>
        <?php if ($calculation['days']): ?>Действует
            <b><?= Yii::t('app', '{n, plural, one{# день} few{# дня} many{# дней} other{# дня} }', ['n' => $calculation['days']]) ?></b><?php endif; ?>

        <br/>

        <?php if ($calculation['discount']): ?>
            <p class='price'>Общая стоимость:
                <b><?= Yii::$app->formatter->asCurrency($calculation['price'] + $calculation['discount'], 'RUB') ?></b>
            </p> <br/>
            <p class='discount' style="margin: 0">Ваша скидка составляет:
                <b><?= Yii::$app->formatter->asCurrency($calculation['discount'], 'RUB') ?></b></p>
        <?php else: ?>
            <br/>
            <div class="alert alert-warning">
                <b>Внимание</b>
                При текущей покупке, Вы не получаете скидку!
            </div>
        <?php endif; ?>
        <p class='total-price'>Всего к оплате:
            <b><?= Yii::$app->formatter->asCurrency($calculation['price'], 'RUB') ?></b></p>
        <?php if (Yii::$app->user->can('admin')) : ?>
            <div class="alert alert-info">
                Вы являетесь администратором! Любая стоимость сервиса будет сброшена на
                <b><?= Yii::$app->formatter->asCurrency(1, 'RUB') ?></b>
            </div>
        <?php endif; ?>
    </div>
</div>