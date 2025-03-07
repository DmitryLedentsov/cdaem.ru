<?php

namespace common\modules\helpdesk\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\modules\users\models\User;
use common\modules\helpdesk\traits\ModuleTrait;

/**
 * Helpdesk
 * @package common\modules\helpdesk\models
 */
class Helpdesk extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%helpdesk}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'ticket_id' => 'ID',
            'user_id' => 'User ID',
            'email' => 'Email',
            'phone' => 'Телефон',
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
    public static function getDepartmentArray($id = null): array
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
    public static function getPriorityArray(): array
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
    public static function getAnsweredArray(): array
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
    public static function getCloseArray(): array
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
    public static function getSourceTypeArray(): array
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
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHelpdeskAnswers(): ActiveQuery
    {
        return $this->hasMany(HelpdeskAnswers::class, ['ticket_id' => 'ticket_id']);
    }

    /**
     * Считает кол-во последних записей за пройденный промежуток времени
     * Учитывает ip и userAgent
     *
     * @param string $ip
     * @param string $userAgent
     * @return int
     * @throws \yii\db\Exception
     */
    public static function countRecentTicketsByUserIdentity(string $ip, string $userAgent): int
    {
        $range = 1; // minutes
        $hash = md5(sprintf('%s-%s', $ip, $userAgent));

        return (int)Yii::$app->db->createCommand("
            SELECT COUNT(ticket_id)
                FROM " . Helpdesk::tableName() . "
                WHERE MD5(CONCAT(ip, \"-\",  user_agent)) = :hash
                AND date_create > date_sub(now(), interval :range minute)
            LIMIT 1;
        ")
            ->bindValue(':hash', $hash)
            ->bindValue(':range', $range)
            ->queryScalar();
    }
}
