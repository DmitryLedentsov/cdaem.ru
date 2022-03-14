<?php

namespace common\modules\partners\models\frontend\form;

use common\modules\partners\models\frontend\Facility;
use yii\debug\models\search\Db;
use yii\web\UploadedFile;

/**
 * @inheritdoc
 */
class FacilityForm extends Facility
{
    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'user-create' => self::OP_ALL,
            'user-update' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'user-create' => ['facilities'],
            'user-update' => ['facilities'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return []; // TODO ограничить списком удобств?
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            // Обрабатываем удобства
            $facilities = [];
            foreach ($this->facilities as $alias => $value) {
                if ($value !== '0' && $value !== []) {
                    // Массив значений доп. удобств храним как строку
                    if (is_array($value)) {
                        $value = serialize($value);
                    }

                    $facility = Facility::findOne(['alias' => $alias]);
                    $facility->setValue($value);
                    $facilities[] = $facility;
                }
            }

            $this->facilities = $facilities;
            // dd($facilities);
            return true;
        }

        return false;
    }
}
