<?php

namespace common\modules\agency\controllers\backend;

use common\modules\agency\models\backend\search\DetailsHistorySearch;
use common\modules\agency\models\backend\form\DetailsHistoryForm;
use common\modules\agency\models\DetailsHistory;
use backend\components\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\helpers\Url;
use Yii;

/**
 * Class DetailsController
 * @package backend\modules\agency\controllers
 */
class DetailsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
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
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/agency/views/backend';

        return true;
    }

    /**
     * Просмотр заявок
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('agency-details-history-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Url::remember();

        $searchModel = new DetailsHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Редактировать заявку
     * @param $id
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('agency-details-history-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $formModel = new DetailsHistoryForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('agency-details-history-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }
            if ($formModel->validate()) {
                if ($formModel->update($model)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }
                return $this->redirect(['update', 'id' => $formModel->id]);
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
        if (!Yii::$app->user->can('agency-details-history-delete')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $this->findModel($id)->delete();

        return $this->redirect(Url::previous());
    }

    /**
     * Мультиуправление
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionMultiControl()
    {
        if (!Yii::$app->user->can('agency-details-history-send')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }
        $redirect = Yii::$app->request->referrer ? Yii::$app->request->referrer : ['index'];
        $action = Yii::$app->request->post('action');
        $ids = Yii::$app->request->post('selection');

        if (!$ids || !is_array($ids)) {
            return $this->redirect($redirect);
        }
        $count = 0;
        foreach ($ids as $id) {
            if ($detail = DetailsHistory::findOne($id)) {
                $count++;
                if (!$detail->processed) {
                    $detail->processed = 1;
                    $detail->save(false);
                }
                Yii::$app->consoleRunner->run('agency/details/send-email ' . $detail->id);
            }
        }

        Yii::$app->session->setFlash('success', 'Данные были отправлены. Кол-во  EMAIL: ' . $count);
        return $this->redirect($redirect);
    }

    /**
     * Редактирование файлов "Реквизиты"
     * @return string
     */
    public function actionUpdateFiles()
    {
        if (Yii::$app->request->isPost) {
            $success = [];
            $success[] = @file_put_contents(Yii::getAlias('@common/modules/agency/details/alfabank.txt'), Yii::$app->request->post('alfabank'));
            $success[] = @file_put_contents(Yii::getAlias('@common/modules/agency/details/legal.txt'), Yii::$app->request->post('legal'));
            $success[] = @file_put_contents(Yii::getAlias('@common/modules/agency/details/phone.txt'), Yii::$app->request->post('phone'));
            $success[] = @file_put_contents(Yii::getAlias('@common/modules/agency/details/qiwi.txt'), Yii::$app->request->post('qiwi'));
            $success[] = @file_put_contents(Yii::getAlias('@common/modules/agency/details/sberbank.txt'), Yii::$app->request->post('sberbank'));
            $success[] = @file_put_contents(Yii::getAlias('@common/modules/agency/details/yamoney.txt'), Yii::$app->request->post('yamoney'));

            if (in_array(false, $success)) {
                Yii::$app->session->setFlash('danger', 'Возникла ошибка при сохранении');
            } else {
                Yii::$app->session->setFlash('success', 'Данные успешно сохранены');
            }
        }

        $model = [
            'alfabank' => @file_get_contents(Yii::getAlias('@common/modules/agency/details/alfabank.txt')),
            'legal' => @file_get_contents(Yii::getAlias('@common/modules/agency/details/legal.txt')),
            'phone' => @file_get_contents(Yii::getAlias('@common/modules/agency/details/phone.txt')),
            'qiwi' => @file_get_contents(Yii::getAlias('@common/modules/agency/details/qiwi.txt')),
            'sberbank' => @file_get_contents(Yii::getAlias('@common/modules/agency/details/sberbank.txt')),
            'yamoney' => @file_get_contents(Yii::getAlias('@common/modules/agency/details/yamoney.txt'))
        ];

        return $this->render('update-details', [
            'model' => $model
        ]);
    }

    /**
     * Finds the DetailsHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DetailsHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DetailsHistory::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}