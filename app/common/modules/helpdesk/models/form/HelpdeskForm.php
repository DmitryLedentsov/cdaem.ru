<?php

namespace common\modules\helpdesk\models\form;

use Yii;
use common\modules\partners\models\Advert;
use common\modules\helpdesk\models\Helpdesk;

/**
 * Class HelpdeskForm
 * @package common\modules\helpdesk\models\form
 */
class HelpdeskForm extends \yii\base\Model
{
    public $theme;

    public $text;

    public $priority;

    public $email;

    public $user_name;

    public $partners_advert_id;

    public $source_type;

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
            'guest-ask' => ['theme', 'text', 'priority', 'email', 'user_name'],
            'guest-ask-work' => ['theme', 'text', 'priority', 'email', 'user_name'],
            'user-ask' => ['theme', 'text', 'priority'],
            'guest-ask-phone' => ['theme', 'priority'],
            'guest-complaint-phone' => ['theme', 'priority'],
            'guest-complaint' => ['partners_advert_id', 'theme', 'text', 'priority', 'email', 'user_name'],
            'guest-complaint-work' => ['partners_advert_id', 'theme', 'text', 'priority', 'email', 'user_name'],
            'user-complaint' => ['partners_advert_id', 'theme', 'text', 'priority'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme', 'text', 'priority', 'email', 'user_name'], 'required'],
            [['user_id', 'priority', 'answered', 'close'], 'integer'],
            ['priority', 'in', 'range' => array_keys(Helpdesk::getPriorityArray())],
            ['source_type', 'in', 'range' => array_keys(Helpdesk::getSourceTypeArray())],
            ['answered', 'in', 'range' => array_keys(Helpdesk::getAnsweredArray())],
            ['close', 'in', 'range' => array_keys(Helpdesk::getCloseArray())],
            [['text'], 'string'],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['theme', 'user_name'], 'string', 'max' => 100],
            ['partners_advert_id', 'required'],
            ['partners_advert_id', 'exist', 'targetClass' => Advert::class, 'targetAttribute' => 'advert_id'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        parent::afterValidate();

        if ($this->hasErrors() === false && ($this->scenario === 'guest-ask' || $this->scenario === 'user-ask')) {
            $checkIfPossibleCreateNewTicket = (bool)Helpdesk::countRecentTicketsByUserIdentity(
                Yii::$app->request->userIP,
                Yii::$app->request->userAgent
            );
            if ($checkIfPossibleCreateNewTicket) {
                $this->addError('text', 'Слишком частое обращение к серверу, попробуйте через минуту.');
            }
        }
    }

    /**
     * Техническая поддержка агенства
     * @return bool
     */
    public function ask()
    {
        $model = $this->createModel();
        $model->source_type = Helpdesk::TYPE_AGENCY;
        $model->department = Helpdesk::LETTER;

        return $model->save();
    }

    public function vacant()
    {
        $model = $this->createModel();
        $model->source_type = Helpdesk::TYPE_AGENCY;
        $model->department = Helpdesk::LETTERWORK;

        return $model->save();
    }

    public function phonehelp()
    {
        $model = $this->createModel();
        $model->source_type = Helpdesk::TYPE_AGENCY;
        $model->department = Helpdesk::LETTERPHONE;
        $model->text = "Поменяйте мне основной номер телефона пожалуйста";

        return $model->save();
    }

    /**
     * Техническая поддержка доски объявлений
     */
    public function help(): void
    {
        $model = $this->createModel();
        $model->source_type = Helpdesk::TYPE_PARTNERS;
        $model->department = Helpdesk::LETTER;

        $model->save(false);
    }

    /**
     * Жалоба на объявление
     * @return bool
     */
    public function complaint()
    {
        $model = $this->createModel();
        $model->source_type = Helpdesk::TYPE_PARTNERS;
        $model->department = Helpdesk::COMPLAINT;
        $model->partners_advert_id = $this->partners_advert_id;

        return $model->save();
    }

    /**
     * Избегаем дублирования кода.
     * Создаем модель и устанавливаем атрибуты.
     * @return Helpdesk
     */
    private function createModel()
    {
        $model = new Helpdesk();
        if (Yii::$app->user->isGuest) {
            $model->email = $this->email;
            $model->user_name = $this->user_name;
        } else {
            $model->user_id = Yii::$app->user->id;
            $model->email = Yii::$app->user->identity->email;
        }

        $model->theme = $this->theme;
        $model->text = $this->text;
        $model->date_create = date('Y-m-d H:i:s');
        $model->date_close = date('Y-m-d H:i:s');
        $model->priority = $this->priority;
        //$model->answered = 0;
        //$model->close = 0;
        $model->ip = Yii::$app->request->userIp;
        $model->user_agent = Yii::$app->request->userAgent;
        $model->source_type = $this->source_type;

        return $model;
    }
}
