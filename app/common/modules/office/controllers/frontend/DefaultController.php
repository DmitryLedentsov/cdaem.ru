<?php

namespace common\modules\office\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\web\HttpException;
use yii\widgets\ActiveForm;
use common\modules\pages\models\Page;
use common\modules\users\models\Profile;
use common\modules\partners\models\frontend as models;
use common\modules\users\models\frontend\UsersListSearch;

/**
 * Главный контроллер офиса
 * @package common\modules\office\controllers\frontend
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
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];

        if (YII_DEBUG) {
            return $behaviors;
        }

        return array_merge($behaviors, require(__DIR__ . '/../../caching/default.php'));
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $actions = ['apartments', 'services', 'send-offer', 'services', 'services'];

        $this->module->viewPath = '@common/modules/office/views/frontend';

        if (in_array($action->id, $actions)) {
            if (!Yii::$app->user->isGuest && Yii::$app->user->identity->profile->user_type == Profile::WANT_RENT) {
                $userType = Yii::$app->BasisFormat->helper('Status')->getItem(Profile::getUserTypeArray(), Yii::$app->user->identity->profile->user_type);
                //Yii::$app->session->setFlash('danger', '<b>Внимание:</b> <br/> Ваш тип аккаунта: "'. $userType . '" и Вы не можете производить данное действие. ');
                $this->redirect(['/office/default/index']);

                return false;
            }
        }

        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }

    /**
     * Главная страница
     * @return Response
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->profile->user_type == Profile::WANT_RENT) {
            $this->redirect(['default/home']);
        }

        return $this->redirect(['/office/apartments']);
    }

    public function actionHome()
    {
        $searchModel = new models\search\AdvertSearch();

        return $this->render('home.twig', [
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Мои покупки
     * @return string
     */
    public function actionOrders()
    {
        $searchModel = new models\search\ServiceSearch();
        $dataProvider = $searchModel->search();

        return $this->render('orders.twig', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Реклама
     * @return string
     */
    public function actionTopSlider()
    {
        $searchModel = new models\search\AdvertisementSliderSearch();
        $dataProvider = $searchModel->search();

        return $this->render('advertising.twig', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Купить рекламу
     * @return array|string|Response
     * @throws \Exception
     */
    public function actionBuyAds()
    {
        $advertisementForm = new models\form\AdvertisementSliderForm();
        $advertisementForm->defineScenario();

        if (Yii::$app->request->isAjax && $advertisementForm->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $errors = ActiveForm::validate($advertisementForm);
            if (!$errors) {
                if ($advertisementForm->save(false)) {
                    Yii::$app->session->setFlash('success', 'Ваша заявка принята. Пожалуйста произведите оплату и Ваше рекламное объявление появится вверху в течении нескольких секунд.');

                    return $this->redirect(['/office/default/top-slider']);
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.');

                    return $this->refresh();
                }
            }

            return $errors;
        }

        return $this->render('buy-ads.twig', [
            'advertisementForm' => $advertisementForm,
        ]);
    }

    /**
     * Отправить предложение
     * @return string
     */
    public function actionSendOffer()
    {
        return $this->render('send-offer.twig', [
        ]);
    }

    /**
     * Избранное
     * @return string
     */
    public function actionBookmark()
    {
        $searchModel = new UsersListSearch();
        $dataProvider = $searchModel->search(['bookmarked' => true, 'user_id' => Yii::$app->user->id]);

        return $this->render('bookmark.twig', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Черный список
     * @return string
     */
    public function actionBlacklist()
    {
        $searchModel = new UsersListSearch();
        $dataProvider = $searchModel->search(['blacklisted' => true, 'user_id' => Yii::$app->user->id]);

        return $this->render('blacklist.twig', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Услуги
     * @return string
     * @throws \Exception
     */
    public function actionServices()
    {
        return $this->render('services.twig');
    }
}
