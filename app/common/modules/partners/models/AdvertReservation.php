<?php

namespace common\modules\partners\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\modules\users\models\User;
use common\modules\partners\traits\ModuleTrait;
use common\modules\partners\models\scopes\AdvertReservationQuery;

/**
 * This is the model class for table "{{%partners_advert_reservations}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $landlord_id
 * @property integer $advert_id
 * @property integer $children
 * @property integer $pets
 * @property integer $clients_count
 * @property string $more_info
 * @property string $date_arrived
 * @property string $date_out
 * @property string $date_actuality
 * @property string $date_create
 * @property string $date_update
 * @property integer $landlord_open_contacts
 * @property integer $confirm
 * @property integer $cancel
 * @property string $cancel_reason
 * @property integer $closed
 *
 * @property Advert $advert
 * @property User $user
 * @property User $landlord
 * @property ReservationDeal[] $partnersAdvertReservationDeals
 */
class AdvertReservation extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_advert_reservations}}';
    }

    /**
     * @inheritdoc
     */
    /*
    public function rules()
    {
        return [
            [['user_id', 'landlord_id', 'advert_id', 'date_arrived', 'date_out', 'date_actuality', 'date_create', 'date_update'], 'required'],
            [['user_id', 'landlord_id', 'advert_id', 'children', 'pets', 'clients_count', 'landlord_open_contacts', 'cancel', 'closed'], 'integer'],
            [['date_arrived', 'date_out', 'date_actuality', 'date_create', 'date_update'], 'safe'],
            [['more_info', 'cancel_reason'], 'string', 'max' => 255],
            [['advert_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnersAdverts::class, 'targetAttribute' => ['advert_id' => 'advert_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['landlord_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['landlord_id' => 'id']],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID клиент',
            'landlord_id' => 'ID владелец',
            'advert_id' => 'ID объявления',
            'children' => 'Наличие детей',
            'pets' => 'Наличие домашних животных',
            'clients_count' => 'Кол-во клиентов',
            'more_info' => 'Дополнительная информация',
            'date_arrived' => 'Дата заезда',
            'date_out' => 'Дата выезда',
            'date_actuality' => 'Актуально до',
            'date_create' => 'Дата создания',
            'date_update' => 'Дата редактирования',
            'landlord_open_contacts' => 'Арендодатель открыл контакты',
            'confirm' => 'Подтверждена',
            'cancel' => 'Отменена',
            'cancel_reason' => 'Причина отмены',
            'closed' => 'Закрыта',
        ];
    }

    /**
     * Значения полей confirm и cancel, причем BOTH для cancel не нужен
     */
    const RENTER = 1;

    const LANDLORD = 2;

    const BOTH = 3;

    /**
     * Массив доступных значений Животные
     * @return array
     */
    public function getPetsArray()
    {
        return [
            0 => 'Без животных',
            1 => 'С животными',
        ];
    }

    /**
     * Текстовое представление значения Животные
     * @return string
     */
    public function getPetsText()
    {
        return ArrayHelper::getValue($this->petsArray, $this->pets);
    }

    /**
     * Массив достпуных значений Дети
     * @return array
     */
    public function getChildrenArray()
    {
        return [
            0 => 'Без детей',
            1 => 'С детьми',
        ];
    }

    /**
     * Текстовое представление значения Дети
     * @return string
     */
    public function getChildrenText()
    {
        return ArrayHelper::getValue($this->childrenArray, $this->children);
    }

    /**
     * Массив доступных значений Кол-во человек
     * @return array
     */
    public function getClientsCountArray()
    {
        return [
            1 => 'Один',
            2 => 'Два',
            3 => 'Три',
            4 => 'Четыре',
            5 => 'Пять',
            6 => 'Шесть и более',
        ];
    }

    /**
     * Текстовое представление значения Кол-во человек
     * @return string
     */
    public function getClientsCountText()
    {
        return ArrayHelper::getValue($this->clientsCountArray, $this->clients_count);
    }

    /**
     * Массив доступных начений Продолжительности актуальности заявки
     * @return array
     */
    public function getActualityList()
    {
        return [
            1 => '15 минут',
            2 => '30 минут',
            3 => '60 минут',
            4 => '24 часа',
            5 => 'До даты заезда',
        ];
    }

    /**
     * Массив статуса отмен
     * @return array
     */
    public function getCancelList()
    {
        return [
            0 => 'Активная',
            1 => 'Отменено создателем',
            2 => 'Отменено арендодателем',
            3 => 'Отменено системой'
        ];
    }

    /**
     * Текстовое представление текущего значения статуса отмены
     * @return string
     */
    public function getCancelText()
    {
        return ArrayHelper::getValue($this->cancelList, $this->cancel);
    }

    /**
     * Массив статуса подтверждений
     * @return array
     */
    public function getConfirmList()
    {
        return [
            0 => 'Не подтверждена',
            1 => 'Подтверждена Клиентом',
            2 => 'Подтверждена Владельцем',
            3 => 'Подтверждена двумя сторонами',
        ];
    }

    /**
     * Текстовое представление текущего значения статуса подтверждения
     * @return string
     */
    public function getConfirmText()
    {
        return ArrayHelper::getValue($this->confirmList, $this->confirm);
    }

    /**
     * Текстовое представление текущего значения статуса подтверждения, предназначеное
     * для арендодателя
     * @return string
     */
    public function getConfirmOwnerText()
    {
        if ($this->confirm == 3) {
            return '<div class="alert alert-success">Заявка подтверждена обеими сторонами.</div>';
        }

        if ($this->confirm == 2) {
            return '<div class="alert alert-warning">Ожидайте подтверждения Клиентом.</div>';
        }

        if ($this->confirm == 1) {
            return '<div class="alert alert-warning">Подтверждена Клиентом. Подтвердите заявку на бронь.</div>';
        }
    }

    /**
     * Текстовое представление текущего значения статуса подтверждения, предназначеное
     * для клиента(создателя заявки)
     * @return string
     */
    public function getConfirmClientText()
    {
        if ($this->confirm == 3) {
            return '<div class="alert alert-success">Заявка подтверждена обеими сторонами.</div>';
        }

        if ($this->confirm == 2) {
            return '<div class="alert alert-warning">Подтверждена Владельцем.</div>';
        }

        if ($this->confirm == 1) {
            return '<div class="alert alert-warning">Ожидание подтверждения Владельцем.</div>';
        }
    }

    /**
     * Возвращает открыты ли контакты для авторизированного юзера
     * @param null $userId
     * @return bool
     */
    public function getIsContactsOpen($userId = null)
    {
        // Подтверждено обеими сторонами
        if ($this->confirm == 3) {
            return true;
        }

        // Авторизированный пользователь - Арендатор и открывал контакты этой заявки
        if ($this->landlord_open_contacts and $this->landlord_id == Yii::$app->user->id) {
            return true;
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdvert()
    {
        return $this->hasOne(Advert::class, ['advert_id' => 'advert_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLandlord()
    {
        return $this->hasOne(User::class, ['id' => 'landlord_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeal()
    {
        return $this->hasOne(ReservationDeal::class, ['reservation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFailure()
    {
        return $this->hasOne(ReservationFailure::class, ['reservation_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFailures()
    {
        return $this->hasMany(ReservationFailure::class, ['reservation_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AdvertReservationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AdvertReservationQuery(get_called_class());
    }

    /**
     * Возвращает количество непросмотренных заявок landlord'а или renter'а
     * @param string $byWhom
     * @return int|string
     */
    public static function nonViewedCount($byWhom = 'renter')
    {
        $lastViewedDate = UserSeen::getLastDate(self::tableName(), $byWhom);
        $query = self::find()->where(['>', 'date_update', $lastViewedDate]);

        if ($byWhom == 'landlord') {
            $query->andWhere(['landlord_id' => Yii::$app->user->id]);
        } else {
            $query->andWhere(['user_id' => Yii::$app->user->id]);
        }

        return $query->count();
    }

    /**
     * Проверят можно ли оставлять заявку "Незаезд" прямо сейчас отталкиваясь от даты заезда
     * @param string $dateArrived "Дата заезда" заявки "Бронь к объявлению"
     * @return boolean
     */
    public static function checkDateToFail($dateArrived)
    {
        $timeArrived = strtotime($dateArrived);
        // Начало возможности отправить заявку "Не заезд"
        $timeStart = $timeArrived + 1000;// + Yii::$app->getModule('partners')->timeIntervalForReservationFailStart;

        // Конец
        $timeEnd = $timeArrived + Yii::$app->getModule('partners')->timeIntervalForReservationFailEnd;

        $now = time();
        if ($now > $timeEnd) {
            return false;
        }

        if ($now < $timeStart) {
            return false;
        }

        return true;
    }

    /**
     * Проверяет не создавал ли этот пользователь "Незаезд" к резервации номер $reservation_id
     * и возвращает boolean
     * @param $reservation_id
     * @return boolean
     */
    public static function checkAlreadyFailed($reservation_id)
    {
        return ReservationFailure::find()->where([
            'reservation_id' => $reservation_id,
            'user_id' => Yii::$app->user->id
        ])->exists();
    }
}
