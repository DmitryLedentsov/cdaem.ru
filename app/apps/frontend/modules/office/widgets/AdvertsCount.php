<?php

namespace frontend\modules\office\widgets;

use frontend\modules\partners\models\Advert;
use yii\base\Widget;
use yii\helpers\Html;
use Yii;

class AdvertsCount extends Widget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        // Ключ кэша, в вариациях юзер айди
        $cacheKey = 'AdvertsCountWidget';
        $cacheKey .= '_' . Yii::$app->user->id;

        // Зависимость кэша
        $cacheDependency = new \yii\caching\DbDependency([
            'sql' => 'SELECT MAX(date_update), count(*) FROM {{%partners_apartments}} WHERE user_id = :user_id',
            'params' => [':user_id' => Yii::$app->user->id],
            'reusable' => true,
        ]);

        // Вынимаем данные из кэша
        $cachedData = Yii::$app->cache->get($cacheKey);

        // Если нету, то сгенерируем
        if ($cachedData === false) {

            $userAdverts = Advert::find()
                ->select('rent_type')
                ->joinWith([
                    'apartment' => function ($query) {
                        $query->permitted();
                    }
                ])
                ->with('rentType')->where(['user_id' => Yii::$app->user->id])->all();

            $userAdvertsCount = [];
            foreach ($userAdverts as $advert) {
                $userAdvertsCount[$advert['rent_type']]['rentTypeName'] = $advert->rentType->name;
                $userAdvertsCount[$advert['rent_type']]['slug'] = $advert->rentType->slug;
                $userAdvertsCount[$advert['rent_type']]['count'] = isset($userAdvertsCount[$advert['rent_type']]['count']) ? $userAdvertsCount[$advert['rent_type']]['count']+1 : 1;
            }

            $cachedData = $this->render('advertsCount', [
                'userAdvertsCount' => $userAdvertsCount,
            ]);

            Yii::$app->cache->set($cacheKey, $cachedData, 300, $cacheDependency);
        }

        return $cachedData;
    }
}