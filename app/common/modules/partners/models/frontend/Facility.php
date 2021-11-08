<?php

namespace common\modules\partners\models\frontend;

/**
 * @inheritdoc
 */
class Facility extends \common\modules\partners\models\Facility
{
    /**
     * @var array
     */
    public $facilities;

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        $labels = parent::attributeLabels();
        $labels['facilities'] = 'Удобства';

        return $labels;
    }
}
