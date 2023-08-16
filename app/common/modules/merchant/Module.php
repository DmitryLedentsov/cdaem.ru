<?php

namespace common\modules\merchant;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Общий модуль [[Merchant]]
 * Осуществляет всю работу c мерчант
 */
class Module extends \yii\base\Module
{
    /**
     * Типы денежного оборота
     */
    public array $systems = [];

    /**
     * Количество записей на главной странице модуля.
     */
    public int $recordsPerPage = 18;

    /**
     * Вид текущей валюты
     */
    public string $viewMainCurrency = 'RUB';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        /**
         * Включаем в список систем все именования сервисов
         */
        $services = array_map(function ($services) {
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
            'merchant' => array_merge(\common\modules\merchant\models\Invoice::getTypesArray(), [
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
