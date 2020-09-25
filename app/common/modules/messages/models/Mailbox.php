<?php

namespace common\modules\messages\models;

use yii\db\ActiveRecord;

/** *
 * @property integer $id
 * @property integer $message_id
 * @property integer $user_id
 * @property integer $interlocutor_id
 * @property integer $read
 * @property integer $inbox
 * @property integer $bin
 * @property integer $deleted
 */
class Mailbox extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_messages_mailbox}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id', 'user_id', 'interlocutor_id', 'read', 'inbox', 'bin', 'deleted'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID сообщения',
            'message_id' => 'ID base сообщения',
            'user_id' => 'ID пользователя',
            'interlocutor_id' => 'Собеседник',
            'read' => 'Прочитано',
            'inbox' => 'Входящее',
            'bin' => 'В корзине',
            'deleted' => 'Удалено',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterlocutor()
    {
        return $this->hasOne(\common\modules\users\models\User::class, ['id' => 'interlocutor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::class, ['id' => 'message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\modules\users\models\User::class, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return MailboxQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MailboxQuery(get_called_class());
    }

    /**
     * Разговоры этого пользователя с незабаненными пользователями
     * @return Mailbox[]
     */
    public static function findAllThisUserConversations()
    {
        $ids = self::find()->select('max(id)')
            ->deleted(0)
            ->thisUser()
            ->groupBy('interlocutor_id')
            ->asArray()
            ->column();

        return self::findAll($ids);
    }

    /**
     * Ставит статус прочтено этому сообщению
     * Примечание: в случае если есть измененные атрибуты, сохранение не происходит
     */
    public function readMessage()
    {
        if (!$this->dirtyAttributes) {
            if ($this->read == 1) {
                return true;
            }
            $this->read = 1;
            if ($this->save(false)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ставит статус удалено этому сообщению
     * Примечание: в случае если есть измененные атрибуты, сохранение не происходит
     */
    public function deleteMessage()
    {
        if (!$this->dirtyAttributes) {
            $this->read = 1;
            $this->deleted = 1;
            if ($this->save(false)) {
                return true;
            }
        }

        return false;
    }
}
