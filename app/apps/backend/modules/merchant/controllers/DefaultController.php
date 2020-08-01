<?php

namespace backend\modules\merchant\controllers;

use backend\modules\merchant\models\ServiceSearch;
use backend\modules\merchant\models\PaymentSearch;
use backend\modules\merchant\models\PaymentForm;
use common\modules\partners\models\Advert;
use common\modules\partners\models\Service;
use common\modules\users\models\User;
use backend\components\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class DefaultController
 * @package backend\modules\merchant\controllers
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => Yii::$app->getModule('users')->accessGroupsToControlpanel,
                    ],
                ]
            ],
        ];
    }

    /**
     * История денежного оборота
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('merchant-payment-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Оплаты услуг
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionService()
    {
        if (!Yii::$app->user->can('merchant-service-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $searchModel = new ServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $paidAdverts = $this->getPaidAdverts($dataProvider);

        return $this->render('service', [
            'searchModel' => $searchModel,
            'paidAdverts' => $paidAdverts,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Управление счетом
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionControl($id)
    {
        if (!Yii::$app->user->can('merchant-account-management')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        if (!$user = User::findOne($id)) {
            throw new NotFoundHttpException('Данный пользователь не найден.');
        }

        $model = new PaymentForm();
        $model->user_id = $user->id;

        $systems = Yii::$app->getModule('merchant')->systems['merchant'];

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->process()) {
                Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
            } else {
                Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
            }
            return $this->refresh();
        }

        return $this->render('control', [
            'user' => $user,
            'model' => $model,
            'systemsArray' => $systems,
        ]);
    }

    /**
     * TODO: to helper
     *
     * @param ActiveDataProvider $dataProvider
     * @return array
     */
    private function getPaidAdverts(ActiveDataProvider $dataProvider): array
    {
        $advertIdList = [];

        /** @var Service $model */
        foreach ($dataProvider->getModels() as $model) {
            foreach ($model->getSelectedAdvertIdList() as $advertId) {
                $advertIdList[$advertId] = $advertId;
            }
        }

        $services = Advert::find()->where(['advert_id' => $advertIdList])->with('apartment')->all();

        return ArrayHelper::index($services, 'advert_id');
    }
}
