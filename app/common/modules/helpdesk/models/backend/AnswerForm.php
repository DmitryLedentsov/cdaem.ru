<?php

namespace common\modules\helpdesk\models\backend;

use common\modules\helpdesk\models\HelpdeskAnswers;
use common\modules\helpdesk\models\Helpdesk;
use common\modules\users\models\User;
use yii\base\Model;
use Yii;

/**
 * Answer Form
 * @package common\modules\helpdesk\models\backend
 */
class AnswerForm extends Model
{
    public $ticket_id;
    public $user_id;
    public $text;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'HelpdeskAnswer';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge((new HelpdeskAnswers())->attributeLabels(), [

        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'update' => ['ticket_id', 'user_id', 'text'],
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
            ['ticket_id', 'exist', 'targetClass' => Helpdesk::class, 'targetAttribute' => 'ticket_id'],
            ['text', 'string'],
        ];
    }

    /**
     * Create
     * @return mixed
     * @throws \Exception
     */
    public function create()
    {
        $transaction = HelpdeskAnswers::getDb()->beginTransaction();

        try {
            $result = $this->createInternal();
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Create
     * @return bool
     */
    public function createInternal()
    {
        $model = new HelpdeskAnswers();
        $model->ticket_id = $this->ticket_id;
        $model->text = $this->text;
        $model->user_id = Yii::$app->user->id;
        $model->date = date('Y-m-d H:i:s');

        $ticket = Helpdesk::findOne($this->ticket_id);
        $ticket->answered = 1;
        $ticket->close = 1;

        if (!$ticket->save(false) or !$model->save(false))
            return false;

        // Отправить email
        Yii::$app->consoleRunner->run('helpdesk/mail/admin-answer ' . $model->answer_id);
        return true;
    }

}