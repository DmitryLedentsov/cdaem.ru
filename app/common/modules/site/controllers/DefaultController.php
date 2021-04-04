<?php

namespace common\modules\site\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;
use common\modules\geo\models\City;
use common\modules\pages\models\Page;
use common\modules\site\models\Sitemap;
use common\modules\realty\models\RentType;
use common\modules\articles\models\Article;
use common\modules\agency\models\SpecialAdvert;
use common\modules\agency\models\form\WantPassForm;
use common\modules\agency\models\search\AdvertSearch as AgencyAdvertSearch;
use common\modules\partners\models\frontend\search\AdvertSearch as PartnersAdvertSearch;

/**
 * Главный контроллер сайта
 * @package common\modules\site\controllers
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
                'view' => '@frontend/themes/basic/default/error.twig',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    /* public function behaviors() {
      if (!YII_DEBUG) {
      return require(__DIR__ . '/../caching/default.php');
      }
      return [];
      } */

    /**
     * Фильтр апартаментов
     * Показывает апартаменты агенства и апартаменты доски объявлений
     * по указанным фильтрам.
     *
     * В данном случае апартаменты доски зависят от фильтра апартаментов агенства
     *
     * @param null $city
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($city = null)
    {
        $agencySearch = new AgencyAdvertSearch();
        $agencyDataProvider = $agencySearch->search(Yii::$app->request->queryParams);

        $partnersSearch = new PartnersAdvertSearch();
        $partnersAdverts = $partnersSearch->siteSearch(Yii::$app->request->queryParams);

        $articlesQuery = Article::find()->orderBy(['date_create' => SORT_DESC]);

        if (is_null($city) === false) {
            $articlesQuery->where(['city' => $city]);
        } else {
            $articlesQuery->where('city IS NULL');
        }
        $articles = $articlesQuery->limit(1)->all();

        $specialAdverts = SpecialAdvert::findActive();

        $articlesQuery2 = Article::find()
            ->orderBy(['date_create' => SORT_DESC]);

        if (!is_null($city)) {
            $articlesQuery2->where(['city' => $city]);
        } else {
            $articlesQuery2->where('city IS NULL');
        }

        $articlesall2 = [];
        $articlesall3 = $articlesQuery2->limit(12)->all();

        $i = 0;
        foreach ($articlesall3 as $item) {
            if ($i > 0) {
                $articlesall2[] = $item;
            }
            $i++;
        }

        $metaData = RentType::findRentTypeBySlug(Yii::$app->request->get('rentType', '/'));

        if (!$metaData) {
            throw new NotFoundHttpException();
        }

        if ($metaData['slug'] !== '/') {
            Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => URL::to('https://cdaem.ru/' . $metaData['slug'])]);
        } else {
            Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => URL::to('https://cdaem.ru')]);
        }
        
        return $this->render('home/index.twig', [
            'agencySearch' => $agencySearch,
            'agencyDataProvider' => $agencyDataProvider,
            'searchModel' => $partnersSearch,
            'partnersAdverts' => $partnersAdverts,
            'specialAdverts' => $specialAdverts,
            'metaData' => $metaData,
            'articles' => $articles,
            'articlesall2' => $articlesall2,
        ]);
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDayindex()
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

        return $this->render('dayindex.twig', [
            'agencySearch' => $agencySearch,
            'agencyDataProvider' => $agencyDataProvider,
            'searchModel' => $partnersSearch,
            'partnersAdverts' => $partnersAdverts,
            'specialAdverts' => $specialAdverts,
            'metaData' => $metaData,
        ]);
    }

    /**
     * @return string
     */
    public function actionBadbrowser()
    {
        return $this->render('badbrowser.twig');
    }

    /**
     * @return array|string
     * @throws NotFoundHttpException
     * @throws \yii\web\HttpException
     */
    public function actionPartnership()
    {
        $model = Page::getPageByUrl('partnership');

        if (!$model) {
            throw new NotFoundHttpException;
        }

        $formModel = new WantPassForm(['scenario' => 'partnership']);

        if ($formModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = ActiveForm::validate($formModel);
            if (!$errors) {
                if ($formModel->create()) {
                    $status = 1;
                    $msg = 'Ваша заявка принята. Пожалуйста ожидайте ответа.';
                } else {
                    $status = 1;
                    $msg = 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.';
                }

                return [
                    'status' => $status,
                    'message' => $msg,
                ];
            }

            return $errors;
        }

        return $this->render('partnership.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    /**
     * Генерация карты сайта
     *
     * @param null $city
     * @return string|void
     * @throws NotFoundHttpException
     */
    public function actionSitemap($city = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;

        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml');

        $sitemap = new Sitemap;

        if ($city === null) {
            return $sitemap->renderCommon();
        }

        $city = (substr($city, 0, 1) == '_') ? str_replace('_', '', $city) : $city;

        /** @var City|null $city */
        $city = City::find()->where(['name_eng' => trim($city)])->one();

        if ($city === null) {
            throw new NotFoundHttpException;
        }

        return $sitemap->renderByCity($city);
    }
}
