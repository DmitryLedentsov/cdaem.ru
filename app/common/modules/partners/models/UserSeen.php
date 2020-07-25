<?php

namespace common\modules\partners\models;

use Yii;

/**
 * Последние просмотренные изменения пользователей
 * This is the model class for table "{{%user_seen}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $table_name
 * @property string $type
 * @property string $last_update
 */
class UserSeen extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_seen}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '№',
            'user_id' => 'ID юзера',
            'table_name' => 'Имя таблицы',
            'type' => 'Тип просмотра',
            'last_update' => 'Дата последнего увиденного изменения',
        ];
    }

    /**
     * Обновляет дату последней просмотренной записи
     *
     * @param $type
     * @param $dateUpdate
     * @return bool
     */
    public static function updateLastDate($tableName, $dateUpdate, $type = null)
    {
        if (!$dateUpdate) return false;
        if (Yii::$app->user->isGuest) return false;
        $model = self::find()->where([
            'user_id' => Yii::$app->user->id,
            'table_name' => $tableName,
        ])->andFilterWhere(['type' => $type])->one();

        if ($model and $model->last_update == $dateUpdate) return true;

        if ($model and $model->last_update > $dateUpdate) return true;

        if (!$model) {
            $model = new self();
            $model->table_name = $tableName;
            $model->type = $type;
            $model->user_id = Yii::$app->user->id;
        }
        $model->last_update = $dateUpdate;

        return $model->save(false);
    }

    /**
     * Возвращает дату последней просмотренной записи
     * @param $tableName
     * @param null $type
     * @return bool|string
     */
    public static function getLastDate($tableName, $type = null)
    {
        return self::find()->select('last_update')->where([
            'user_id' => Yii::$app->user->id,
            'table_name' => $tableName
        ])->andFilterWhere([
            'type' => $type
        ])->scalar();
    }
}
