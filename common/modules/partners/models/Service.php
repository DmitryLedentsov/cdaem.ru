<?php

namespace common\modules\partners\models;

use common\modules\partners\models\scopes\ServiceQuery;
use common\modules\partners\traits\ModuleTrait;
use common\modules\users\models\User;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use Yii;

/**
 * Управление сервисами
 * @package common\modules\partners\models
 */
class Service extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%partners_services}}';
    }

    /**
     * @return ServiceQuery
     */
    public static function find()
    {
        return new ServiceQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'service' => 'Сервис',
            'payment_id' => 'Оплата',
            'user_id' => 'Пользователь',
            'user' => 'Пользователь',
            'advert_id' => 'Объявление',
            'apartment_id' => 'Апартамент',
            'reservation_id' => 'Резервация',
            'data_payment' => 'Дата оплаты',
            'date_start' => 'Дата включения',
            'date_expire' => 'Дата выключения',
            'data' => 'Дополнительные данные',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::className(), ['id' => 'user_id']);
    }

    /**
     * Статусы процесса
     *    Процесс активирован и в стадии выполнения
     *    Процесс выполнен
     *    Процесс в ожидании
     */
    const PROCESS_IMPLEMENTATION = -1;
    const PROCESS_FINISHED = 1;
    const PROCESS_PENDING = 0;

    /**
     * Все доступные сервисы
     *    Добавить объявление в рекламный слайдер
     *    Рекламировать объявление в разделе
     *    Отправить контакты владельца пользователю
     *    Открыть контакты объявления
     *    Поднять позицию объявления
     *    Выделить объявление
     *    Поднять объявление в топ
     *    Открыть контакты пользователя, который забронировал апаратаменты
     *    Открыть контакты пользователя, который оставил общую заявку на резервацию
     */
    const SERVICE_ADVERTISING_TOP_SLIDER = 'ADVERTISING_TOP_SLIDER';
    const SERVICE_ADVERTISING_IN_SECTION = 'ADVERTISING_IN_SECTION';
    const SERVICE_CONTACTS_OPEN_TO_USER = 'CONTACTS_OPEN_TO_USER';
    const SERVICE_APARTMENT_CONTACTS_OPEN = 'APARTMENT_CONTACTS_OPEN';
    const SERVICE_ADVERT_TOP_POSITION = 'ADVERT_TOP_POSITION';
    const SERVICE_ADVERT_SELECTED = 'ADVERT_SELECTED';
    const SERVICE_ADVERT_IN_TOP = 'ADVERT_IN_TOP';
    const SERVICE_CONTACTS_OPEN_FOR_TOTAL_BID = 'CONTACTS_OPEN_FOR_TOTAL_BID';
    const SERVICE_CONTACTS_OPEN_FOR_RESERVATION = 'CONTACTS_OPEN_FOR_RESERVATION';

    /**
     * Поиск необработанного процесса активации сервиса по $id
     * @param $id
     * @param int $process
     * @return array|null|ActiveRecord
     */
    public static function findProcessById($id, $process = 0)
    {
        $query = self::find();
        $query->andWhere('id = :id', [':id' => $id]);
        if ($process !== false) {
            $query->process($process);
        }
        return $query->one();
    }

    /**
     * Поиск всех необработанных процессов активации сервисов
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findProcessesInQueue()
    {
        return self::find()
            ->andWhere('process != 1')
            ->andWhere('payment_id IS NOT NULL')
            ->orderBy(['date_start' => SORT_ASC])
            ->all();
    }
    
    public static function findProcessesInQueueApartament($id)
    {
        return self::find()
            ->andWhere('process = 0')
            ->andWhere('payment_id IS NOT NULL')
                ->andWhere('date_create >= (now() - INTERVAL 1200 SECOND) ')
            //->andWhere('service =  oki')  
            ->andWhere('user_id = :id', [':id' => $id])
            ->all();
    }

    /**
     * Получить кол-во записей очередей сервисов
     * пользователя за определенный интервал времени
     * @param $userId
     * @return int|string
     */
    public static function getCountRecordsForInterval($userId)
    {
        return self::find()
            ->andWhere('date_create >= (now() - INTERVAL 10 SECOND) ')
            ->andWhere('user_id = :user_id', [':user_id' => $userId])
            ->andWhere('payment_id IS NOT NULL')
            ->count();
    }

    /**
     * Расчет стоимости покупки сервисов
     * @param $service
     * @param array $data
     * @return array
     */
    public function calculation($service, array $data)
    {
        $selected = $data['selected'];
        $days = (int)$data['days']; // кол-во дней работает сервис

        // Кол-во выбранных объектов
        $countSelected = count($selected);

        if ($days > 28) {
            $days = 28;
        }
        if ($days < 1) {
            $days = 1;
        }

        $days = $service->isTimeInterval() ? $days : 1;

        // Цена
        $price = 0;

        // Получаем общую цену
        for ($i = 0; $i < $countSelected; ++$i) {
            $price += $service->getPrice();
        }

        $price *= $days;


        // Высчитываем скидку
        $discount = $service->calculateDiscount($countSelected, [
            'days' => $days,
            'price' => $price,
        ]);


        // Получаем конечную цену (вычет скидки)
        $price = $price - $discount;

        return [
            'countSelected' => $countSelected,
            'days' => $days,
            'discount' => $discount,
            'price' => $price,
        ];
    }

    /**
     * Добавить процесс в очередь на активацию
     * @param $service
     * @param null $date_start
     * @param null $days
     * @param array $data
     * @param null $paymentId
     * @param null $user_id
     * @return bool|mixed
     */
    public static function addProcess($service, $date_start = null, $days = null, array $data = [], $paymentId = null, $user_id = null)
    {
        if (!$user_id) {
            $user_id = Yii::$app->user->id;
        }

        // Дата активации сервиса
        if ($date_start) {
            $date = new \DateTime($date_start);
            $date_start = $date->format('Y-m-d H:i:s');
        }

        // Дата окончания сервиса
        $date_expire = null;
        if ($days) {
            $date = new \DateTime($date_start);
            $date->add(\DateInterval::createFromDateString('+' . $days . ' days'));
            $date_expire = $date->format('Y-m-d H:i:s');
        }

        $model = new Service();
        $model->service = $service;
        $model->payment_id = $paymentId;
        $model->user_id = $user_id;

        $model->date_create = date('Y-m-d H:i:s');
        $model->date_start = $date_start;
        $model->date_expire = $date_expire;
        $model->data = Json::encode($data);
        $model->process = 0;

        if ($model->save(false)) {
            return $model->id;
        }

        return false;
    }

    /**
     *  Массив доступных данных статуса
     * @return array
     */
    public static function getProcessArray()
    {
        return [
            self::PROCESS_FINISHED => [
                'label' => 'Выполнен',
                'style' => 'color: green',
            ],
            self::PROCESS_IMPLEMENTATION => [
                'label' => 'Активный',
                'style' => 'color: green',
            ],
            self::PROCESS_PENDING => [
                'label' => 'В ожидании',
                'style' => 'color: blue',
            ],
        ];
    }
}