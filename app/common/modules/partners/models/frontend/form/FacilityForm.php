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
            $facilities = [];
            foreach ($this->facilities as $alias => $active) {
                if ($active === '1') {
                    $facility = Facility::findOne(['alias' => $alias]);
                    $facilities[] = $facility;
                }
            }

            $this->facilities = $facilities;
            return true;
        }

        return false;
    }
}
