<?php

namespace common\modules\site\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\modules\geo\models\City;
use common\modules\site\models\Sitemap;
use common\modules\realty\models\RentType;
use common\modules\articles\models\Article;
use common\modules\agency\models\SpecialAdvert;
use common\modules\agency\models\search\AdvertSearch as AgencyAdvertSearch;
use common\modules\partners\models\frontend\search\AdvertSearch as PartnersAdvertSearch;

class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
                'view' => '@common/modules/site/views/default/error.twig',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    /* public function behaviors(): array {
      if (!YII_DEBUG) {
      return require(__DIR__ . '/../caching/default.php');
      }
      return [];
      } */

    /**
     * Фильтр апартаментов.
     * Показывает апартаменты агенства и апартаменты доски объявлений
     * по указанным фильтрам.
     *
     * В данном случае апартаменты доски зависят от фильтра апартаментов агенства
     *
     * @param string|null $city
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionIndex(?string $city = null): Response
    {
        $metaData = RentType::findRentTypeBySlug(Yii::$app->request->get('rentType', '/'));

        if (empty($metaData)) {
            throw new NotFoundHttpException();
        }

        $partnersSearch = new PartnersAdvertSearch();

        $articles = Article::find()
            ->where(is_null($city) ? 'city IS NULL' : ['city' => $city])
            ->orderBy(['date_create' => SORT_DESC])
            ->visible()
            ->status(Article::STATUS_ALL)
            ->limit(6)
            ->all();

        if ($metaData['slug'] !== '/') {
            Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => URL::to('https://cdaem.ru/' . $metaData['slug'])]);
        } else {
            Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => URL::to('https://cdaem.ru')]);
        }

        return $this->response($this->render('home/index.twig', [
            'metaData' => $metaData,
            'articles' => $articles,
            'searchModel' => $partnersSearch,
        ]));
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDayindex(): Response
    {
        $agencySearch = new AgencyAdvertSearch();
        $agencyDataProvider = $agencySearch->search(Yii::$app->request->queryParams);
        $partnersSearch = new PartnersAdvertSearch();
        $partnersAdverts = $partnersSearch->siteSearch(Yii::$app->request->queryParams);

        $specialAdverts = SpecialAdvert::findActive();

        $metaData = RentType::findRentTypeBySlug(Yii::$app->request->get('rentType', '/'));

        if (!$metaData) {
            throw new NotFoundHttpException();
        }

        return $this->response($this->render('dayindex.twig', [
            'agencySearch' => $agencySearch,
            'agencyDataProvider' => $agencyDataProvider,
            'searchModel' => $partnersSearch,
            'partnersAdverts' => $partnersAdverts,
            'specialAdverts' => $specialAdverts,
            'metaData' => $metaData,
        ]));
    }

    /**
     * @return Response
     */
    public function actionBadbrowser(): Response
    {
        return $this->response($this->render('badbrowser.twig'));
    }

    /**
     * Генерация карты сайта
     *
     * @param string|null $city
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionSitemap(?string $city = null): Response
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml');

        $sitemap = new Sitemap;

        if ($city === null) {
            return $this->response($sitemap->renderCommon());
        }

        $city = ($city[0] === '_') ? str_replace('_', '', $city) : $city;

        /** @var City|null $city */
        $cityModel = City::find()->where(['name_eng' => trim($city ?? '')])->one();

        if ($cityModel === null) {
            throw new NotFoundHttpException;
        }

        return $this->response($sitemap->renderByCity($cityModel));
    }
}
