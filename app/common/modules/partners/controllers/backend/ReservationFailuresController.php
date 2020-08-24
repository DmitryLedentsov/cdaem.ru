<?php

namespace common\modules\partners\controllers\backend;

use common\components\Response;
use Yii;
use yii\widgets\ActiveForm;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use backend\modules\partners\models\ReservationFailure;
use backend\modules\partners\models\ReservationFailureSearch;

/**
 * ReservationFailuresController implements the CRUD actions for Reservation model.
 * @package common\modules\partners\controllers\backend
 */
class ReservationFailuresController extends Controller
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
     * Просмотр заявок "Незаезд"
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('partners-reservation-failure-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $searchModel = new ReservationFailureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Редактировать заявку
     *
     * @param $id
     * @return array|string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('partners-reservation-failure-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('partners-reservation-failure-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }

            if ($model->validate()) {
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $model->id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Удалить заявку
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('partners-reservation-failure-delete')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Reservation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param $id
     * @return ReservationFailure|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = ReservationFailure::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
