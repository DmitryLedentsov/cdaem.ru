<?php

namespace common\modules\agency\controllers\backend;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\modules\agency\models\Apartment;
use common\modules\agency\models\Advertisement;
use common\modules\agency\models\backend\form\AdvertisementForm;
use common\modules\agency\models\backend\search\AdvertisementSearch;

/**
 * Advertisement Controller
 * @package common\modules\agency\controllers\backend
 */
class AdvertisementController extends Controller
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
     * Просмотр
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('agency-advertisement-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Url::remember();

        $searchModel = new AdvertisementSearch();
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
        if (!Yii::$app->user->can('agency-advertisement-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = new Advertisement();
        $formModel = new AdvertisementForm(['scenario' => 'create']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if ($formModel->validate()) {
                if ($formModel->create()) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $formModel->advertisement_id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($formModel);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'formModel' => $formModel,
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
        if (!Yii::$app->user->can('agency-advertisement-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $formModel = new AdvertisementForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('agency-advertisement-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }

            if ($formModel->validate()) {
                if ($formModel->update($model)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $formModel->advertisement_id]);
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
     * @param $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('agency-advertisement-delete')) {
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
     * @return Response
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionMultiControl()
    {
        if (!Yii::$app->user->can('agency-advertisement-multi-control')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $redirect = Yii::$app->request->referrer ? Yii::$app->request->referrer : ['index'];
        $action = Yii::$app->request->post('action');
        $ids = Yii::$app->request->post('selection');

        if (!$ids || !is_array($ids)) {
            return $this->redirect($redirect);
        }

        switch ($action) {

            case 'delete':
                $advertisements = Advertisement::findAll($ids);
                foreach ($advertisements as $advertisement) {
                    $advertisement->delete();
                }
                break;

            default:
                return $this->redirect($redirect);
        }

        return $this->redirect($redirect);
    }

    /**
     * Массовое создание
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionMultiCreate()
    {
        if (!Yii::$app->user->can('agency-advertisement-multi-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $ids = Yii::$app->request->get('ids');
        $models = Apartment::findAll($ids);

        // Все модели рекламных объявлений для создания
        $advertisements = [];
        foreach ($models as $model) {
            foreach ($model->adverts as $advert) {
                if (!$advert->advertisement) {
                    $advertisements[$model->apartment_id][$advert->advert_id] = new AdvertisementForm(['scenario' => 'create']);
                    $advertisements[$model->apartment_id][$advert->advert_id]->advert_id = $advert->advert_id;
                }
            }
        }

        $selected = [];

        if (Yii::$app->request->isPost) {
            $selected = (array)Yii::$app->request->post('selected');
            $adverts = (array)Yii::$app->request->post('AdvertisementForm');
            $countRecords = 0;
            $saveRecords = 0;

            foreach ($advertisements as $apartmentId => $advertisementsByAdverts) {
                foreach ($advertisementsByAdverts as $advertisement) {
                    if (in_array($advertisement->advert_id, array_keys($selected))) {
                        if (isset($adverts[$advertisement->advert_id])) {
                            ++$countRecords;
                            $advertisement->load($adverts[$advertisement->advert_id], '');
                            if ($advertisement->validate() && $advertisement->create()) {
                                ++$saveRecords;
                                unset($advertisements[$apartmentId][$advertisement->advert_id]);
                            }
                        }
                    }
                }
            }

            Yii::$app->session->setFlash('info', "Сохранено записей: {$saveRecords}  из {$countRecords}");
        }

        return $this->render('multi-create', [
            'models' => $models,
            'advertisements' => $advertisements,
            'selected' => $selected,
        ]);
    }

    /**
     * Finds the Advertisement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Advertisement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Advertisement::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
