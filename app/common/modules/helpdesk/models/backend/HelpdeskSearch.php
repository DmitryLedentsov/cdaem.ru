<?php

namespace common\modules\helpdesk\models\backend;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\helpdesk\models\Helpdesk;

/**
 * Helpdesk Search
 * @package common\modules\helpdesk\models\backend
 */
class HelpdeskSearch extends Model
{
    public $ticket_id;

    public $user_id;

    public $user_name;

    public $priority;

    public $answered;

    public $close;

    public $email;

    public $source_type;

    public $theme;

    public $text;

    public $department;

    public $date_create;

    public $date_close;

    public $ip;

    public $user_agent;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'user_id', 'priority', 'answered', 'close'], 'integer'],
            ['email', 'string', 'max' => 255],
            ['source_type', 'in', 'range' => array_keys(Helpdesk::getSourceTypeArray())],
            [['theme', 'user_name'], 'string', 'max' => 100],
            ['text', 'string'],
            ['priority', 'in', 'range' => array_keys(Helpdesk::getPriorityArray())],
            ['answered', 'in', 'range' => array_keys(Helpdesk::getAnsweredArray())],
            ['close', 'in', 'range' => array_keys(Helpdesk::getCloseArray())],
            ['department', 'in', 'range' => array_keys(Helpdesk::getDepartmentArray())],
            ['date_create', 'date', 'format' => 'php:Y-m-d'],
            [['date_close', 'department', 'ip', 'user_agent'], 'safe'],
        ];
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Helpdesk::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_create' => SORT_DESC,
                    'priority' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'ticket_id' => $this->ticket_id,
            'user_id' => $this->user_id,
            'date(date_create)' => $this->date_create,
            'date_close' => $this->date_close,
            'priority' => $this->priority,
            'answered' => $this->answered,
            'close' => $this->close,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'theme', $this->theme])
            ->andFilterWhere(['like', 'source_type', $this->source_type])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'user_agent', $this->user_agent]);

        return $dataProvider;
    }
}
