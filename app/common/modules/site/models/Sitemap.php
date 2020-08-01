<?php

namespace common\modules\site\models;

use common\modules\agency\models\Apartment as AgencyApartment;
use common\modules\partners\models\Apartment;
use common\modules\articles\models\Article;
use common\modules\realty\models\RentType;
use common\modules\pages\models\Page;
use common\modules\geo\models\City;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

/**
 * Sitemap
 * @package common\modules\site\models
 */
class Sitemap extends \yii\base\Model
{

    /**
     * Генерация общей карты сайта
     *
     * @return string
     */
    public function renderCommon()
    {
        $result = [];

        // Главная ссылка на домен
        $result[] = Html::tag('url', Html::tag('loc', Yii::$app->params['siteDomain']) .
            Html::tag('priority', '1.0')
        );

        // Список ссылок на объявления
        $apartments = $this->findAgencyApartments();
        foreach ($apartments as $apartment) {
            foreach ($apartment['adverts'] as $advert) {
                $result[] = Html::tag('url', Html::tag('loc', Url::to(
                        ['/agency/default/view',
                            'id' => $advert['advert_id']
                        ], true)) .
                    Html::tag('priority', '0.7')
                );
            }
        }

        // Список ссылок на статьи
        $articles = Article::find()->select(['article_id'])->status()->asArray()->all();
        $result[] = Html::tag('url', Html::tag('loc', Url::to(
                ['/articles/default/index'], true)) .
            Html::tag('priority', '0.9')
        );

        foreach ($articles as $article) {
            $result[] = Html::tag('url', Html::tag('loc', Url::to(
                    ['/articles/default/view',
                        'id' => $article['article_id']
                    ], true)) .
                Html::tag('priority', '0.7')
            );
        }

        // Список ссылок на страницы
        $pages = Page::find()->select(['page_id', 'url'])->status()->asArray()->all();
        foreach ($pages as $page) {

            $result[] = Html::tag('url', Html::tag('loc', Url::to(
                    ['/pages/default/index',
                        'url' => $page['url']
                    ], true)) .
                Html::tag('priority', '0.7')
            );
        }

        // Список ссылок на страницы
        $types = RentType::find()->select(['rent_type_id', 'slug'])->visible()->asArray()->all();
        foreach ($types as $type) {

            $result[] = Html::tag('url', Html::tag('loc', Url::to(
                    ['/site/default/index',
                        'rentType' => $type['slug']
                    ], true)) .
                Html::tag('priority', '0.9')
            );
        }

        // Ссылки на другие страницы
        $result[] = Html::tag('url', Html::tag('loc', Url::to(['/agency/default/index'], true)) .
            Html::tag('priority', '0.9')
        );

        $result[] = Html::tag('url', Html::tag('loc', Url::to(['/agency/default/select'], true)) .
            Html::tag('priority', '0.9')
        );

        $result[] = Html::tag('url', Html::tag('loc', Url::to(['/agency/default/want-pass'], true)) .
            Html::tag('priority', '0.9')
        );

        $result[] = Html::tag('url', Html::tag('loc', Url::to(['/partners/reservation/index'], true)) .
            Html::tag('priority', '0.9')
        );

        $result[] = Html::tag('url', Html::tag('loc', Url::to(['/partners/default/index'], true)) .
            Html::tag('priority', '0.9')
        );

        return $this->render(implode('', $result));
    }

    /**
     * Генерация карты сайта для города
     *
     * @param City $city
     * @return string
     */
    public function renderByCity(City $city)
    {
        $result = [];

        // Главная ссылка на поддомен
        $result[] = Html::tag('url', Html::tag('loc', str_replace('<city>', $city->name_eng, Yii::$app->params['siteSubDomain'])) .
            Html::tag('priority', '0.7')
        );

        // Список ссылок на объявления
        $apartments = $this->findPartnerApartments($city->city_id);

        foreach ($apartments as $apartment) {
            foreach ($apartment['adverts'] as $advert) {
                $result[] = Html::tag('url', Html::tag('loc', Url::to(
                    ['/partners/default/view',
                        'city' => $city->name_eng,
                        'id' => $advert['advert_id']
                    ], true)));
            }
        }

        return $this->render(implode('', $result));
    }

    /**
     * Рендер xml контента
     *
     * @param $content
     * @return string
     */
    public function render($content)
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>';
        return $xml . Html::tag('urlset', $content, [
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation' => 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd',
                'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
            ]);
    }

    /**
     * Поиск апартаментов агенства
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    protected function findAgencyApartments()
    {
        return AgencyApartment::find()
            ->select([AgencyApartment::tableName() . '.apartment_id'])
            ->joinWith(['adverts' => function ($query) {
                $query->select(['apartment_id', 'advert_id']);
            }])
            ->visible()
            ->asArray()
            ->all();
    }

    /**
     * Поиск апартаментов доски объявлений
     *
     * @param $city_id
     * @return array|\yii\db\ActiveRecord[]
     */
    protected function findPartnerApartments($city_id)
    {
        return Apartment::find()
            ->select([Apartment::tableName() . '.apartment_id', 'city_id'])
            ->where(['city_id' => $city_id])
            ->joinWith(['adverts' => function ($query) {
                $query->select(['apartment_id', 'advert_id']);
            }])
            ->permitted()
            ->asArray()
            ->all();
    }

}
