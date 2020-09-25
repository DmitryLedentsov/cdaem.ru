<?php

namespace common\modules\articles\models\backend;

use yii\base\Model;
use common\modules\geo\models\City;
use common\modules\articles\models\Article;

/**
 * Article
 * @package common\modules\articles\models\backend
 */
class ArticleForm extends Model
{
    public $article_id;

    public $slug;

    public $name;

    public $short_text;

    public $title;

    public $description;

    public $keywords;

    public $full_text;

    public $visible;

    public $status;

    public $city;

    public $date_create;

    public $title_img;

    public $background;

    public $file;

    public $bgfile;

    public $imagesPath = '@frontend/web/images';

    public $imagesPath2 = '@frontend/web/images';

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Article())->attributeLabels(), [
            'city' => 'Поддомен (город)'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['city', 'slug', 'name', 'short_text', 'title', 'description', 'keywords', 'full_text', 'visible', 'status', 'title_img', 'background'],
            'update' => ['city', 'slug', 'name', 'short_text', 'title', 'description', 'keywords', 'full_text', 'visible', 'status', 'date_create', 'title_img', 'background'],
            'delete' => [],
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
            [['file'], 'file', 'extensions' => 'jpg, png'],
            [['bgfile'], 'file', 'extensions' => 'jpg, png'],
            [['article_id', 'visible', 'status'], 'integer'],
            [['slug', 'name', 'short_text', 'title_img'], 'string', 'max' => 255],
            [['title', 'description', 'keywords'], 'string'],
            ['date_create', 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['full_text'], 'string'],
            [['slug', 'name', 'short_text', 'title', 'description', 'keywords', 'full_text', 'visible', 'status'], 'required', 'on' => 'create'],
            [['slug', 'name', 'short_text', 'title', 'description', 'keywords', 'full_text', 'visible', 'status'], 'required', 'on' => 'update'],
            // Url адрес
            ['slug', 'unique', 'targetAttribute' => 'slug', 'targetClass' => Article::class, 'filter' =>
                ($this->scenario == 'create') ? null : function ($query) {
                    $query->andWhere(['<>', 'article_id', $this->article_id]);
                }
            ],
            ['slug', 'string', 'min' => 1, 'max' => 32],
            ['slug', 'match', 'pattern' => '/^[a-zA-Z0-9-_.]+$/'],
        ];
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new Article();
        $model->setAttributes($this->getAttributes(), false);
        $model->date_create = date('Y-m-d H:i:s');

        if (!$model->save(false)) {
            return false;
        }

        $this->article_id = $model->article_id;

        return true;
    }

    /**
     * Редктировать
     * @param Article $model
     * @return bool
     */
    public function update(Article $model)
    {
        $model->setAttributes($this->getAttributes(), false);
        //$model->date_update = date('Y-m-d H:i:s');
        return $model->save(false);
    }

    /**
     * Удалить
     * @param Article $model
     * @return mixed
     */
    public function delete(Article $model)
    {
        return $model->delete();
    }
}
