<?php

namespace common\modules\seo\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * Seotext
 * @package common\modules\seo\models
 */
class Seotext extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%seo_text}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'url' => 'URL Адрес',
            'type' => 'Тип',
            'text' => 'Текст',
            'visible' => 'Отображение',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['url', 'type', 'text', 'visible'],
            'update' => ['url', 'type', 'text', 'visible'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'type', 'text', 'visible'], 'required'],
            [['url', 'type'], 'unique', 'targetAttribute' => ['url', 'type']],
            ['type', 'in', 'range' => array_keys($this->typeArray)],
            ['visible', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],
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

    const HEADER = 'header';
    const FOOTER = 'footer';

    /**
     * @return array
     */
    public static function getTypeArray()
    {
        return [
            self::HEADER => 'В шапке',
            self::FOOTER => 'Внизу',
        ];
    }
}