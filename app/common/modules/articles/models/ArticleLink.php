<?php

namespace common\modules\articles\models;

use yii\helpers\ArrayHelper;

/**
 * Статьи
 * @package common\modules\articles\models
 */
class ArticleLink extends \yii\db\ActiveRecord
{
    public $file;

    public $imagesPath = '@frontend/web/images';

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new ArticleQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%articles_adv_links}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'ID_Статьи',
            'thumb_img' => 'Имя файла изображения',
            'file' => 'Выбрать изображение',
            'title' => 'Заголовок',
            'link_page' => 'Ссылка на страницу',
            'text' => 'Текст',
        ];
    }

    public function getArticlesList()
    {
        $articles = Article::find()->orderBy(['date_create' => SORT_DESC])->asArray()->all();

        return ArrayHelper::map($articles, 'article_id', 'name');
    }
}
