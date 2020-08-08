<?php

namespace backend\modules\partners\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @inheritdoc
 */
class AdvertReservation extends \common\modules\partners\models\AdvertReservation
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            'ActionBehavior' => [
                'class' => 'nepster\users\behaviors\ActionBehavior',
                'module' => $this->module->id,
                'actions' => [
                    //ActiveRecord::EVENT_BEFORE_INSERT => 'create-reservation',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update-advert-reservation',
                    ActiveRecord::EVENT_BEFORE_DELETE => 'delete-advert-reservation',
                ],
            ],
        ];

        return array_merge($behaviors, parent::behaviors());
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => [/* вотэтовот не стоит редактировать 'user_id', 'landlord_id', 'advert_id',*/
                'children', 'pets', 'clients_count',
                'more_info', 'date_arrived', 'date_out', 'landlord_open_contacts',
                'cancel', 'cancel_reason', 'date_actuality', 'closed', 'confirm'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_id', 'exist', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'id'],
            ['landlord_id', 'exist', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'id'],
            ['advert_id', 'exist', 'targetClass' => '\common\modules\partners\models\Advert', 'targetAttribute' => 'advert_id'],
            ['children', 'in', 'range' => array_keys($this->childrenArray)],
            ['pets', 'in', 'range' => array_keys($this->petsArray)],
            ['clients_count', 'in', 'range' => array_keys($this->clientsCountArray)],
            [['date_arrived', 'date_out', 'date_actuality'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_out', 'compare', 'compareAttribute' => 'date_arrived', 'operator' => '>'],
            [['closed', 'landlord_open_contacts'], 'boolean'],
            ['cancel', 'in', 'range' => array_keys($this->cancelList)],
            ['confirm', 'in', 'range' => array_keys($this->confirmList)],


            //required "Причина отмены" когда заявка отменена
            ['cancel_reason', 'required', 'when' => function ($model) {
                return $this->cancel == 2 || $this->cancel == 1 || $this->cancel == 3;
            }, 'enableClientValidation' => false, 'message' => 'Вы отменили заявку, по-этому заполните это поле'],

            //need to be empty когда заявка не отменена
            ['cancel_reason', 'boolean', 'trueValue' => false, 'falseValue' => false, 'when' => function ($model) {
                return $this->cancel == 0;
            }, 'enableClientValidation' => false, 'message' => 'Заявка не отменена, по-этому очистите это поле'],

            //required
            [['user_id', 'landlord_id', 'advert_id', 'children', 'pets', 'clients_count', 'date_arrived', 'date_out', 'date_actuality', 'more_info',
                'cancel'], 'required'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::class, ['advert_id' => 'advert_id']);
    }
}
