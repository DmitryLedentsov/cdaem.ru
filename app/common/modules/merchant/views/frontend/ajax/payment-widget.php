<?php
/**
 * Оплата покупки сервиса
 * @var \yii\web\View this
 * @var string $service
 * @var array $data
 */

use yii\helpers\Html;
use common\modules\partners\models\Service;

try {
    $service = Yii::$app->service->load($service);
    $defaultDesk = false;
    $recommended = '';
    $description = '';
    $customViewByServiceContactsOpenForReservation = false;

    switch ($service::NAME) {

        case Service::SERVICE_CONTACTS_OPEN_TO_USER:
        case Service::SERVICE_CONTACTS_OPEN_FOR_TOTAL_BID:
            $defaultDesk = true;
            break;

        case Service::SERVICE_ADVERTISING_TOP_SLIDER:
            $description = Html::tag('p', $service->getName(), ['style' => 'font-weight :bold']);
            $description .= Html::tag('p', 'После оплаты Ваше объявление будет показываться в рекламном слайдере.');
            break;

        case Service::SERVICE_CONTACTS_OPEN_FOR_RESERVATION:
            $defaultDesk = true;
            $customViewByServiceContactsOpenForReservation = true;
            $reservation_id = isset($data['reservation_id']) ? (int)$data['reservation_id'] : '0';
            $helpUrl = Html::a('помощь', ['/pages/default/index', 'url' => 'help'], ['target' => '_blank']);

            $recommended = '<div id="recommended">';

            $recommended .= '<p class="text-center">Воспользоваться услугой бронирования:</p><p class="text-center"><span class="btn btn-orange reservation-send" data-type="confirm" data-reservation="' . $reservation_id . '">Подтвердить бронь</span></p>';
            $recommended .= '<br/>';
            $recommended .= '<p class="text-center">Воспользоваться услугой "открыть контакты":</p><p class="text-center"><span id="c_open" class="btn btn-orange">Открыть контакты</span></p></div>';
            break;
    }


    if ($defaultDesk) {
        $description = Html::tag('p', $service->getName(), ['style' => 'font-weight :bold']);
        $description .= Html::tag('p', 'Услуга платная, после оплаты, Вам на указанный Email будет отправлено письмо со всеми контактными данными, указанными владельцем объявления.');
    }

    $result = \frontend\modules\merchant\widgets\fastpayment\FastPayment::widget([
        'description' => $description,
        'service' => $service::NAME,
        'data' => $data,
    ]);
    $result = Html::tag('div', $result, $customViewByServiceContactsOpenForReservation ? ['style' => 'display: none', 'id' => 'fast-payment-widget'] : []);
    $result = $result . $recommended;
} catch (\Exception $e) {
    $result = Html::tag('div', 'Возникла критическая ошибка, пожалуйста попробуйте еще раз или обратитесь в службу технической поддержки.', [
        'class' => 'alert alert-danger'
    ]);
    /*$result = Html::tag('div', $e->getMessage(), [
        'class' => 'alert alert-danger'
    ]);*/
}

echo \yii\helpers\Html::tag('div', $result, [
    'class' => 'modal fade',
    'id' => 'modal-payment-widget',
    'data-title' => 'Оплата'
]);


if ($customViewByServiceContactsOpenForReservation) : ?>
    <script>
        $(function () {
            $(document).on('click', '#c_open', function (e) {
                e.preventDefault();
                $('#recommended').hide();
                $('#fast-payment-widget').show();
            });
        });
    </script>
<?php endif; ?>