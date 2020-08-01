<?php

namespace common\modules\seo\models\backend;

use common\modules\seo\models\SeoSpecification;
use common\modules\geo\models\City;
use yii\base\Model;
use Yii;

/**
 * Class SeoSpecificationForm
 * @package backend\modules\seotext\models
 */
class SeoSpecificationForm extends Model
{
    public $id;
    public $city;
    public $url;
    public $title;
    public $description;
    public $keywords;
    public $service_head;
    public $service_footer;
    public $date_create;
    public $date_update;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['city', 'url', 'title', 'description', 'keywords', 'service_head', 'service_footer'],
            'update' => ['city', 'url', 'title', 'description', 'keywords', 'service_head', 'service_footer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['city', 'exist', 'targetAttribute' => 'name_eng', 'targetClass' => City::class, 'message' => 'Такого поддомена не существует'],
            ['city', 'default', 'value' => null],

            [['url', 'title', 'description', 'keywords'], 'string', 'max' => 255],
            [['url', 'city'], 'unique', 'targetAttribute' => ['url', 'city'], 'targetClass' => SeoSpecification::class,
                'filter' => function ($query) {
                    if ($this->id) {
                        $query->andWhere(['NOT', ['id' => $this->id]]);
                    }
                },
                'message' => 'Связка "Url" и "Поддомен" уже занята'],

            [['url', 'title', 'description', 'keywords'], 'required'],

            [['service_head', 'service_footer'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        parent::beforeValidate();

        if ($this->scenario == 'create' || $this->scenario == 'update') {
            $this->url = '/' . trim($this->url, '/');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new SeoSpecification())->attributeLabels(), [
            'city' => 'Поддомен (город)'
        ]);
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new SeoSpecification();
        $model->setAttributes($this->getAttributes(), false);
        $model->date_create = date('Y-m-d H:i:s');
        $model->date_update = $model->date_create;

        if ($model->save(false)) {
            $this->id = $model->id;
            return true;
        }

        return false;
    }

    public function update(SeoSpecification $model)
    {
        $model->setAttributes($this->getAttributes(), false);
        $model->date_update = date('Y-m-d H:i:s');

        return $model->save(false);
    }
}