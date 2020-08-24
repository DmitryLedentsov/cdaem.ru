<?php

namespace common\modules\partners\models\frontend\form;

use common\modules\partners\models\frontend\Image;
use yii\web\UploadedFile;

/**
 * @inheritdoc
 */
class ImageForm extends Image
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
            'user-create' => ['files'],
            'user-update' => ['files'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['files', 'required', 'on' => 'user-create'],
            [
                'files', 'image',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, gif',
                'maxFiles' => $this->module->maxUploadImages,
                'minWidth' => $this->module->previewImageResizeWidth,
                'maxSize' => $this->module->imageMaxSize
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->files = UploadedFile::getInstances($this, 'files');

            return true;
        }

        return false;
    }
}
