<?php

namespace backend\modules\partners\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use backend\modules\partners\models\Image;
use backend\modules\partners\models\Advert;
use backend\modules\partners\models\Apartment;
use backend\modules\partners\models\ApartmentForm;
use backend\modules\partners\models\ApartmentSearch;

/**
 * DefaultController
 * @package backend\modules\partners\controllers
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
     * Все
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('partners-apartment-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Url::remember();

        $searchModel = new ApartmentSearch();
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
        if (!Yii::$app->user->can('partners-apartment-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = new ApartmentForm(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $model->apartment_id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }
        }

        return $this->render('create', [
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
        if (!Yii::$app->user->can('partners-apartment-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        if (($model = ApartmentForm::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model->scenario = 'update';
        $advertsQuery = Advert::find()->where(['apartment_id' => $id]);
        $adverts = $advertsQuery;
        $advertsDataProvider = new ActiveDataProvider([
            'query' => $advertsQuery,
            'pagination' => false,
            'sort' => false,
        ]);

        if ($model->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('partners-apartment-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }

            if ($model->validate()) {
                if ($model->save(false)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $model->apartment_id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'advertsDataProvider' => $advertsDataProvider,
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
        if (!Yii::$app->user->can('partners-apartment-delete')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $this->findModel($id)->delete();

        return $this->redirect(Url::previous());
    }

    /**
     * Finds the Apartments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Apartment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Apartment::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Назначение заглавного изображения
     * @param $id
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionDefaultImage($id)
    {
        if (!Yii::$app->user->can('partners-apartment-update')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = [
            'action' => 'defaultImage',
            'id' => $id
        ];

        $newDefaultImg = Image::findOne($id);

        if (!$newDefaultImg) {
            $response['result'] = false;

            return $response;
        }

        $oldDefaultImg = Image::find()->where([
            'apartment_id' => $newDefaultImg->apartment_id,
            'default_img' => 1,
        ])->one();

        if ($newDefaultImg == $oldDefaultImg) {
            $response['result'] = true;

            return $response;
        }

        $newDefaultImg->default_img = 1;
        $newDefaultImg->save();

        if ($oldDefaultImg) {
            $oldDefaultImg->default_img = 0;
            $oldDefaultImg->save();
        }

        $response['result'] = true;

        return $response;
    }

    /**
     * Удаление изображения
     * @param $id
     * @return array
     * @throws ForbiddenHttpException
     */
    public function actionDeleteImage($id)
    {
        if (!Yii::$app->user->can('partners-apartment-update')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $image = Image::findOne($id);
        $result = false;

        if ($image) {
            $apartment_id = $image->apartment_id;
            $result = $image->deleteWithFiles();
            if ($result) {
                $newDefaultImg = Image::find()->where(['apartment_id' => $apartment_id, 'default_img' => 1])->select('image_id')->scalar();
            }
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'action' => 'deleteImage',
            'result' => $result,
            'id' => $id,
            'newDefaultImg' => isset($newDefaultImg) ? $newDefaultImg : null,
        ];
    }

    /**
     * Мультиуправление
     * @return Response
     * @throws ForbiddenHttpException
     */
    public function actionMultiControl()
    {
        if (!Yii::$app->user->can('partners-apartment-multi-control')) {
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
            case 'moderated':
                $updatedRows = Apartment::updateAll(['status' => 1, 'date_update' => date('Y-m-d H:i:s')], ['in', 'apartment_id', $ids]);
                break;
            case 'unmoderated':
                $updatedRows = Apartment::updateAll(['status' => 0, 'date_update' => date('Y-m-d H:i:s')], ['in', 'apartment_id', $ids]);
                break;
            case 'blocked':
                $updatedRows = Apartment::updateAll(['status' => 2, 'date_update' => date('Y-m-d H:i:s')], ['in', 'apartment_id', $ids]);
                break;
            case 'delete':
                $deletedImages = Image::deleteAllWithFiles(['in', 'apartment_id', $ids]);
                $deletedRows = Apartment::deleteAll(['in', 'apartment_id', $ids]);
                break;
        }

        Yii::$app->session->setFlash('success', 'Данные успешно сохранены');

        return $this->redirect($redirect);
    }

    /**
     * Сортировка изображений
     * @return array|Response
     * @throws ForbiddenHttpException
     */
    public function actionSortImages()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        if (!Yii::$app->user->can('partners-apartment-update')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $sort = (array)Yii::$app->request->post('sort');
        foreach ($sort as $key => $imageId) {
            $image = Image::findOne($imageId);
            if ($image) {
                $image->sort = $key;
                $image->save(false);
            }
        }

        return [];
    }
}
