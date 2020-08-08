<?php

namespace frontend\modules\partners\models\form;

use Yii;
use yii\base\Model;
use yii\base\Exception;
use yii\validators\ExistValidator;
use frontend\modules\partners\models\Calendar;
use frontend\modules\partners\models\Apartment;

/**
 * Форма для сохранения календаря "Быстрое заселение"
 * @package frontend\modules\partners\models\form
 */
class CalenderForm extends Calendar
{
    /**
     * Присутствие ошибок которые появились после валидации
     * @var bool
     */
    public $hasManualErrors = false;

    /**
     * Ошибки которые появляються после валидации, с именами инпутов
     * @var bool | array
     */
    public $manualErrors = false;

    /**
     * Дата в которой происходит изменение
     * @var sring
     */
    public $whichDate;

    /**
     * ID обрабатуемого апартамента
     * @var int
     */
    public $calendarApartmentId;

    /**
     * Тип
     * @var string
     */
    public $type;

    /**
     * Дата старта
     * @var string
     */
    public $date_start;

    /**
     * Время старта
     * @var string
     */
    public $time_start;

    /**
     * Дата завершения
     * @var string
     */
    public $date_end;

    /**
     * Время завершения
     * @var string
     */
    public $time_end;

    /**
     * Данные аренды
     * @var array
     */
    public $_rentData = [];

    /**
     * Апартаменты
     * @var array
     */
    public $_apartments = [];

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'user-create' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['type', 'date_start', 'time_start', 'date_end', 'time_end', 'whichDate', 'calendarApartmentId']
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (!is_array($this->type)) {
                $calendarApartmentId = $this->calendarApartmentId;

                $this->type = [$calendarApartmentId => $this->type];
                $this->whichDate = [$calendarApartmentId => $this->whichDate];
                $this->calendarApartmentId = [$calendarApartmentId => $this->calendarApartmentId];
                $this->date_start = [$calendarApartmentId => $this->date_start];
                $this->time_start = [$calendarApartmentId => $this->time_start];
                $this->date_end = [$calendarApartmentId => $this->date_end];
                $this->time_end = [$calendarApartmentId => $this->time_end];
            }

            foreach ($this->type as $apartmentId => $type) {
                if (!in_array($type, array_keys(self::getStatusArray()))) {
                    continue;
                }

                if ($this->_apartments[$apartmentId] = Apartment::findApartmentByUser($apartmentId, Yii::$app->user->id)) {
                    $dateError = false;

                    if ($type != '-1') {
                        $validator = new \yii\validators\DateValidator();
                        $validator->format = 'php:d.m.Y H:i';

                        $dateStart = $this->getDateStart($this->_apartments[$apartmentId]->apartment_id);
                        $dateEnd = $this->getDateEnd($this->_apartments[$apartmentId]->apartment_id);


                        if (!$validator->validate($dateStart)) {
                            $this->addError('date_start[' . $this->_apartments[$apartmentId]->apartment_id . ']', '');
                            $this->addError('time_start[' . $this->_apartments[$apartmentId]->apartment_id . ']', 'Укажите корректную дату и время');
                            $dateError = true;
                        }

                        if (!$validator->validate($dateEnd)) {
                            $this->addError('date_end[' . $this->_apartments[$apartmentId]->apartment_id . ']', '');
                            $this->addError('time_end[' . $this->_apartments[$apartmentId]->apartment_id . ']', 'Укажите корректную дату и время');
                            $dateError = true;
                        }

                        $timeStart = strtotime($dateStart);
                        $timeEnd = strtotime($dateEnd);

                        if (!$dateError) {
                            if ($timeStart >= $timeEnd) {
                                $this->addError('date_start[' . $this->_apartments[$apartmentId]->apartment_id . ']', '');
                                $this->addError('time_start[' . $this->_apartments[$apartmentId]->apartment_id . ']', 'Дата старта должна быть меньше даты завершения');
                            } else {
                                /*
                                 Эта валидация перенесена ниже
                                if (time() > $timeStart) {
                                    $this->addError('date_start[' . $this->_apartments[$apartmentId]->apartment_id . ']', '');
                                    $this->addError('time_start[' . $this->_apartments[$apartmentId]->apartment_id . ']', 'Дата старта должна быть больше текущей даты');
                                }
                                 */
                            }
                        }
                    }

                    if (!$dateError) {
                        $this->_rentData[$this->_apartments[$apartmentId]->apartment_id] = [
                            'whichDate' => $this->whichDate[$apartmentId],
                            'calendarApartmentId' => $this->calendarApartmentId[$apartmentId],
                            'type' => $type,
                            'date_start' => date("Y-m-d H:i:s", $timeStart),
                            'date_end' => date("Y-m-d H:i:s", $timeEnd),
                        ];
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Процесс выполнения
     */
    public function process()
    {
        foreach ($this->_rentData as $apartmentId => $data) {
            if ($data['type'] == -1) {
                $this->ignore($data);
            } elseif ($data['type'] == 1) {
                $this->unavailable($data);
            } else {
                $this->available($data);
            }

            // Если какой-нибудь из методов записал ошибку
            if ($this->hasManualErrors) {
                foreach ($this->getErrors() as $attribute => $errors) {
                    $this->manualErrors[\yii\helpers\Html::getInputId($this, $attribute)] = $errors;
                }
                break;
            }

            $this->reprocess($data['calendarApartmentId']);

            continue;

            //ОШибки
            /*
            $this->hasManualErrors = true;
            $this->addError('date_start[' . $this->_apartments[$apartmentId]->apartment_id . ']', '');
            $this->addError('time_start[' . $this->_apartments[$apartmentId]->apartment_id . ']', 'Укажите корректную дату и время');


            foreach ($this->getErrors() as $attribute => $errors) {
                $this->manualErrors[\yii\helpers\Html::getInputId($this, $attribute)] = $errors;
            }
            */


            $currentRecord = Calendar::find()
                ->joinWith([
                    'apartment' => function ($query) use ($apartmentId) {
                        $query->andWhere(['user_id' => Yii::$app->user->id])
                            ->andWhere([Apartment::tableName() . '.apartment_id' => $apartmentId]);
                    }
                ])
                ->andWhere(['OR',
                    ':date_from BETWEEN date_from AND date_to',
                    ':date_to BETWEEN date_from AND date_to',
                    'date_from BETWEEN :date_from AND :date_to',
                    'date_to BETWEEN :date_from AND :date_to'
                ])
                ->addParams([':date_from' => $data['date_start'], ':date_to' => $data['date_end']])
                ->one();

            if ($currentRecord) {
                $currentRecord->reserved = $data['type'];
                $currentRecord->date_from = $data['date_start'];
                $currentRecord->date_to = $data['date_end'];
                $currentRecord->process = 0;
                $currentRecord->save(false);
            } else {
                $model = new Calendar();
                $model->apartment_id = $apartmentId;
                $model->reserved = $data['type'];
                $model->date_from = $data['date_start'];
                $model->date_to = $data['date_end'];
                $model->process = 0;
                $model->save(false);
            }

            // Если статус резервации меняется в текущее время
            // Устанавливаем соответствующий флаг апартаментам
            if (!$data['type'] && strtotime($data['date_start']) >= time()) {
                $this->_apartments[$apartmentId]->now_available = 1;
                $this->_apartments[$apartmentId]->save(false);
            } elseif ($data['type'] && strtotime($data['date_start']) >= time()) {
                $this->_apartments[$apartmentId]->now_available = 0;
                $this->_apartments[$apartmentId]->save(false);
            }
        }
    }

    /**
     * "Не учитывать"
     * Удаляет запись, временной промежуток которого задевает дату которую редактирует пользователь ($data['whichDate'])
     * Или ничего не делает, если такого промежутка нету.
     * @param $data
     */
    private function ignore($data)
    {
        $currentRecord = Calendar::find()
            ->joinWith('apartment')
            ->andWhere([Apartment::tableName() . '.user_id' => Yii::$app->user->id])
            ->andWhere([Apartment::tableName() . '.apartment_id' => $data['calendarApartmentId']])
            ->andWhere(':whichDate BETWEEN DATE(date_from) AND DATE(DATE_SUB(date_to, INTERVAL 1 SECOND))')
            ->addParams([':whichDate' => $data['whichDate']])
            ->one();

        if ($currentRecord) {
            $currentRecord->delete();
        }
    }

    /**
     * "Сейчас свободно"
     * Создает временной промежуток в календаре с пометкой "Сейчас свободно"
     * При редактировании (это когда была запись промежуток которого задевал дату ($data['whichDate']),
     * вместо создания делается редактирование.
     *
     * @param $data
     */
    private function available($data)
    {
        $currentRecord = Calendar::find()
            ->joinWith('apartment')
            ->andWhere([Apartment::tableName() . '.user_id' => Yii::$app->user->id])
            ->andWhere([Calendar::tableName() . '.apartment_id' => $data['calendarApartmentId']])
            ->andWhere(':whichDate BETWEEN DATE(date_from) AND DATE(DATE_SUB(date_to, INTERVAL 1 SECOND))')
            ->addParams([':whichDate' => $data['whichDate']])
            ->one();

        if ($currentRecord) {
            $intersection = $this->checkIntersection($data, $currentRecord->id);
        } else {
            $intersection = $this->checkIntersection($data);
        }

        if ($intersection) {
            $this->hasManualErrors = true;
            $this->addError('date_start[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', '');
            $this->addError('time_start[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', '');
            $this->addError('date_end[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', '');
            $this->addError('time_end[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', 'Промежуток времени пересекается с другими записями.');

            return;
        }

        if ($currentRecord) {
            // Если редактирования небыло
            if ($currentRecord->date_from == $data['date_start']
                && $currentRecord->date_to == $data['date_end']
                && $currentRecord->reserved == $data['type']) {
                return;
            }

            $currentRecord->date_from = $data['date_start'];
            $currentRecord->date_to = $data['date_end'];
            $currentRecord->reserved = Calendar::FREE;
            $currentRecord->process = 0;
            $currentRecord->save(false);
        } else {
            if ($data['date_start'] <= date('Y-m-d H:i:s')) {
                $this->hasManualErrors = true;
                $this->addError('date_start[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', '');
                $this->addError('time_start[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', 'Дата старта должна быть больше текущей даты');

                return;
            }
            $calendar = new Calendar();
            $calendar->apartment_id = $data['calendarApartmentId'];
            $calendar->date_from = $data['date_start'];
            $calendar->date_to = $data['date_end'];
            $calendar->reserved = Calendar::FREE;
            $calendar->process = 0;
            $calendar->save(false);
        }
    }

    /**
     * "Занято"
     * Создается или редактируется запись с пометкой "Занято"
     * Читать "Сейчас свободно".
     * @param $data
     */
    private function unavailable($data)
    {
        $currentRecord = Calendar::find()
            ->joinWith('apartment')
            ->andWhere([Apartment::tableName() . '.user_id' => Yii::$app->user->id])
            ->andWhere([Calendar::tableName() . '.apartment_id' => $data['calendarApartmentId']])
            ->andWhere(':whichDate BETWEEN DATE(date_from) AND DATE(DATE_SUB(date_to, INTERVAL 1 SECOND))')
            ->addParams([':whichDate' => $data['whichDate']])
            ->one();

        if ($currentRecord) {
            $intersection = $this->checkIntersection($data, $currentRecord->id);
        } else {
            $intersection = $this->checkIntersection($data);
        }

        if ($intersection) {
            $this->hasManualErrors = true;
            $this->addError('date_start[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', '');
            $this->addError('time_start[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', '');
            $this->addError('date_end[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', '');
            $this->addError('time_end[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', 'Промежуток времени пересекается с другими записями.');

            return;
        }

        if ($currentRecord) {
            // Если редактирования небыло
            if ($currentRecord->date_from == $data['date_start']
                && $currentRecord->date_to == $data['date_end']
                && $currentRecord->reserved == $data['type']) {
                return;
            }

            $currentRecord->date_from = $data['date_start'];
            $currentRecord->date_to = $data['date_end'];
            $currentRecord->reserved = Calendar::RESERVED;
            $currentRecord->process = 0;
            $currentRecord->save(false);
        } else {
            if ($data['date_start'] <= date('Y-m-d H:i:s')) {
                $this->hasManualErrors = true;
                $this->addError('date_start[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', '');
                $this->addError('time_start[' . $this->_apartments[$data['calendarApartmentId']]->apartment_id . ']', 'Дата старта должна быть больше текущей даты');

                return;
            }
            $calendar = new Calendar();
            $calendar->apartment_id = $data['calendarApartmentId'];
            $calendar->date_from = $data['date_start'];
            $calendar->date_to = $data['date_end'];
            $calendar->reserved = Calendar::RESERVED;
            $calendar->process = 0;
            $calendar->save(false);
        }
    }

    /**
     * Пересчитываем записи и в соответсвии с ними делаем статус "Свободно" или нет
     * для этого апартамента
     */
    private function reprocess($apartment_id)
    {
        //1. Закрываем незакрытые(в процессе) ушедшие промежутки "Сейчас свободно"
        $goneAvailable = Calendar::find()
            ->joinWith('apartment')
            ->where([
                Calendar::tableName() . '.apartment_id' => $apartment_id,
                Apartment::tableName() . '.user_id' => Yii::$app->user->id,
                'reserved' => Calendar::FREE,
                'process' => 1,
            ])
            ->andWhere('date_to <= :nowDate')
            ->addParams([':nowDate' => date('Y-m-d H:i:s')])
            ->one();

        if ($goneAvailable) {
            $goneAvailable->process = 2;
            $goneAvailable->save(false);
        }


        //2. Включаем неоткрытый "Сейчас свободно", которые должны открыться сейчас или через 5 минут
        $nowAvailable = Calendar::find()
            ->joinWith('apartment')
            ->where([
                Apartment::tableName() . '.apartment_id' => $apartment_id,
                Apartment::tableName() . '.user_id' => Yii::$app->user->id,
                'reserved' => Calendar::FREE,
            ])
            ->andWhere('date_from <= :nowDate AND date_to >= :nowDate')
            ->addParams([':nowDate' => date('Y-m-d H:i:s')])
            ->one();

        if (!$nowAvailable) {
            // Может через 5 минут включиться?
            $nowAvailable = Calendar::find()
                ->joinWith('apartment')
                ->where([
                    Apartment::tableName() . '.apartment_id' => $apartment_id,
                    Apartment::tableName() . '.user_id' => Yii::$app->user->id,
                    'reserved' => Calendar::FREE,
                ])
                ->andWhere('date_from <= :nowDatePlusFiveM AND date_to >= :nowDatePlusFiveM')
                ->addParams([':nowDatePlusFiveM' => date('Y-m-d H:i:s', time() + 300)])
                ->one();
        }

        $apartment = Apartment::findOne($apartment_id);

        if ($nowAvailable) {
            if ($nowAvailable->process != 1) {
                $nowAvailable->process = 1;
                $nowAvailable->save(false);
            }

            if ($apartment->now_available != 1) {
                $apartment->now_available = 1;
                $apartment->save(false);
            }
        } else {
            if ($apartment->now_available != 0) {
                $apartment->now_available = 0;
                $apartment->save(false);
            }
        }
    }

    /**
     * Проверяет не пересекаеться временной промежуток $data другие промежутки, не учитывая $calendar_id
     * одного апартамента
     * @param $data
     * @param null $calendar_id
     * @return bool
     */
    private function checkIntersection($data, $calendar_id = null)
    {
        $query = Calendar::find()
            ->joinWith('apartment')
            ->andWhere([Apartment::tableName() . '.user_id' => Yii::$app->user->id])
            ->andWhere([Apartment::tableName() . '.apartment_id' => $data['calendarApartmentId']])
            ->andWhere(['OR',
                //':date_from BETWEEN date_from AND date_to',
                'date_from < :date_from AND :date_from < date_to',
                //':date_to BETWEEN date_from AND date_to',
                'date_from < :date_to AND :date_to < date_to',
                //'date_from BETWEEN :date_from AND :date_to',
                ':date_from < date_from AND date_from < :date_to',
                //'date_to BETWEEN :date_from AND :date_to'
                ':date_from < date_to AND date_to < :date_to',
            ])
            ->addParams([':date_from' => $data['date_start'], ':date_to' => $data['date_end']]);
        if ($calendar_id !== null) {
            $query->andWhere(['!=', Calendar::tableName() . '.id', $calendar_id]);
        }

        return $query->exists();
    }

    /**
     * Возвращает проверенную дату
     * @param $date
     * @return bool|string
     */
    public function getDate($date)
    {
        $validator = new \yii\validators\DateValidator();
        $validator->format = 'php:Y-m-d';

        if (!$validator->validate($date)) {
            $date = date('Y-m-d');
        }

        return $date;
    }

    /**
     * Преобразовать данные для удобной обработки
     * @param Apartment $apartment
     * @param $date
     * @return array
     */
    public function getFormatData(Apartment $apartment, $date)
    {
        $data = [
            'type' => null,
            'date_from' => null,
            'time_from' => null,
            'date_to' => null,
            'time_to' => null,
        ];

        $record = Calendar::findApartmentRecordByDate($apartment->apartment_id, $date);
        if ($record) {
            $dateTimeFrom = explode(' ', $record->date_from);
            $dateTimeTo = explode(' ', $record->date_to);
            $data = [
                'type' => $record->reserved,
                'date_from' => isset($dateTimeFrom[0]) ? date('d.m.Y', strtotime($dateTimeFrom[0])) : null,
                'time_from' => isset($dateTimeFrom[1]) ? $dateTimeFrom[1] : null,
                'date_to' => isset($dateTimeTo[0]) ? date('d.m.Y', strtotime($dateTimeTo[0])) : null,
                'time_to' => isset($dateTimeTo[1]) ? $dateTimeTo[1] : null,
            ];
        }
        /*if ($apartment->calenderActualRecordByDate($date)) {
            $dateTimeFrom = explode(' ', $apartment->calenderActualRecord->date_from);
            $dateTimeTo = explode(' ', $apartment->calenderActualRecord->date_to);
            $data = [
                'type' => $apartment->calenderActualRecord->reserved,
                'date_from' => isset($dateTimeFrom[0]) ? date('d.m.Y', strtotime($dateTimeFrom[0])) : null,
                'time_from' => isset($dateTimeFrom[1]) ? $dateTimeFrom[1] : null,
                'date_to' => isset($dateTimeTo[0]) ? date('d.m.Y', strtotime($dateTimeTo[0])) : null,
                'time_to' => isset($dateTimeTo[1]) ? $dateTimeTo[1] : null,
            ];
        }*/
        return $data;
    }

    /**
     * Получить дату старта
     * @param $apartmentId
     * @return string
     */
    private function getDateStart($apartmentId)
    {
        $date_start = isset($this->date_start[$apartmentId]) ? $this->date_start[$apartmentId] : '';
        $time_start = isset($this->time_start[$apartmentId]) ? $this->time_start[$apartmentId] : '';

        $date_start = $date_start . ' ' . $time_start;

        return $date_start;
    }

    /**
     * Получить дату завершения
     * @param $apartmentId
     * @return string
     */
    private function getDateEnd($apartmentId)
    {
        $date_end = isset($this->date_end[$apartmentId]) ? $this->date_end[$apartmentId] : '';
        $time_end = isset($this->time_end[$apartmentId]) ? $this->time_end[$apartmentId] : '';

        $date_end = $date_end . ' ' . $time_end;

        return $date_end;
    }
}
