<?php

namespace common\modules\users\models\backend\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\users\models\backend\User;
use common\modules\users\models\backend\Profile;

/**
 * @inheritdoc
 */
class UserSearch extends User
{
    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $contacts;

    /**
     * @var string
     */
    public $date_from;

    /**
     * @var string
     */
    public $date_to;

    /**
     * @var bool
     */
    public $banned;

    /**
     * @var bool
     */
    public $online;

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'profile.user_type',
            'profile.user_partner',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['user', 'contacts', 'date_from', 'date_to'], 'string'],
            ['group', 'in', 'range' => array_keys(\nepster\users\rbac\models\AuthItem::getGroupsArray())],
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],
            ['banned', 'in', 'range' => array_keys(Yii::$app->formatter->booleanFormat)],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
            ['profile.user_type', 'in', 'range' => array_keys(Profile::getUserTypeArray())],
            ['profile.user_partner', 'in', 'range' => array_keys(Profile::getUserPartnerArray())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        return array_merge($labels, [
            'user' => Yii::t('users', 'USER'),
            'contacts' => Yii::t('users', 'CONTACTS'),
            'date_from' => Yii::t('users', 'DATE_FROM'),
            'date_to' => Yii::t('users', 'DATE_TO'),
            'profile.user_partner' => 'Партнер',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = Model::scenarios();

        return array_merge($scenarios, [
            'profile.user_type',
            'profile.user_partner',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 's';
    }

    /**
     * Создает экземпляр поставщика данных с поисковым запросом
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find()
            ->joinWith([
                'profile',
                'banned'
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'time_register' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['user'] = [
            'asc' => [self::tableName() . '.id' => SORT_ASC],
            'desc' => [self::tableName() . '.id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['contacts'] = [
            'asc' => [self::tableName() . '.email' => SORT_ASC],
            'desc' => [self::tableName() . '.email' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['banned'] = [
            'asc' => ['time_banned' => SORT_ASC],
            'desc' => ['time_banned' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->andFilterWhere([
            self::tableName() . '.id' => $this->id,
            'status' => $this->status,
            'group' => $this->group,
            'user_type' => $this->getAttribute('profile.user_type'),
            'user_partner' => $this->getAttribute('profile.user_partner'),
        ]);

        // Поиск по имени и фамилии
        if (!empty($this->user)) {
            $query->andWhere('name LIKE :name OR surname LIKE :surname', [':name' => '%' . $this->user . '%', ':surname' => '%' . $this->user . '%']);
        }

        // Поиск по контактам
        if (!empty($this->contacts)) {
            $query->andWhere(['OR', self::tableName() . '.phone LIKE :contacts', self::tableName() . '.email LIKE :contacts'])
                ->addParams([':contacts' => '%' . $this->contacts . "%"]);
        }

        // Если статус не указан, по умолчанию не показываем удаленных пользователей
        if (!isset($this->status)) {
            $query->andWhere('status != :status', [':status' => $this::STATUS_DELETED]);
        }


        if (isset($this->banned)) {
            $query->banned($this->banned);
        }

        // Поиск по дате регистрации, от
        if (!empty($this->date_from)) {
            $dateFrom = strtotime($this->date_from);
            $query->andWhere('time_register >= :date_from', [':date_from' => $dateFrom]);
        }

        // Поиск по дате регистрации, до
        if (!empty($this->date_to)) {
            $dateTo = strtotime($this->date_to) + 86400; // Минимальный диапазон выборки 24 часа
            $query->andWhere('time_register <= :date_to', [':date_to' => $dateTo]);
        }

        return $dataProvider;
    }
}
