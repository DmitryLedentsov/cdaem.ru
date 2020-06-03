<?php

namespace common\modules\users\models\frontend;

use common\modules\users\models\UsersList;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use Yii;

/**
 * Class UsersListSearch
 */
class UsersListSearch extends UsersList
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
    public function search($params)
    {        
        $query = UsersList::find()
            ->joinWith([
                'interlocutor' => function ($query) {
                    $query->banned(0)->with('profile');
                },
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);

        $query->filterWhere([
            self::tableName() . '.user_id' => $params['user_id'],
        ]);
        
        if (!empty($params['bookmarked'])) {
            $query->bookmarked();
        }
        
        if (!empty($params['blacklisted'])) {
            $query->blacklisted();
        }

        return $dataProvider;
    }
}
