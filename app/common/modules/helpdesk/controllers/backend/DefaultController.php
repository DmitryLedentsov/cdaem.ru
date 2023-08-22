<?php

namespace common\modules\helpdesk\controllers\backend;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\modules\helpdesk\models\Helpdesk;
use common\modules\helpdesk\models\backend\AnswerForm;
use common\modules\helpdesk\models\backend\HelpdeskForm;
use common\modules\helpdesk\models\backend\HelpdeskSearch;

/**
 * Default Controller
 * @package backend\modules\helpdesk\controllers
 */
class DefaultController extends \backend\components\Controller
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

        $this->module->viewPath = '@common/modules/helpdesk/views/backend';

        return true;
    }

    /**
     * Все обращения
     *
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('helpdesk-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Url::remember();

        $model = new Helpdesk();
        $searchModel = new HelpdeskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр
     *
     * @param $id
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('helpdesk-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);

        $formModel = new AnswerForm(['scenario' => 'update']);
        $formModel->ticket_id = $id;

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('helpdesk-answer')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }

            if ($formModel->validate()) {
                if ($formModel->create()) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->refresh();
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($formModel);
            }
        }

        return $this->render('view', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    /**
     * Редактирование
     *
     * @param $id
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('helpdesk-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);

        $formModel = new HelpdeskForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('helpdesk-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }
            if ($formModel->validate()) {
                if ($formModel->update($model)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->refresh();
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
     * Удалить
     *
     * @param $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('helpdesk-delete')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $formModel = new HelpdeskForm(['scenario' => 'delete']);

        if ($formModel->validate() && $formModel->delete($model)) {
            Yii::$app->session->setFlash('success', 'Данные успешно удалены.');
        } else {
            Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
        }

        return $this->redirect(Url::previous());
    }

    /**
     * Мультиуправление
     *
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionMultiControl()
    {
        if (!Yii::$app->user->can('helpdesk-multi-control')) {
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
                $deletedRows = Helpdesk::deleteAll(['in', 'ticket_id', $ids]);
                break;
        }

        Yii::$app->session->setFlash('success', 'Данные успешно сохранены');

        return $this->redirect($redirect);
    }

    /**
     * @param $id
     * @return Helpdesk
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Helpdesk::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страница не найдена.');
    }
}
