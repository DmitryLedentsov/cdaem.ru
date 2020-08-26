<?php

namespace common\modules\partners\models\backend;

use yii\db\ActiveRecord;

/**
 * @inheritdoc
 * @package common\modules\partners\models\backend
 */
class ReservationFailure extends \common\modules\partners\models\ReservationFailure
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            'ActionBehavior' => [
                'class' => \nepster\users\behaviors\ActionBehavior::class,
                'module' => $this->module->id,
                'actions' => [
                    //ActiveRecord::EVENT_BEFORE_INSERT => 'create-reservation',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update-reservation-failure',
                    ActiveRecord::EVENT_BEFORE_DELETE => 'delete-reservation-failure',
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
            'update' => ['closed', 'date_to_process', 'cause_text'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['closed', 'boolean'],
            ['date_to_process', 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_to_process', 'compare', 'compareValue' => date('Y-m-d H:i:s'), 'operator' => '>'],
            ['cause_text', 'string'],
        ];
    }
}
