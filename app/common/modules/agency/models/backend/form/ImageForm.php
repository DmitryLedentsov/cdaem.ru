<?php

namespace common\modules\agency\models\backend\form;

use yii\base\Model;
use common\modules\agency\models\Image;

/**
 * Class ImageForm
 * @package common\modules\agency\models\backend\form
 */
class ImageForm extends Model
{
    public $image_id;

    public $title;

    public $alt;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Image())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => ['title', 'alt'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'string'],
            ['alt', 'string']
        ];
    }

    /**
     * Редактировать
     *
     * @param Image $model
     * @return bool
     */
    public function update(Image $model)
    {
        $model->setAttributes($this->getAttributes(), false);

        return $model->save(false);
    }
}
