<?php

namespace common\modules\callback\models\backend;

use common\modules\callback\models\Callback;
use yii\base\Model;
use yii;

/**
 * Форма "Обратный звонок"
 * @package common\modules\callback\models\backend
 */
class CallbackForm extends Model
{
    public $callback_id;
    public $active;
    public $phone;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Callback())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => ['active', 'phone'],
            'delete' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['active', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],
            ['phone', '\common\validators\PhoneValidator', 'message' => 'Некорректный формат номера'],
        ];
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new Callback();
        $model->setAttributes($this->getAttributes(), false);

        if (!$model->save(false)) {
            return false;
        }

        $this->callback_id = $model->callback_id;
        return true;
    }

    /**
     * Редктировать
     * @param Callback $model
     * @return bool
     */
    public function update(Callback $model)
    {
        $oldActive = $model->active;
        $model->setAttributes($this->getAttributes(), false);

        if ($oldActive != $this->active) {
            $model->date_processed = null;
            if ($this->active) {
                $model->date_processed = date('Y-m-d H:i:s');
            }
        }

        return $model->save(false);
    }

    /**
     * Удалить
     * @param Callback $model
     * @return mixed
     */
    public function delete(Callback $model)
    {
        return $model->delete();
    }

}
