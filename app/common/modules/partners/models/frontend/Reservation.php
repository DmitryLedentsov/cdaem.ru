<?php

namespace common\modules\partners\models\frontend;

/**
 * @inheritdoc
 */
class Reservation extends \common\modules\partners\models\Reservation
{
    /**
     * Возвращает полный адресс
     * @return string
     */
    public function getFullAddress()
    {
        if ($this->city) {
            return $this->city->country->name . ', ' . $this->city->name . ', ' . $this->address;
        }

        return '';
    }
}
