<?php

namespace frontend\modules\merchant\widgets\robokassa;

use yii\helpers\Html;
use yii\base\Widget;
use yii\web\View;
use Yii;

class PaymentSystemsList extends Widget
{
    /**
     * @var bool
     */
    public $select = true;

    /**
     * @var string
     */
    public $language = 'ru';

    /**
     * @var array
     */
    public $selectOptions = [];

    /**
     * @var string
     */
    private $_apiUrl = 'https://auth.robokassa.ru/Merchant/WebService/Service.asmx/GetCurrencies?MerchantLogin={login}&Language={lang}';

    /**
     * @var array
     */
    private $_list = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_apiUrl = str_replace('{lang}', $this->language, $this->_apiUrl);
        $this->_apiUrl = str_replace('{login}', Yii::$app->robokassa->mrchLogin, $this->_apiUrl);

        // Получаем данные о доступных платежных системах Robokassa
        $xml_str = @file_get_contents($this->_apiUrl, 0);

        if (!empty($xml_str)) {
            $movies = new \SimpleXMLElement($xml_str);
            if (isset($movies->Groups->Group)) {
                foreach ($movies->Groups->Group as $group) {
                    $items = [];
                    if (isset($group->Items->Currency)) {
                        foreach ($group->Items->Currency as $currency) {
                            $items[] = [
                                'label' => (string)$currency->attributes()->Label,
                                'name' => (string)$currency->attributes()->Name,
                                'maxValue' => (int)$currency->attributes()->MaxValue,
                            ];
                        }
                    }
                    $this->_list[] = [
                        'code' => (string)$group->attributes()->Code,
                        'description' => (string)$group->attributes()->Description,
                        'items' => $items,
                    ];
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->select) {
            $options = '';

            if (is_array($this->_list)) {
                foreach ($this->_list as $curency) {
                    $optgroup = '';
                    foreach ($curency['items'] as $item) {
                        $optgroup .= Html::tag('option', $item['name'], [
                            'value' => $item['label'],
                            'data-maxValue' => $item['maxValue']
                        ]);
                    }
                    $options .= Html::tag('optgroup', $optgroup, [
                        'label' => $curency['description']
                    ]);
                }
            }

            return Html::tag('select', $options, $this->selectOptions);
        }
        return $this->_list;
    }

}