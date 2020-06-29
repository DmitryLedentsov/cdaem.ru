<?php

namespace common\components;

/**
 * @inheritdoc
 */
class Formatter extends \yii\i18n\Formatter
{
    /**
     * @inheritdoc
     */
    public function asCurrency($value, $currency = null, $options = [], $textOptions = [])
    {
        /*if ((int)$value === 0) {
            // return '<span title="Договорная цена">Договор</span>';
            return '';
        }*/

        if ($currency == 'RUB' || $currency == 'RUR') {
            $formatter = new \yii\i18n\Formatter;
            //return $formatter->asDecimal($value) . '&nbsp;<span class="currency-rub">P<span class="s"></span></span>';
            return $formatter->asDecimal($value) . '&nbsp;<span class="hyphen" style="font-weight: normal"></span><span class="ruble" style="font-weight: normal">p</span><span class="dot" style="font-weight: normal">уб.</span>';
        }

        return parent::asCurrency($value, $currency, $options, $textOptions);
    }
}
