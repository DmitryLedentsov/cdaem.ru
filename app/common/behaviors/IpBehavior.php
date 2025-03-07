<?php

namespace common\behaviors;

use Yii;
use yii\base\Event;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * IpBehavior автоматически заполняет указанные атрибуты с текущим ip адресом пользователя
 *
 * public function behaviors()
 * {
 *     return [
 *         'IpBehavior' => [
 *             'class' => IpBehavior::class,
 *             'attributes' => [
 *                 ActiveRecord::EVENT_BEFORE_INSERT => 'ip',
 *                 ActiveRecord::EVENT_BEFORE_UPDATE => 'ip',
 *             ]
 *         ],
 *     ];
 * }
 */
class IpBehavior extends Behavior
{
    public $attributes = [
        BaseActiveRecord::EVENT_BEFORE_INSERT => 'ip',
        BaseActiveRecord::EVENT_BEFORE_UPDATE => 'ip',
    ];

    /**
     * Назначаем обработчик для [[owner]] событий
     * @return array события (array keys) с назначенными им обработчиками (array values)
     */
    public function events()
    {
        $events = $this->attributes;
        foreach ($events as $i => $event) {
            $events[$i] = 'getCurrentIp';
        }

        return $events;
    }

    /**
     * Добавляем IP адрес
     * @param Event $event Текущее событие
     */
    public function getCurrentIp($event)
    {
        $attributes = isset($this->attributes[$event->name]) ? (array)$this->attributes[$event->name] : [];

        if (!empty($attributes)) {
            foreach ($attributes as $source => $attribute) {
                $this->owner->$attribute = Yii::$app->request->userIP;
            }
        }
    }
}
