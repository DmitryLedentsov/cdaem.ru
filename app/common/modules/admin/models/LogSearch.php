<?php

namespace common\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;
use yii\data\ActiveDataProvider;
use common\modules\admin\models\Log;

/**
 * Class LogSearch
 * @package common\modules\admin\models
 */
class LogSearch extends Model
{
    public $id;

    public $level;

    public $category;

    public $prefix;

    public $message;

    public $log_time;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'level'], 'integer'],
            [['category'], 'in', 'range' => array_keys(self::getCategoryArray())],
            [['log_time'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function scenarios()
    {
        return [
            'search' => ['id', 'level', 'category', 'log_time'],
        ];
    }

    public function formName()
    {
        return 's';
    }

    /**
     * Поиск
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Log::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'log_time' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'level' => $this->level,
        ]);

        if ($this->category) {
            $query->andWhere(['category' => self::getCategoryArray()[$this->category]]);
        }

        if ($this->log_time) {
            $date = (new \DateTime($this->log_time));
            $start = $date->format('U');
            $date->add(new \DateInterval('P1D'));
            $end = $date->format('U');

            $query->andWhere('log_time BETWEEN :start AND :end')
                ->params([
                    ':start' => $start,
                    ':end' => $end,
                ]);
        }

        return $dataProvider;
    }

    /**
     * Все созданные категории
     * @return array
     */
    public static function getCategoryArray()
    {
        $dependecy = new DbDependency([
            'sql' => 'SELECT COUNT(*) FROM {{%log}}',
        ]);

        $cachedData = Yii::$app->cache->get('arrayOfCategoriesFromLog');

        // Если нету, то сгенерируем
        if ($cachedData === false) {
            $categories = Log::find()
                ->select('category')
                ->distinct()
                ->asArray()
                ->all();

            $cachedData = ArrayHelper::getColumn($categories, 'category');

            Yii::$app->cache->set('arrayOfCategoriesFromLog', $cachedData, 0, $dependecy);
        }

        return $cachedData;
    }
}
