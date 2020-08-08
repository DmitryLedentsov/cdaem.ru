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
        return $this->form->field($this->model, $this->name, [
            'template' => '<label class="control-label">&nbsp;</label><div class="code">{input}{error}</div>',
            'options' => ['class' => 'form-group verifycode'],
        ])->widget(\himiklab\yii2\recaptcha\ReCaptcha::class);
    }

    /**
     * @return string
     */
    public static function getClassValidator()
    {
        return \himiklab\yii2\recaptcha\ReCaptchaValidator::class;
    }
}
