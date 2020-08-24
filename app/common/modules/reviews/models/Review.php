<?php

namespace common\modules\reviews\models;

use Yii;
use yii\db\ActiveRecord;
use common\modules\reviews\traits\ModuleTrait;
use common\modules\partners\models\Apartment;

/**
 * Review
 * @package common\modules\reviews\models
 */
class Review extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reviews}}';
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new ReviewQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'review_id' => '№',
            'apartment_id' => 'Апартаменты',
            'user_id' => 'Пользователь',
            'text' => 'Отзыв',
            'match_description' => 'Описание',
            'price_quality' => 'Цена и Качество',
            'cleanliness' => 'Чистота',
            'entry' => 'Заселение',
            'date_create' => 'Дата создания',
            'visible' => 'Отображение',
            'moderation' => 'Модерация',
            'user' => 'Пользователь',
            'rating' => 'Общий рейтинг',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApartment()
    {
        return $this->hasOne(Apartment::class, ['apartment_id' => 'apartment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::class, ['id' => 'user_id']);
    }

    /**
     * Кол-во отзывов у апартаментов
     * @param null $apartmentId
     * @return int
     */
    public static function getCountReviewsToApartment($apartmentId = null)
    {
        $query = static::find();

        if ($apartmentId) {
            $query->where('apartment_id = :apartment_id', [':apartment_id' => $apartmentId]);
        }

        return $query->moderation()
            ->count();
    }

    /**
     * Кол-во отзывов у апартаментов определенного пользователя
     * @param null $apartmentId
     * @param null $userId
     * @return int
     */
    public static function getCountReviewsToApartmentByUser($apartmentId = null, $userId = null)
    {
        $userId = $userId ? $userId : Yii::$app->user->id;

        return static::find()
            ->joinWith([
                'apartment' => function ($query) use ($apartmentId, $userId) {
                    $query->where(Apartment::tableName() . '.user_id = :user_id', [':user_id' => $userId]);
                    if ($apartmentId) {
                        $query->andWhere(Apartment::tableName() . '.apartment_id = :apartment_id', [':apartment_id' => $apartmentId]);
                    }
                },
            ])
            ->moderation()
            ->count();
    }

    /**
     * @return array Соответствие описанию
     */
    public static function getRatingMatchDescriptionArray()
    {
        return [
            5 => [
                'label' => 'Отлично',
                'style' => 'color: #00C184',
            ],
            4 => [
                'label' => 'Хорошо',
                'style' => 'color: #8DC000',
            ],
            3 => [
                'label' => 'Сомнительно',
                'style' => 'color: #FFC741',
            ],
            2 => [
                'label' => 'Плохо',
                'style' => 'color: #FF6969',
            ],
            1 => [
                'label' => 'Очень плохо',
                'style' => 'color: #FF2B00',
            ],
        ];
    }

    /**
     * @return array Цена и Качество
     */
    public static function getRatingPriceAndQualityArray()
    {
        return [
            5 => [
                'label' => 'Отлично',
                'style' => 'color: #00C184',
            ],
            4 => [
                'label' => 'Хорошо',
                'style' => 'color: #8DC000',
            ],
            3 => [
                'label' => 'Сомнительно',
                'style' => 'color: #FFC741',
            ],
            2 => [
                'label' => 'Плохо',
                'style' => 'color: #FF6969',
            ],
            1 => [
                'label' => 'Очень плохо',
                'style' => 'color: #FF2B00',
            ],
        ];
    }

    /**
     * @return array Чистота
     */
    public static function getRatingCleanlinessArray()
    {
        return [
            5 => [
                'label' => 'Отлично',
                'style' => 'color: #00C184',
            ],
            4 => [
                'label' => 'Хорошо',
                'style' => 'color: #8DC000',
            ],
            3 => [
                'label' => 'Сомнительно',
                'style' => 'color: #FFC741',
            ],
            2 => [
                'label' => 'Плохо',
                'style' => 'color: #FF6969',
            ],
            1 => [
                'label' => 'Очень плохо',
                'style' => 'color: #FF2B00',
            ],
        ];
    }

    /**
     * @return array Заселение
     */
    public static function getRatingEntryArray()
    {
        return [
            5 => [
                'label' => 'Отлично',
                'style' => 'color: #00C184',
            ],
            4 => [
                'label' => 'Хорошо',
                'style' => 'color: #8DC000',
            ],
            3 => [
                'label' => 'Сомнительно',
                'style' => 'color: #FFC741',
            ],
            2 => [
                'label' => 'Плохо',
                'style' => 'color: #FF6969',
            ],
            1 => [
                'label' => 'Очень плохо',
                'style' => 'color: #FF2B00',
            ],
        ];
    }
}
