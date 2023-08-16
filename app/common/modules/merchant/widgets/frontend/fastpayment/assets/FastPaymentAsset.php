<?php

namespace common\modules\merchant\widgets\frontend\fastpayment\assets;

use yii\web\AssetBundle;

class FastPaymentAsset extends AssetBundle
{
    public $sourcePath = '@common/modules/merchant/widgets/frontend/fastpayment/assets';

    public $css = [

    ];

    public $js = [
        'js/fast-payment.js',
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
