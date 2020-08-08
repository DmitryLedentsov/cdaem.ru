<?php

namespace common\modules\helpdesk\models\backend;

use common\modules\users\models\User;
use common\modules\helpdesk\models\Helpdesk;

/**
 * Helpdesk Form
 * @package common\modules\helpdesk\models\backend
 */
class HelpdeskForm extends \yii\base\Model
{
    public $ticket_id;

    public $user_id;

    public $email;

    public $theme;

    public $user_name;

    public $text;

    public $date_create;

    public $date_close;

    public $priority;

    public $answered;

    public $close;

    public $department;

    public $partners_advert_id;

    public $source_type;

    public $ip;

    public $user_agent;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'Helpdesk';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new Helpdesk())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => ['theme', 'text', 'priority', 'user_id', 'answered', 'department', 'source_type'],
            'delete' => [],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'user_id', 'priority', 'answered', 'close'], 'integer'],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            ['email', 'email'],
            [['theme', 'user_name'], 'string', 'max' => 100],
            ['text', 'string'],
            ['priority', 'in', 'range' => array_keys(Helpdesk::getPriorityArray())],
            ['answered', 'in', 'range' => array_keys(Helpdesk::getAnsweredArray())],
            ['close', 'in', 'range' => array_keys(Helpdesk::getCloseArray())],
            ['department', 'in', 'range' => array_keys(Helpdesk::getDepartmentArray())],
            ['source_type', 'in', 'range' => array_keys(Helpdesk::getSourceTypeArray())],
            [['date_create', 'date_close'], 'date', 'format' => 'php:Y-m-d'],
            [['ip', 'user_agent'], 'safe'],
            //required on
            [['email', 'theme', 'text', 'priority', 'answered', 'close', 'department'], 'required', 'on' => 'update'],
        ];
    }

    /**
     * @param Helpdesk $model
     * @return bool
     */
    public function update(Helpdesk $model)
    {
        if ($this->answered == 1 && $model->answered != 1) {
            $this->date_close = date('Y-m-d H:i:s');
            $this->close = 1;
        }

        $model->setAttributes($this->getAttributes(), false);

        //@TODO: Записать действия в лог
        return $model->save();
    }

    /**
     * Удалить
     *
     * @param Helpdesk $model
     * @return false|int
     * @throws \Exception
     */
    public function delete(Helpdesk $model)
    {
        //@TODO: Записать действия в лог
        return $model->delete();
    }
}
