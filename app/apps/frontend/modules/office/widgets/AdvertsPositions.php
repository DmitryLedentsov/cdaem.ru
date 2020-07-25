<?php

namespace frontend\modules\office\widgets;

use frontend\modules\partners\models\Advert;
use yii\base\Widget;
use yii\helpers\Html;
use Yii;

class AdvertsPositions extends Widget
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
        $cacheKey = 'AdvertsPositionsWidget';
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

            $userAdverts = Advert::getAdvertsByUser(Yii::$app->user->id, 10);

            $cachedData = $this->render('advertsPositions', [
                'userAdverts' => $userAdverts,
            ]);

            Yii::$app->cache->set($cacheKey, $cachedData, 300, $cacheDependency);
        }

        return $cachedData;
    }
}