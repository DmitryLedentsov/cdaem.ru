<?php

namespace common\validators;

use Yii;
use yii\validators\Validator;

/**
 * Class PhoneValidator
 * @package common\validators
 */
class PhoneValidator extends Validator
{
    /**
     * @var string
     */
    public $defaultCountryCode = 'RU';

    /**
     * @var bool
     */
    public $leadingPlus = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('app', 'phone-validator-message');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        $phone = $model->$attribute;

        if ($this->leadingPlus) {
            if (mb_substr($phone, 0, 1) !== '+') {
                $phone = '+' . $phone;
            }
        }

        try {
            $swissNumberProto = $phoneUtil->parse($phone, $this->defaultCountryCode);
            $isValid = $phoneUtil->isValidNumber($swissNumberProto);
        } catch (\libphonenumber\NumberParseException $e) {
            $isValid = false;
        }

        if (!$isValid) {
            $this->addError($model, $attribute, $this->message);
        }
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        return false;
    }
}
