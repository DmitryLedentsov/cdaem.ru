<?php

namespace frontend\modules\partners\models;

use common\modules\partners\models\Calendar;
use yii\base\Model;
use Yii;

/**
 * class CalendarBlockerForm
 * Форма для создания блокировки календаря на время. Используеться, исключительно, при подтверждении резерваций
 *
 * Создается запись в календаре о том что на данный промежуток времени апартамент занят,
 * также осуществляеться разбитие или редактирование записей "Сейчас свободно" если временные промежутки пересекаются.
 * Еще выключается "Сейчас свободно" если сейчас находится в красном промежутке.
 * Еще удаляются все "сейчас занято" у которых промежутки времени пересекаются с этим промежутком.
 */
class CalendarBlockerForm extends Model
{
    public $reserved = 1;
    public $apartment_id;
    public $date_from;
    public $date_to;

    /**
     * @var null or array of objects of Calendar
     */
    protected $freeCalendars;

    public function rules()
    {
        return [
            ['reserved', 'compare', 'compareValue' => 1],
            ['apartment_id', 'exist', 'targetClass' => '\common\modules\partners\models\Apartment', 'targetAttribute' => 'apartment_id'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            ['date_from', 'compare', 'operator' => '<', 'compareAttribute' => 'date_to']
        ];
    }

    public function process($validate = true)
    {
        if ($validate && !$this->validate()) return false;

        if (!$this->deleteOtherBlockers()) return false;

        if (!$this->setApartmentAvailable()) return false;

        if ($this->checkIntersections()) {
            foreach ($this->freeCalendars as $freeCalendar) {
                if(!$this->divideIntersection($freeCalendar)) return false;
            }
        }

        $calendar = new Calendar();
        $calendar->reserved = $this->reserved;
        $calendar->apartment_id = $this->apartment_id;
        $calendar->date_from = $this->date_from;
        $calendar->date_to = $this->date_to;

        return $calendar->save(false);
    }

    /**
     * Находим все блокеры которые пересекаются с этим, и удаляем их
     * @return bool
     */
    protected function deleteOtherBlockers()
    {
        // Хотя при создании заявки на бронирование апартаментов, делается проверка - нету ли записей о том, что во временной
        // промежуток эти апартаменты заняты, всё же юзер может создать блокеры уже после создания такой заявки.
        // По этому для чистоты такого приложения нужно удалить пересекающиеся записи
        $blockers = Calendar::find()->where([
            'apartment_id' => $this->apartment_id,
            'reserved' => Calendar::RESERVED,
        ])->andWhere(['OR',
            ':date_from BETWEEN date_from AND date_to',
            ':date_to BETWEEN date_from AND date_to',
            'date_from BETWEEN :date_from AND :date_to',
            'date_to BETWEEN :date_from AND :date_to'
        ])->params([
            ':date_from' => $this->date_from,
            ':date_to' => $this->date_to
        ])->all();

        if (!$blockers) return true;

        foreach ($blockers as $blocker) {
            if (!$blocker->delete()) return false;
        }

        return true;
    }

    /**
     * Проверяет присутствие записи "сейчас свободно" пересекающейся с этим промежутком времени
     * @return bool
     */
    protected function checkIntersections()
    {
        $calendars = Calendar::find()->where([
            'apartment_id' => $this->apartment_id,
            'reserved' => Calendar::FREE
        ])->andWhere(['OR',
            ':date_from BETWEEN date_from AND date_to',
            ':date_to BETWEEN date_from AND date_to',
            'date_from BETWEEN :date_from AND :date_to',
            'date_to BETWEEN :date_from AND :date_to'
        ])->params([
            ':date_from' => $this->date_from,
            ':date_to' => $this->date_to
        ])->all();

        if ($calendars) {
            $this->freeCalendars = $calendars;
            return true;
        }

        return false;
    }

    protected function setApartmentAvailable()
    {
        $now = date('Y-m-d H:i:s');
        if ($this->date_from <= $now AND $now <= $this->date_to) { // Если сейчас находиться во временном промежутке этого блокера
            $apartment = Apartment::findOne($this->apartment_id);
            if (!$apartment) return false;
            if ($apartment->now_available == 0) return true;
            $apartment->now_available = 0;
            return $apartment->save(false);
        }

        return true;
    }

    /**
     * Разбивает, укорачивает или удаляет запись "Сейчас свободно"
     * так чтобы "занято" не соприкасалось со "свободно"
     * @return bool
     */
    protected function divideIntersection($freeCalendar)
    {
        $freeStart = new \DateTime($freeCalendar->date_from);
        $freeEnd = new \DateTime($freeCalendar->date_to);
        $unfreeStart = new \DateTime($this->date_from);
        $unfreeEnd = new \DateTime($this->date_to);

        // За основу берем "Сейчас свобдно"
        $startDiff = $freeStart->diff($unfreeStart);
        $endDiff = $freeEnd->diff($unfreeEnd);

        if (!$startDiff->invert AND !$endDiff->invert) { // только конец зеленого попадает в промежуток красного

            $freeCalendar->date_to = $this->date_from;
            return $freeCalendar->save(false);

        } else if (!$startDiff->invert AND $endDiff->invert) { // промежуток красного находиться целиком в зеленом

            $calendar = new Calendar();
            $calendar->date_from = $this->date_to;
            $calendar->date_to = $freeCalendar->date_to;
            $calendar->reserved = 0;
            $calendar->apartment_id = $this->apartment_id;
            $calendar->process = 0;

            $freeCalendar->date_to = $this->date_from;

            if ($calendar->save(false) && $freeCalendar->save(false)) return true;
            return false;

        } else if ($startDiff->invert AND !$endDiff->invert) { // промежуток зеленого находится целиком в красном

            return $freeCalendar->delete();

        } else if ($startDiff->invert AND $startDiff->invert) { // только начало зеленого попадает в промежуток красного

            $freeCalendar->date_from = $this->date_to;
            if ($freeCalendar->process == 1) {
                $freeCalendar->process = 0;
            }
            return $freeCalendar->save(false);

        } else { // А тут дурня какая-то. Врядле получится сюда попасть.
            return false;
        }
    }

}