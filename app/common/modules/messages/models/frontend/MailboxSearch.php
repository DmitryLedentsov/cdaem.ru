<?php

namespace common\modules\messages\models\frontend;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\messages\models\Mailbox;

/**
 * Class MailboxSearch
 * @package common\modules\messages\models\frontend
 */
class MailboxSearch extends Mailbox
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function conversationsSearch($params)
    {
        $query = Mailbox::find()
            ->deleted(0)
            ->thisUser()
         //   ->groupBy('`interlocutor_id` DESC') TODO: устарело в MYSQL 8
            ->with('message');

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function dialogSearch($params)
    {
        $query = Mailbox::find()
            ->deleted(0)
            ->thisUser()
            ->andWhere(['interlocutor_id' => $params['interlocutor_id']])
            ->with('message');

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 50,
            ],
        ]);
    }
}
