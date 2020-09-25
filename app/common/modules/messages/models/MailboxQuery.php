<?php

namespace common\modules\messages\models;

/**
 * This is the ActiveQuery class for [[Mailbox]].
 *
 * @see Mailbox
 */
class MailboxQuery extends \yii\db\ActiveQuery
{
    /**
     * Прочитанные
     * @param int $state
     * @return $this
     */
    public function read($state = 1)
    {
        $this->andWhere([Mailbox::tableName() . '.read' => $state]);

        return $this;
    }

    /**
     * Непрочитанные
     * @param int $state
     * @return $this
     */
    public function unread($state = 0)
    {
        $this->andWhere([Mailbox::tableName() . '.read' => $state]);

        return $this;
    }

    /**
     * Входящие
     * @param int $state
     * @return $this
     */
    public function inbox($state = 1)
    {
        $this->andWhere([Mailbox::tableName() . '.inbox' => $state]);

        return $this;
    }

    /**
     * Исходящие
     * @param int $state
     * @return $this
     */
    public function outbox($state = 0)
    {
        $this->andWhere([Mailbox::tableName() . '.inbox' => $state]);

        return $this;
    }

    /**
     * Удаленные
     * @param int $state
     * @return $this
     */
    public function deleted($state = 1)
    {
        $this->andWhere([Mailbox::tableName() . '.deleted' => $state]);

        return $this;
    }

    /**
     * Владелец сообщения
     * @return $this
     */
    public function thisUser()
    {
        $this->andWhere([Mailbox::tableName() . '.user_id' => \Yii::$app->user->id]);

        return $this;
    }
}
