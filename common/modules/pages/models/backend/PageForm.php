<?php

namespace common\modules\pages\models\backend;

use common\modules\pages\models\Page;
use yii\base\Model;
use Yii;

/**
 * Page Form
 * @package common\modules\pages\models
 */
class PageForm extends Model
{
    public $page_id;
    public $url;
    public $title;
    public $description;
    public $keywords;
    public $name;
    public $text;
    public $status;
    public $active;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Page())->attributeLabels(), [

        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['url', 'title', 'description', 'keywords', 'name', 'text', 'status', 'active'],
            'update' => ['url', 'title', 'description', 'keywords', 'name', 'text', 'status', 'active'],
            'delete' => [],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url', 'title', 'description', 'keywords', 'name', 'text', 'status', 'active'], 'required'],
            [['url', 'title', 'description', 'keywords', 'name', 'text', 'status', 'active'], 'required'],
            
            [['page_id', 'status', 'active'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['title', 'description', 'keywords'], 'string'],
            ['text', 'string'],

            // Url адрес
            ['url', 'unique', 'targetAttribute' => 'url', 'targetClass' => Page::className(), 'filter' =>
                ($this->scenario == 'create') ? null : function ($query) {
                    $query->andWhere(['<>', 'page_id', $this->page_id]);
                }
            ],
            ['url', 'string', 'min' => 1, 'max' => 32],
            ['url', 'match', 'pattern' => '/^[a-zA-Z0-9-_.]+$/'],
        ];
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new Page();
        $model->setAttributes($this->getAttributes(), false);

        if (!$model->save(false)) {
            return false;
        }

        $this->page_id = $model->page_id;
        return true;
    }

    /**
     * Редктировать
     * @param Page $model
     * @return bool
     */
    public function update(Page $model)
    {
        $model->setAttributes($this->getAttributes(), false);
        return $model->save(false);
    }

    /**
     * Удалить
     * @param Page $model
     * @return mixed
     */
    public function delete(Page $model)
    {
        return $model->delete();
    }
}
