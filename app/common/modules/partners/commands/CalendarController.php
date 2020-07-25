<?php

namespace common\modules\partners\commands;

use common\modules\partners\models as models;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\log\Logger;
use Yii;

/**
 * Class CalendarController
 * @package common\modules\partners\commands
 */
class CalendarController extends \yii\console\Controller
{
    /**
     * Calendar run
     *
     * Осуществляет включение и отключение статуса "Сейчас свободно"
     *
     * Вызов команды: php yii partners/calender
     *
     * TODO: НЕОБХОДИМО НАСТРОИТЬ КРОН ДЛЯ ВЫПОЛНЕНИЯ ДАННОГО СЦЕНАРИЯ
     */
    public function actionIndex()
    {
        $date = date('Y-m-d H:i:s');

        // Первый прогон таблицы - зеленые которые нужно закрыть
        $batchRecords = models\Calendar::find()
            ->where(['reserved' => 0, 'process' => 1])
            ->andWhere('date_to <= :date', [':date' => $date])  // date_to уже прошел
            ->batch();
        $countSuccess = 0;
        $countError = 0;
        foreach ($batchRecords as $records) {
            foreach ($records as $record) {
                // Выключаем "Сейчас свободно" апартаменту
                $apartment = models\Apartment::findOne($record->apartment_id);
                // если апартамента не нашли
                if (!$apartment) {
                    Yii::error('Ошибка: [Анапртаментов с ID' . $record->apartment_id . ' нет в базе данных]', 'partners-calendar');
                    continue;
                }

                $apartment->now_available = 0;
                $record->process = 2; // закончено, запись больше не нужна
                if ($record->save(false) && $apartment->save(false)) {
                    $countSuccess++;
                } else {
                    $countError++;
                    Yii::error('Ошибка: [Сохранение ID' . $record->apartment_id . ' не прошло]', 'partners-calendar');
                }
            }
        }


        // Второй прогон - зеленые которые нужно открыть
        $batchRecords = models\Calendar::find()
            ->where(['reserved' => 0, 'process' => 0])
            ->andWhere('date_from <= :date AND date_to >= :date', [':date' => $date]) //если сейчас(date()) находится во временном промежутке (date_from ... date_to) неоткрытого зеленого
            ->batch();

        foreach ($batchRecords as $records) {
            foreach ($records as $record) {
                // Включаем "Сейчас свободно" апартаменту
                $apartment = models\Apartment::findOne($record->apartment_id);
                // если апартамента не нашли
                if (!$apartment) {
                    Yii::error('Ошибка: [Анапртаментов с ID' . $record->apartment_id . ' нет в базе данных]', 'partners-calendar');
                    continue;
                }

                $apartment->now_available = 1;
                $record->process = 1;
                if ($record->save(false) && $apartment->save(false)) {
                    $countSuccess++;
                } else {
                    $countError++;
                    Yii::error('Ошибка: [Сохранение ID' . $record->apartment_id . ' не прошло]', 'partners-calendar');
                }
            }
        }

        // Третий прогон - удаление записей которые уже прошли в прошлом месяце
        $dateTime = new \DateTime('first day of today');
        $dateTime->modify('-1 sec');

        models\Calendar::deleteAll('date_to <= :endDateOfLastMonth AND (process = 2 OR process = 0)', [
            ':endDateOfLastMonth' => $dateTime->format('Y-m-d H:i:s')
        ]);

        $this->stdout('Процес выполнен. Обработано записей: ' . ($countSuccess + $countError) . ', возникло ошибок: ' . $countError . PHP_EOL, Console::FG_GREEN);
        Yii::info('Процес выполнен. Обработано записей: ' . ($countSuccess + $countError) . ', возникло ошибок: ' . $countError, 'partners-calendar');
    }


    /***
     * Процесс 0, 1, 2
     * 0 - не запускали
     * 1 - запустили и сейчас евейлбл
     * 2 - выключили и поставили 0 евейлбл
     *
     * Первый прогон закрывает открывшиеся
     * where(['date_to' <= date()] AND process = 1
     *
     * Второй прогон включает не включеные where(['date_from' => date() AND 'date_to' <= date()]) AND process = 0
     *
     * Красные служат только для того чтобы нельзя было бронировать в этот промежуток времени
     * Красные если врезаются в зеленые разбивают их на 2 или укарачивают. Если укорачивание происходит на процессе = 1
     *  то нужно проверить: если укарачивается слева и отрезает кусок аж до самого начала, то второму оставшемуся куску
     *  процесс = 0, и евейлбл = 0 апартаменту
     *
     * При врезке левый кусок остается каким и был, только дейт_ту меняются
     * При врезке, правый кусок зеленого всегда новый процесс = 0
     * Чистильщик удалеяет записи where('date_to' <= date() AND process = 2 OR 0) пердыдущего месяца(подумаем)
     */


}
