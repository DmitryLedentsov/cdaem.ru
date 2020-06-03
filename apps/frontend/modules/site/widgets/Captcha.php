<?php

namespace frontend\modules\site\widgets;

use yii\helpers\Html;
use yii\base\Widget;
use Yii;

/**
 * Captcha
 * @package frontend\modules\site\widgets
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
        ])->widget(\himiklab\yii2\recaptcha\ReCaptcha::className());
    }

    /**
     * @return string
     */
    public static function getClassValidator()
    {
        
        return  \himiklab\yii2\recaptcha\ReCaptchaValidator::className();
    }
}
