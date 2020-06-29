<?php

namespace common\modules\reviews\models\backend;

use common\modules\reviews\models\Review;
use yii\base\Model;
use yii;

/**
 * Форма "Написать отзыв"
 * @package common\modules\reviews\models\backend
 */
class ReviewForm extends Model
{
    public $review_id;
    public $apartment_id;
    public $match_description;
    public $price_quality;
    public $cleanliness;
    public $entry;
    public $user_id;
    public $visible;
    public $moderation;
    public $text;
    public $date_create;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Review())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['apartment_id', 'match_description', 'price_quality', 'cleanliness', 'entry', 'text', 'moderation', 'visible', 'user_id'],
            'update' => ['apartment_id', 'match_description', 'price_quality', 'cleanliness', 'entry', 'text', 'moderation', 'visible', 'user_id'],
            'delete' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Защитный код
            ['apartment_id', 'required'],
            ['apartment_id', 'exist', 'targetClass' => '\common\modules\partners\models\Apartment', 'targetAttribute' => 'apartment_id'],

            // Защитный код
            ['user_id', 'required'],
            ['user_id', 'exist', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'id'],

            // Отзыв
            ['text', 'required'],
            ['text', 'string', 'min' => 20],

            // Очки рейтинга
            [['match_description', 'price_quality', 'cleanliness', 'entry'], 'required'],
            ['match_description', 'in', 'range' => array_keys(Review::getRatingMatchDescriptionArray())],
            ['price_quality', 'in', 'range' => array_keys(Review::getRatingPriceAndQualityArray())],
            ['cleanliness', 'in', 'range' => array_keys(Review::getRatingCleanlinessArray())],
            ['entry', 'in', 'range' => array_keys(Review::getRatingEntryArray())],


            ['moderation', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],
            ['visible', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],

            // Пользовательское соглашение
            ['agreement', 'required'],
            ['agreement', 'compare', 'compareValue' => '1', 'message' => 'Необходимо согласиться с условиями пользовательского соглашения.']
        ];
    }

    /**
     * Создать
     * @return bool
     */
    public function create()
    {
        $model = new Review();
        $model->setAttributes($this->getAttributes(), false);
        $model->date_create = date('Y-m-d H:i:s');

        if (!$model->save(false)) {
            return false;
        }

        $this->review_id = $model->review_id;
        return true;
    }

    /**
     * Редктировать
     * @param Review $model
     * @return bool
     */
    public function update(Review $model)
    {
        $model->setAttributes($this->getAttributes(), false);
        return $model->save(false);
    }

    /**
     * Удалить
     * @param Review $model
     * @return mixed
     */
    public function delete(Review $model)
    {
        return $model->delete();
    }

}
