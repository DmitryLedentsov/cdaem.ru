<?php

namespace common\modules\articles\models\backend;

use common\modules\articles\models\ArticleLink;
use yii\web\UploadedFile;
use yii\base\Model;
use Yii;

/**
 * Article
 * @package common\modules\articles\models\backend
 */
class ArticleLinkForm extends Model {

   
    public $article_id;
    public $id;
    public $title;
    public $text;
    public $thumb_img;
    public $link_page;
    public $file;
    public $imagesPath = '@frontend/web/images';

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return array_merge((new ArticleLink())->attributeLabels());
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        return [
            'create' => ['title', 'text', 'thumb_img', 'link_page', 'article_id'],
            'update' => ['title', 'text', 'thumb_img', 'link_page', 'article_id'],
            'delete' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
                [['file'], 'file', 'extensions' => 'jpg, png'],
                [['id', 'article_id', 'visible', 'status'], 'integer'],
                [['link_page', 'text', 'thumb_img'], 'string', 'max' => 255],
                [['title'], 'string'],
                [['title', 'text', 'link_page', 'article_id'], 'required', 'on' => 'create'],
                [['title', 'text', 'link_page', 'article_id'], 'required', 'on' => 'update'],
    ];
    }

    /**
     * Создать ArticleLinks
     * @return bool
     */
    public function create() {
        $model = new ArticleLink();
        $model->setAttributes($this->getAttributes(), false);
       
        if (!$model->save(false)) {
            return false;
        }

        $this->id = $model->id;
        return true;
    }

    /**
     * Редктировать
     * @param ArticleLinks $model
     * @return bool
     */
    public function update(ArticleLink $model) {
        $model->setAttributes($this->getAttributes(), false);
        return $model->save(false);
    }

    /**
     * Удалить
     * @param ArticleLinks $model
     * @return mixed
     */
    public function delete(ArticleLink $model) {
        return $model->delete();
    }

}
