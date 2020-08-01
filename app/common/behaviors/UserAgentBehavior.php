<?php

namespace common\behaviors;

use Yii;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\base\Behavior;

/**
 * UserAgentBehavior автоматически заполняет указанные атрибуты с текущим USER_AGENT пользователя
 *
 * public function behaviors()
 * {
 *     return [
 *         'UserAgentBehavior' => [
 *             'class' => UserAgentBehavior::class,
 *             'attributes' => [
 *                 ActiveRecord::EVENT_BEFORE_INSERT => 'user_agent',
 *                 ActiveRecord::EVENT_BEFORE_UPDATE => 'user_agent',
 *             ]
 *         ],
 *     ];
 * }
 */
class UserAgentBehavior extends Behavior
{
    public $attributes = [
        BaseActiveRecord::EVENT_BEFORE_INSERT => 'user_agent',
        BaseActiveRecord::EVENT_BEFORE_UPDATE => 'user_agent',
    ];

    /**
     * Назначаем обработчик для [[owner]] событий
     * @return array события (array keys) с назначеными им обработчиками (array values)
     */
    public function events()
    {
        $events = $this->attributes;
        foreach ($events as $i => $event) {
            $events[$i] = 'getCurrentUserAgent';
        }
        return $events;
    }

    /**
     * Добавляем USER_AGENT
     * @param Event $event Текущее событие
     */
    public function getCurrentUserAgent($event)
    {
        $attributes = isset($this->attributes[$event->name]) ? (array)$this->attributes[$event->name] : [];

        if (!empty($attributes)) {
            foreach ($attributes as $source => $attribute) {
                $this->owner->$attribute = Yii::$app->request->userAgent;
            }
        }
    }
}