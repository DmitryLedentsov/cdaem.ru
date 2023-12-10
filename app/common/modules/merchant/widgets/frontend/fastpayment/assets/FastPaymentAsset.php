<?php

namespace common\modules\merchant\widgets\frontend\fastpayment\assets;

use yii\web\AssetBundle;

class FastPaymentAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/merchant/widgets/frontend/fastpayment/assets';

    public $css = [

    ];

    public $js = [
        '/_new/vendor/display-validation.min.js', // для отчистки сообщений валидации формы оплаты
        '/_new/vendor/datetimepicker/datetimepicker.js', // для пикера даты
        'js/fast-payment.js',
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
