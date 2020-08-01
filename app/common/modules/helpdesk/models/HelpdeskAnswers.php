<?php

namespace common\modules\helpdesk\models;

use common\modules\helpdesk\traits\ModuleTrait;
use common\modules\users\models\User;
use Yii;

/**
 * Helpdesk Answers
 * @package common\modules\helpdesk\models
 */
class HelpdeskAnswers extends \yii\db\ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%helpdesk_answers}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'answer_id' => '№',
            'ticket_id' => '№',
            'user_id' => 'User ID',
            'text' => 'Сообщение',
            'date' => 'Дата',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHelpdesk()
    {
        return $this->hasOne(Helpdesk::class, ['ticket_id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
