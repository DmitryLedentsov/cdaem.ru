<?php

namespace frontend\modules\partners\models;

use Yii;
use yii\base\Model;
use common\modules\partners\models\Calendar;

/**
 * class CalendarBlockerForm
 * ����� ��� �������� ���������� ��������� �� �����. �������������, �������������, ��� ������������� ����������
 *
 * ��������� ������ � ��������� � ��� ��� �� ������ ���������� ������� ���������� �����,
 * ����� ��������������� �������� ��� �������������� ������� "������ ��������" ���� ��������� ���������� ������������.
 * ��� ����������� "������ ��������" ���� ������ ��������� � ������� ����������.
 * ��� ��������� ��� "������ ������" � ������� ���������� ������� ������������ � ���� �����������.
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
        if ($validate && !$this->validate()) {
            return false;
        }

        if (!$this->deleteOtherBlockers()) {
            return false;
        }

        if (!$this->setApartmentAvailable()) {
            return false;
        }

        if ($this->checkIntersections()) {
            foreach ($this->freeCalendars as $freeCalendar) {
                if (!$this->divideIntersection($freeCalendar)) {
                    return false;
                }
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
     * ������� ��� ������� ������� ������������ � ����, � ������� ��
     * @return bool
     */
    protected function deleteOtherBlockers()
    {
        // ���� ��� �������� ������ �� ������������ ������������, �������� �������� - ���� �� ������� � ���, ��� �� ���������
        // ���������� ��� ����������� ������, �� �� ���� ����� ������� ������� ��� ����� �������� ����� ������.
        // �� ����� ��� ������� ������ ���������� ����� ������� �������������� ������
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

        if (!$blockers) {
            return true;
        }

        foreach ($blockers as $blocker) {
            if (!$blocker->delete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * ��������� ����������� ������ "������ ��������" �������������� � ���� ����������� �������
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
        if ($this->date_from <= $now and $now <= $this->date_to) { // ���� ������ ���������� �� ��������� ���������� ����� �������
            $apartment = Apartment::findOne($this->apartment_id);
            if (!$apartment) {
                return false;
            }
            if ($apartment->now_available == 0) {
                return true;
            }
            $apartment->now_available = 0;

            return $apartment->save(false);
        }

        return true;
    }

    /**
     * ���������, ����������� ��� ������� ������ "������ ��������"
     * ��� ����� "������" �� ������������� �� "��������"
     * @return bool
     */
    protected function divideIntersection($freeCalendar)
    {
        $freeStart = new \DateTime($freeCalendar->date_from);
        $freeEnd = new \DateTime($freeCalendar->date_to);
        $unfreeStart = new \DateTime($this->date_from);
        $unfreeEnd = new \DateTime($this->date_to);

        // �� ������ ����� "������ �������"
        $startDiff = $freeStart->diff($unfreeStart);
        $endDiff = $freeEnd->diff($unfreeEnd);

        if (!$startDiff->invert and !$endDiff->invert) { // ������ ����� �������� �������� � ���������� ��������

            $freeCalendar->date_to = $this->date_from;

            return $freeCalendar->save(false);
        } elseif (!$startDiff->invert and $endDiff->invert) { // ���������� �������� ���������� ������� � �������

            $calendar = new Calendar();
            $calendar->date_from = $this->date_to;
            $calendar->date_to = $freeCalendar->date_to;
            $calendar->reserved = 0;
            $calendar->apartment_id = $this->apartment_id;
            $calendar->process = 0;

            $freeCalendar->date_to = $this->date_from;

            if ($calendar->save(false) && $freeCalendar->save(false)) {
                return true;
            }

            return false;
        } elseif ($startDiff->invert and !$endDiff->invert) { // ���������� �������� ��������� ������� � �������

            return $freeCalendar->delete();
        } elseif ($startDiff->invert and $startDiff->invert) { // ������ ������ �������� �������� � ���������� ��������

            $freeCalendar->date_from = $this->date_to;
            if ($freeCalendar->process == 1) {
                $freeCalendar->process = 0;
            }

            return $freeCalendar->save(false);
        } else { // � ��� ����� �����-��. ������ ��������� ���� �������.
            return false;
        }
    }
}
