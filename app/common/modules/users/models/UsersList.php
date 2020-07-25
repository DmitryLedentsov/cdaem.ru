<?php

namespace common\modules\users\models;

use Yii;

/**
 * Class UsersList
 * @package common\modules\users\models
 */
class UsersList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'interlocutor_id', 'type'], 'required'],
            [['user_id', 'interlocutor_id'], 'exist', 'targetClass' => '\common\modules\users\models\User',
                'targetAttribute' => 'id'],
            ['user_id', 'compare', 'compareAttribute' => 'interlocutor_id', 'operator' => '!='],
            [['user_id', 'interlocutor_id', 'type'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => Yii::t('users', 'Record ID'),
            'user_id' => Yii::t('users', 'User ID'),
            'interlocutor_id' => Yii::t('users', 'Interlocutor ID'),
            'type' => Yii::t('users', 'Тип (Заметка или Бан)'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new UsersListQuery(get_called_class());
    }

    /**
     * В избранном
     * В черном списке
     */
    const BOOKMARK = 1;
    const BLACKLIST = 0;

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInterlocutor()
    {
        return $this->hasOne(User::className(), ['id' => 'interlocutor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
