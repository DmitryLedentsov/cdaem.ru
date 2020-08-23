<?php

namespace common\modules\merchant\widgets\frontend\robokassa;

use yii\base\Widget;

/**
 * Class RenderForm
 * @package common\modules\merchant\widgets\frontend\robokassa
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
