<?php

namespace common\modules\site\widgets;

use yii\base\Widget;

/**
 * Captcha
 * @package common\modules\site\widgets
 */
class Captcha extends Widget
{
    public $form;

    public $model;

    public $name;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (YII_DEBUG) {
            return '';
        }

        return $this->form->field($this->model, $this->name, [
            'template' => '<label class="control-label">&nbsp;</label><div class="code">{input}{error}</div>',
            'options' => ['class' => 'form-group verifycode'],
        ])->widget(\himiklab\yii2\recaptcha\ReCaptcha2::class);
    }

    /**
     * @return string
     */
    public static function getClassValidator(): string
    {
        return \himiklab\yii2\recaptcha\ReCaptchaValidator2::class;
    }

    /**
     * @param string|null $fieldName
     * @return array
     */
    public static function getValidationRules(?string $fieldName = null): array
    {
        if (YII_DEBUG) {
            return [];
        }

        $fieldName = $fieldName ? $fieldName : 'verifyCode';

        return [
            [
                [$fieldName], \himiklab\yii2\recaptcha\ReCaptchaValidator2::class,
                'uncheckedMessage' => 'Подтвердите, что Вы не робот'
            ],
        ];
    }
}
