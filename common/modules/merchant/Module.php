<?php

namespace common\modules\merchant;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * Общий модуль [[Merchant]]
 * Осуществляет всю работу c мерчант
 */
class Module extends \yii\base\Module
{
    /**
     * @var array Типы денежного оборота
     */
    public $systems;

    /**
     * @var integer Количество записей на главной странице модуля.
     */
    public $recordsPerPage = 18;

    /**
     * @var string Вид текущей валюты
     */
    public $viewMainCurrency = 'RUB';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        /**
         * Включаем в список систем все именования сервисов
         */
        $services = array_map(function($services) {
            $result = [];
            foreach ($services as $service => $name) {
                $result[$service] = [
                    'label' => Yii::$app->service->load($service)->getName(),
                    'color' => '',
                ];
            }
            return $result;
        }, Yii::$app->service->services);

        $this->systems = [
            'merchant' => array_merge(\frontend\modules\merchant\models\Invoice::getTypesArray(), [
                'system' => [
                    'label' => 'Система',
                    'style' => '',
                ],
                'fine' => [
                    'label' => 'Штраф',
                    'style' => '',
                ],
                'refund' => [
                    'label' => 'Возврат',
                    'style' => '',
                ],
            ]),
        ];

        $this->systems = array_merge($this->systems, $services);

        $this->systems = ArrayHelper::merge($this->systems, [
            'partners' => \common\modules\partners\models\ReservationDeal::getTypesArray()
        ]);
    }
    
}
