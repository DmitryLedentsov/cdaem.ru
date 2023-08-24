<?php

namespace common\modules\agency\controllers\backend;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\modules\agency\models\Select;
use common\modules\agency\models\backend\form\SelectForm;
use common\modules\agency\models\backend\search\SelectSearch;

/**
 * Select Controller
 * @package common\modules\agency\controllers\backend
 */
class SelectController extends Controller
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
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/agency/views/backend';

        return true;
    }

    /**
     * Просмотр заявок
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('agency-select-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Url::remember();

        $searchModel = new SelectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Редактировать заявку
     * @param $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('agency-select-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);

        $formModel = new SelectForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('agency-select-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }

            if ($formModel->validate()) {
                if ($formModel->update($model)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $formModel->apartment_select_id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($formModel);
            }
        }

        return $this->render('update', [
            'formModel' => $formModel,
            'model' => $model,
        ]);
    }

    /**
     * Удалить заявку
     * @param $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('agency-select-delete')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        if ($this->findModel($id)->delete()) {
            Yii::$app->session->setFlash('success', 'Данные успешно удалены.');
        } else {
            Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
        }

        return $this->redirect(Url::previous());
    }

    /**
     * Мультиуправление
     */
    public function actionMultiControl()
    {
        if (!Yii::$app->user->can('agency-select-multi-control')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $redirect = Yii::$app->request->referrer ? Yii::$app->request->referrer : ['index'];
        $action = Yii::$app->request->post('action');
        $ids = Yii::$app->request->post('selection');

        if (!$ids || !is_array($ids)) {
            Yii::$app->session->setFlash('danger', 'Не выбрано ни одной действие');

            return $this->redirect($redirect);
        }

        switch ($action) {
            case 'delete':
                $deletedRows = Select::deleteAll(['in', 'apartment_select_id', $ids]);
                break;
        }

        Yii::$app->session->setFlash('success', 'Данные успешно сохранены');

        return $this->redirect($redirect);
    }

    /**
     * Finds the Select model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Select the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Select::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
