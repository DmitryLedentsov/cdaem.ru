<?php

namespace backend\modules\merchant\models;

use common\modules\users\models\User;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * @inheritdoc
 */
class PaymentForm extends Payment
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            [
                'class' => \yii\behaviors\TimestampBehavior::class,
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => 'date_update',
                'value' => function ($event) {
                    return date('Y-m-d H:i:s');
                }
            ],
        ];

        return array_merge(parent::behaviors(), $behaviors);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['system', 'type', 'funds']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['system', 'type', 'funds'], 'required'],
            ['type', 'in', 'range' => array_keys($this->typeArray)],
            ['system', 'in', 'range' => array_keys(Yii::$app->getModule('merchant')->systems['merchant'])],
            ['funds', 'integer', 'min' => 1, 'max' => 100000],
        ];
    }

    /**
     * Пополнить или списать средства со счета пользователя
     * @return bool
     */
    public function process()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->type == self::COSTS) {
                // Списать средства
                Yii::$app->balance
                    ->setModule(Yii::$app->getModule('merchant')->id)
                    ->setUser(User::findOne($this->user_id))
                    ->costs($this->funds, $this->system);

            } else {
                // Начислить средства
                Yii::$app->balance
                    ->setModule(Yii::$app->getModule('merchant')->id)
                    ->setUser(User::findOne($this->user_id))
                    ->billing($this->funds, $this->system);
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
}
