<?php

namespace common\modules\realty\models;

use yii\helpers\ArrayHelper;

/**
 * Типы аренды
 * @package common\modules\realty\models
 */
class RentType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new RentTypeQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%realty_rent_type}}';
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'slug', 'meta_title', 'meta_description', 'meta_keywords', 'icons', 'short_description', 'agency_rules', 'sort', 'agency_seo_short_desc'],
            'update' => ['name', 'slug', 'meta_title', 'meta_description', 'meta_keywords', 'icons', 'short_description', 'agency_rules', 'sort', 'agency_seo_short_desc'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rent_type_id' => '№',
            'name' => 'Название',
            'slug' => 'Транслитерация',
            'icons' => 'иконки в JSON формате',
            'short_description' => 'Краткое описание',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
            'agency_rules' => 'Правила заселения агенства',
            'visible' => 'Отображается на сайте',
            'sort' => 'Сортировка',
            'agency_seo_short_desc' => 'Короткий сео текст'
        ];
    }

    /**
     * Получить тип аренды по слагу
     * @param $slug
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findRentTypeBySlug($slug)
    {
        return self::find()
            ->where('slug = :slug', ['slug' => $slug])
            ->asArray()
            ->one();
    }

    /**
     * Список типов аренды
     * @return array
     */
    public static function rentTypeslist()
    {
        $rentTypes = self::find()
            ->where(['!=', 'slug', '/'])
            ->select(['rent_type_id', 'name'])
            ->asArray()
            ->all();

        return ArrayHelper::map($rentTypes, 'rent_type_id', 'name');
    }
}
