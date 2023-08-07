<?php

namespace common\modules\agency\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\web\HttpException;
use yii\widgets\ActiveForm;
use common\modules\agency\models as models;
use common\modules\articles\models\Article;
use common\modules\agency\models\SpecialAdvert;
use common\modules\agency\models\search\AdvertSearch as AgencyAdvertSearch;

/**
 * Главный контроллер агенства
 * @package common\modules\agency\controllers\frontend
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['?', '@']
                    ],
                ]
            ],
        ];

        if (YII_DEBUG) {
            return $behaviors;
        }

        return array_merge($behaviors, require(__DIR__ . '/../../caching.php'));
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/agency/views/frontend';

        return true;
    }

    /**
     * Все объявления агенства
     * @return string
     */
    public function actionIndex()
    {
        $specialAdverts = SpecialAdvert::findActive();

        $agencySearch = new AgencyAdvertSearch();
        $agencyDataProvider = $agencySearch->allSearch(Yii::$app->request->get());

        $articlesQuery = Article::find()->orderBy(['date_create' => SORT_DESC]);
        $articles = $articlesQuery
            ->limit(13)
            ->asArray()
            ->all();

        return $this->render('index.twig', [
            'agencySearch' => $agencySearch,
            'specialAdverts' => $specialAdverts,
            'agencyDataProvider' => $agencyDataProvider,
            'articles' => $articles,
        ]);
    }

    /**
     * Просмотр объявлений
     * @param $id
     * @return array|string
     * @throws HttpException
     */
    public function actionView($id)
    {
        $model = models\Advert::getFullData($id);
        $reservationForm = new models\form\ReservationForm();
        $detailsHistoryForm = new models\form\DetailsHistoryForm();

        if (!$model) {
            throw new HttpException(404, 'Объявление под номером ' . $id . ' не найдено в базе данных');
        }

        // Резервация
        $reservationForm->apartment_id = $model->apartment_id;
        if ($reservationForm->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($reservationForm);
            if (!$errors) {
                if (Yii::$app->request->post('submit')) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    if ($reservationForm->create()) {
                        $status = 1;
                        $msg = '<p><strong>№ заявки: ' . $reservationForm->reservation_id . '</strong></p>';
                        $msg .= '<p>Заявка на бронирование успешно отправлена. Ожидайте связи с оператором. <br/> Пожалуйста запомните номер заявки и сообщите его оператору.</p>';
                    } else {
                        $status = 0;
                        $msg = 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.';
                    }

                    return [
                        'status' => $status,
                        'message' => $msg,
                    ];
                }

                return [];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            return $errors;
        }


        return $this->render('view.twig', [
            'model' => $model,
            'reservationForm' => $reservationForm,
            'detailsHistoryForm' => $detailsHistoryForm,
        ]);
    }

    /**
     * @param $id
     * @return array|string
     */
    public function actionRes($id)
    {
        $model = models\Advert::getFullData($id);
        $reservationForm = new models\form\ReservationForm();
        $detailsHistoryForm = new models\form\DetailsHistoryForm();

        if (!$model) {
            throw new HttpException(404, 'Объявление под номером ' . $id . ' не найдено в базе данных');
        }

        // Резервация
        $reservationForm->apartment_id = $model->apartment_id;
        if ($reservationForm->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($reservationForm);
            if (!$errors) {
                if (Yii::$app->request->post('submit')) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    if ($reservationForm->create()) {
                        $status = 1;
                        $msg = '<p><strong>№ заявки: ' . $reservationForm->reservation_id . '</strong></p>';
                        $msg .= '<p>Заявка на бронирование успешно отправлена. Ожидайте связи с оператором. <br/> Пожалуйста запомните номер заявки и сообщите его оператору.</p>';
                    } else {
                        $status = 0;
                        $msg = 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.';
                    }

                    return [
                        'status' => $status,
                        'message' => $msg,
                    ];
                }

                return [];
            }

            Yii::$app->response->format = Response::FORMAT_JSON;

            return $errors;
        }

        return $this->render('res.twig', [
            'model' => $model,
            'reservationForm' => $reservationForm,
            'detailsHistoryForm' => $detailsHistoryForm,
        ]);
    }

    /**
     * Подбор квартиры
     * @return array|string|Response
     */
    public function actionSelect()
    {
        $model = new models\form\SelectForm();
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = ActiveForm::validate($model);
            if (!$errors) {
                if ($model->create()) {
                    $status = 1;
                    $msg = 'Ваша заявка принята. С Вами свяжутся в ближайшее время.';
                } else {
                    $status = 0;
                    $msg = 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.';
                }

                return [
                    'status' => $status,
                    'message' => $msg,
                ];
            }

            return $errors;
        }

        return $this->render('select.twig', [
            'model' => $model,
        ]);
    }


    /**
     * Заявка на отправку реквизитов
     * @return array|Response
     */
    public function actionDetails()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new models\form\DetailsHistoryForm();
        $model->load(Yii::$app->request->post());

        $errors = ActiveForm::validate($model);
        if (!$errors) {
            if (Yii::$app->request->post('submit')) {
                if ($model->create()) {
                    return [
                        'status' => 1,
                        'message' => 'Ваша заявка принята. В течении нескольких минут мы отправим письмо с реквизитами для платежа на указанный Вами EMAIL.',
                    ];
                }

                return [
                    'status' => 0,
                    'message' => 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.',
                ];
            }

            return [];
        }

        return $errors;
    }

    /**
     * Поиск апартаментов
     * @return string
     */
    public function actionSearch()
    {
        return $this->render('search.twig', [
            'get' => Yii::$app->request->get()
        ]);
    }
}
