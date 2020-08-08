<?php

namespace frontend\modules\merchant\widgets\fastpayment\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Менеджер ресурсов
 */
class FastPaymentAsset extends AssetBundle
{
    public $sourcePath = '@frontend/modules/merchant/widgets/fastpayment/assets';

    public $css = [

    ];

    public $js = [
        'js/fast-payment.js',
    ];

    public $depends = [
        \yii\web\JqueryAsset::class,
    ];
}
