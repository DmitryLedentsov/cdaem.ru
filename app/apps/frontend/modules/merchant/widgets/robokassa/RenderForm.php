<?php

namespace frontend\modules\merchant\widgets\robokassa;

use yii\base\Widget;

/**
 * Class RenderForm
 * @package frontend\modules\merchant\widgets\robokassa
 */
class RenderForm extends Widget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('form', [

        ]);
    }
}