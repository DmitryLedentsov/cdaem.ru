<?php

namespace common\modules\messages\models;

use common\modules\messages\traits\ModuleTrait;
use yii\db\ActiveRecord;
use common\modules\users\models\UsersList;
use Yii;

/**
 * Class Message
 * @package common\modules\messages\models
 */
class Message extends ActiveRecord
{
    use ModuleTrait;

    /**
     * Идентификатор собеседника
     * @var int
     */
    public $interlocutor_id;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => false,
                'value' => function ($event) {
                    return date('Y-m-d H:i:s');
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_messages}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'theme' => 'Тема',
            'text' => 'Текст',
            'date_create' => 'Дата создания',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['theme', 'text', 'interlocutor_id']
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'create' => self::OP_ALL
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme'], 'string', 'max' => 255],
            ['text', 'string', 'min' => 10],
            ['text', 'required'],
            ['interlocutor_id', 'required'],
            ['interlocutor_id', 'exist', 'targetClass' => '\common\modules\users\models\User', 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();

        // Нельзя писать пользователю который заблокировал залогиненого пользователя
        $usersList = UsersList::find()->where([
            'user_id' => $this->interlocutor_id,
            'interlocutor_id' => Yii::$app->user->id,
            'type' => UsersList::BLACKLIST
        ])->exists();

        if ($usersList) $this->addError('text', 'Пользователь, которому вы пишите, поместил вас в чорный список');
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $mailbox = new Mailbox();
        $mailbox->message_id = $this->id;
        $mailbox->user_id = Yii::$app->user->id;
        $mailbox->interlocutor_id = $this->interlocutor_id;
        $mailbox->read = 1;
        $mailbox->inbox = 0;
        $mailbox->bin = 0;
        $mailbox->deleted = 0;
        $mailbox->save(false);

        if (Yii::$app->user->id != $this->interlocutor_id) {
            $mailbox = new Mailbox();
            $mailbox->message_id = $this->id;
            $mailbox->user_id = $this->interlocutor_id;
            $mailbox->interlocutor_id = Yii::$app->user->id;
            $mailbox->read = 0;
            $mailbox->inbox = 1;
            $mailbox->bin = 0;
            $mailbox->deleted = 0;
            $mailbox->save(false);
        }

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMessagesMailboxes()
    {
        return $this->hasMany(Mailbox::className(), ['message_id' => 'id']);
    }
}
