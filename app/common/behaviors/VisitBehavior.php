<?php

namespace common\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\base\Behavior;
use yii\helpers\Json;
use common\library\DateFormat;
use common\modules\users\models\Visits;
use common\modules\users\models\Suspicions;


/**
 * VisitBehavior при успешной авторизации записываем историю посещений
 *               при ошибке авторизации записываем историю подозрительных действий
 *
 *
 * public function behaviors()
 * {
 *     return [
 *         'VisitBehavior' => [
 *             'class' => \common\behaviors\VisitBehavior::className(),
 *         ],
 *     ];
 * }
 * ```
 */
class VisitBehavior extends Behavior
{
    /**
     * Приложение в котором осуществили авторизацию
     * @var string
     */
    public $application = 'site';


    /**
     * Назначаем обработчик для [[owner]] событий
     * @return array события (array keys) с назначеными им обработчиками (array values)
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'history',
        ];
    }


    /**
     * Создать новую запись
     */
    public function history($event)
    {
        // если валидация не прошла и есть ошибки при авторизации
        if ($event->sender->hasErrors()) {
            // записываем их в историю подозрительных действий 
            return Suspicions::createHistory($this->application, 'login', 0, null, Json::encode($event->sender->getErrors()));
        } // если ошибок при валидации не возникло, записываем историю посещений
        else {
            return Visits::createHistory($this->application, $event->sender->user->id);
        }
    }
}