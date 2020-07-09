<?php

namespace frontend\modules\site\controllers;

use common\modules\agency\models\form\WantPassForm;
use common\modules\agency\models\search\AdvertSearch as AgencyAdvertSearch;
use frontend\modules\partners\models\search\AdvertSearch as PartnersAdvertSearch;
use common\modules\agency\models\SpecialAdvert;
use common\modules\realty\models\RentType;
use frontend\modules\site\models\Sitemap;
use common\modules\pages\models\Page;
use common\modules\geo\models\City;
use frontend\modules\site\models\Taxi;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use yii\web\Response;
use common\modules\articles\models\Article;
use Yii;

/**
 * Главный контроллер сайта
 * @package frontend\modules\site\controllers
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
     */
    public function actionIndex($city = null)
    {
        echo 1;

        $agencySearch = new AgencyAdvertSearch();
        $agencyDataProvider = $agencySearch->search(Yii::$app->request->queryParams);

        $partnersSearch = new PartnersAdvertSearch();
        $partnersAdverts = $partnersSearch->siteSearch(Yii::$app->request->queryParams);
        $articlesQuery = Article::find()->orderBy(['date_create' => SORT_DESC]);

        if (!is_null($city)) {
            $articlesQuery->where(['city' => $city]);
        } else {
            $articlesQuery->where('city IS NULL');
        }

        $articles = $articlesQuery->limit(1)->asArray()->all();
        $specialAdverts = SpecialAdvert::findActive();
        $articlesQuery2 = Article::find()
            ->orderBy(['date_create' => SORT_DESC]);

        if (!is_null($city)) {
            $articlesQuery2->where(['city' => $city]);
        } else {
            $articlesQuery2->where('city IS NULL');
        }

        $articlesall3 = $articlesQuery2->limit(12)->asArray()->all();
        //$articlesall2 = array_shift($articlesall2);

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

        if ($metaData['slug'] != '/') {
            Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => URL::to('https://cdaem.ru/' . $metaData['slug'])]);
        } else {
            Yii::$app->view->registerLinkTag(['rel' => 'canonical', 'href' => URL::to('https://cdaem.ru')]);
        }

        return $this->render('index.twig', [
            'agencySearch' => $agencySearch,
            'agencyDataProvider' => $agencyDataProvider,
            'searchModel' => $partnersSearch,
            'partnersAdverts' => $partnersAdverts,
            'specialAdverts' => $specialAdverts,
            'metaData' => $metaData,
            'articles' => $articles,
            'articlesall2' => $articlesall2 ?? [],
        ]);
    }

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
        return $this->render('badbrowser.twig', [
        ]);
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
     * Форма заказа такси
     * @return array|string|Response
     */
    public function actionTaxi()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        $model = new Taxi();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = ActiveForm::validate($model);
            if (!$errors) {
                if (Yii::$app->request->post('submit')) {
                    if ($model->process()) {
                        return [
                            'status' => 1,
                            'message' => 'Заказ принят службой  TAXIREVERSi! 8(495)-979-9977'
                        ];
                    } else {
                        return [
                            'status' => 0,
                            'message' => 'Возникла критическая ошибка.'
                        ];
                    }
                }
                return [];
            }
            return $errors;
        }

        return $this->renderAjax('../ajax/taxi.php', [
            'model' => $model
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

        if ($city === null) {
            $sitemap = new Sitemap;
            return $sitemap->renderCommon();
        }

        $city = (substr($city, 0, 1) == '_') ? str_replace('_', '', $city) : $city;

        if (!$city = City::find()->where(['name_eng' => trim($city)])->one()) {
            throw new NotFoundHttpException;
        }

        return $sitemap->renderByCity($city);
    }

}
