<?php

namespace common\modules\realty\controllers\backend;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\modules\realty\models\RentType;
use common\modules\realty\models\backend\search\RentTypeSearch;

/**
 * DefaultController
 * @package common\modules\realty\controllers\backend
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
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            $this->module->viewPath = '@common/modules/realty/views/backend';

            return true;
        }

        return false;
    }

    /**
     * Все
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('realty-rent-type-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $searchModel = new RentTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Создать
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('realty-rent-type-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = new RentType(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect('index');
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
     * Редактировать
     * @param $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('realty-rent-type-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('realty-rent-type-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }

            if ($model->validate()) {
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->refresh();
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
     * Finds the Advertisement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RentType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RentType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
