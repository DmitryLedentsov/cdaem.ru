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
use common\modules\agency\models\SpecialAdvert;
use common\modules\agency\models\backend\form\SpecialAdvertForm;
use common\modules\agency\models\backend\search\SpecialAdvertSearch;

/**
 * Specials Controller
 * @package common\modules\agency\controllers\backend
 */
class SpecialsController extends Controller
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
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/agency/views/backend';

        return true;
    }

    /**
     * Все
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('agency-special-advert-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Url::remember();

        $searchModel = new SpecialAdvertSearch();
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
        if (!Yii::$app->user->can('agency-special-advert-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = new SpecialAdvert();
        $formModel = new SpecialAdvertForm(['scenario' => 'create']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if ($formModel->validate()) {
                if ($formModel->create()) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $formModel->special_id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($formModel);
            }
        }

        return $this->render('create', [
            'formModel' => $formModel,
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
        if (!Yii::$app->user->can('agency-special-advert-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $formModel = new SpecialAdvertForm(['scenario' => 'create']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('agency-special-advert-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }

            if ($formModel->validate()) {
                if ($formModel->update($model)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $formModel->special_id]);
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
        if (!Yii::$app->user->can('agency-special-advert-delete')) {
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
        if (!Yii::$app->user->can('agency-special-advert-multi-control')) {
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
                $specialAdverts = SpecialAdvert::findAll($ids);
                foreach ($specialAdverts as $specialAdvert) {
                    $specialAdvert->delete();
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
        if (!Yii::$app->user->can('agency-special-advert-multi-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $ids = Yii::$app->request->get('ids');
        $models = Apartment::findAll($ids);

        // Все модели рекламных объявлений для создания
        $specialAdverts = [];
        foreach ($models as $model) {
            foreach ($model->adverts as $advert) {
                if (!$advert->special) {
                    $specialAdverts[$model->apartment_id][$advert->advert_id] = new SpecialAdvertForm(['scenario' => 'create']);
                    $specialAdverts[$model->apartment_id][$advert->advert_id]->advert_id = $advert->advert_id;
                }
            }
        }

        $selected = [];

        if (Yii::$app->request->isPost) {
            $selected = (array)Yii::$app->request->post('selected');
            $adverts = (array)Yii::$app->request->post('SpecialAdvertForm');
            $countRecords = 0;
            $saveRecords = 0;

            foreach ($specialAdverts as $apartmentId => $specialAdvertsByAdverts) {
                foreach ($specialAdvertsByAdverts as $specialAdvert) {
                    if (in_array($specialAdvert->advert_id, array_keys($selected))) {
                        if (isset($adverts[$specialAdvert->advert_id])) {
                            ++$countRecords;
                            $specialAdvert->load($adverts[$specialAdvert->advert_id], '');
                            if ($specialAdvert->validate() && $specialAdvert->create()) {
                                ++$saveRecords;
                                unset($specialAdverts[$apartmentId][$specialAdvert->advert_id]);
                            }
                        }
                    }
                }
            }

            Yii::$app->session->setFlash('info', "Сохранено записей: {$saveRecords}  из {$countRecords}");
        }

        return $this->render('multi-create', [
            'models' => $models,
            'specialAdverts' => $specialAdverts,
            'selected' => $selected,
        ]);
    }

    /**
     * Finds the SpecialAdverts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SpecialAdvert the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SpecialAdvert::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
