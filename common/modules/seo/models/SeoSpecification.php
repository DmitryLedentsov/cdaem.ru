<?php

namespace common\modules\seo\models;

use Yii;

/**
 * Таблица "{{%seo_specifications}}".
 *
 * @property integer $id
 * @property string $city
 * @property string $url
 * @property string $title
 * @property string $description
 * @property string $keywords
 */
class SeoSpecification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%seo_specifications}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city' => 'Поддомен',
            'url' => 'Url',
            'title' => 'Title',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'date_create' => 'Дата создания',
            'date_update' => 'Дата редактирования',
            'service_head' => 'Служебный head',
            'service_footer' => 'Служебный footer'
        ];
    }
}