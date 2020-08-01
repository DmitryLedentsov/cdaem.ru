<?php

namespace common\modules\helpdesk\models;

use common\modules\helpdesk\traits\ModuleTrait;
use common\modules\users\models\User;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use Yii;

/**
 * Helpdesk
 * @package common\modules\helpdesk\models
 */
class Helpdesk extends ActiveRecord
{
    use ModuleTrait;


    /*public function behaviors()
    {
        return [
            'ActionBehavior' => [
                'class' => 'nepster\users\behaviors\ActionBehavior',
                'module' => $this->module->id,
                'actions' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'create-helpdesk',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'update-helpdesk',
                    ActiveRecord::EVENT_BEFORE_DELETE => 'delete-helpdesk',
                ],
            ],
        ];
    }*/

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%helpdesk}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_id' => 'ID',
            'user_id' => 'User ID',
            'email' => 'Email',
            'theme' => 'Тема обращения',
            'user_name' => 'Имя пользователя',
            'text' => 'Обращение',
            'date_create' => 'Дата создания',
            'date_close' => 'Дата закрытия',
            'priority' => 'Приоритет',
            'answered' => 'Есть ответ',
            'close' => 'Закрыт',
            'department' => 'Тип обращения',
            'partners_advert_id' => 'ID Объявления',
            'source_type' => 'Раздел',
            'ip' => 'IP',
            'user_agent' => 'User Agent',
        ];
    }

    /**
     * Отдел вопроса (поле "department")
     * - Технический
     * - Финансовый
     */
    const LETTER = 'letter';
    const LETTERWORK = 'letterwork';
    const COMPLAINT = 'complaint';
    const LETTERPHONE = 'letterphone';


    /**
     * @return array
     */
    public static function getDepartmentArray($id = null)
    {
        $statuses = [
            self::LETTER => [
                'label' => 'Письмо',
                'style' => 'color: black',
            ],
            self::COMPLAINT => [
                'label' => 'Жалоба',
                'style' => 'color: red',
            ],
            self::LETTERWORK => [
                'label' => 'Письмо Работа',
                'style' => 'color: black',
            ],

            self::LETTERPHONE => [
                'label' => 'Письмо номер телефона',
                'style' => 'color: black',
            ],
        ];

        if ($id !== null) {
            return ArrayHelper::getValue($statuses, 'label', null);
        }

        return $statuses;
    }

    /**
     * Приоритеты
     * - Критично
     * - Очень важно
     * - Важно
     * - Не важно
     */
    const CRITICAL = 3;
    const VERY_IMPORTANT = 2;
    const IMPORTANT = 1;
    const NOT_IMPORTANT = 0;

    /**
     * @return array
     */
    public static function getPriorityArray()
    {
        return [
            self::NOT_IMPORTANT => [
                'label' => 'Не срочно',
                'style' => 'color: silver',
            ],
            self::IMPORTANT => [
                'label' => 'Срочно',
                'style' => 'color: black',
            ],
            self::VERY_IMPORTANT => [
                'label' => 'Очень срочно',
                'style' => 'color: orange',
            ],
            self::CRITICAL => [
                'label' => 'Критично',
                'style' => 'color: #C71585',
            ],
        ];
    }

    /**
     * Статус ответа на обращение
     * - Закрыт
     * - Открыт
     */
    const ANSWERED = 1;
    const AWAITING = 0;

    /**
     * @return array Массив статусов ответа
     */
    public static function getAnsweredArray()
    {
        return [
            self::ANSWERED => [
                'label' => 'Есть ответ',
                'style' => 'color: green',
            ],
            self::AWAITING => [
                'label' => 'Без ответа',
                'style' => 'color: mediumvioletred',
            ],
        ];
    }

    /**
     * Статус обращения
     * - Закрыт
     * - Открыт
     */
    const CLOSED = 1;
    const OPEN = 0;

    /**
     * @return array
     */
    public static function getCloseArray()
    {
        return [
            self::CLOSED => [
                'label' => 'Закрыт',
                'style' => 'color: mediumvioletred',
            ],
            self::OPEN => [
                'label' => 'Открыт',
                'style' => 'color: orange',
            ],
        ];
    }

    /**
     * - Агенство
     * - Партнеры
     */
    const TYPE_AGENCY = 'agency';
    const TYPE_PARTNERS = 'partners';

    /**
     * @return array
     */
    public static function getSourceTypeArray()
    {
        return [
            'agency' => [
                'label' => 'Агентство',
                'color' => 'orange',
            ],
            'partners' => [
                'label' => 'Доска объявлений',
                'color' => 'mediumvioletred',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHelpdeskAnswers()
    {
        return $this->hasMany(HelpdeskAnswers::class, ['ticket_id' => 'ticket_id']);
    }
}
