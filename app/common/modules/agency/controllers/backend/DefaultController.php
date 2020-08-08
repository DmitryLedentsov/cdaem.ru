<?php

namespace common\modules\agency\controllers\backend;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\modules\agency\models\Image;
use common\modules\agency\models\Advert;
use common\modules\agency\models\Apartment;
use common\modules\agency\models\backend\form\ImageForm;
use common\modules\agency\models\backend\form\ApartmentForm;
use common\modules\agency\models\backend\search\ApartmentSearch;

/**
 * Главный контроллер агенства
 * @package backend\modules\agency\controllers
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => Yii::$app->params['siteDomain'] . '.' . $this->module->imageUrl,
                'path' => $this->module->imagesPath,
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
        if (!Yii::$app->user->can('agency-apartment-view')) {
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
        if (!Yii::$app->user->can('agency-apartment-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = new Apartment();
        $formModel = new ApartmentForm(['scenario' => 'create']);
        if ($formModel->load(Yii::$app->request->post())) {
            if ($formModel->validate()) {
                if ($formModel->create()) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $formModel->apartment_id]);
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
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if (($model = Apartment::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if (!Yii::$app->user->can('agency-apartment-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $advertsQuery = Advert::find()->where(['apartment_id' => $id]);
        $advertsDataProvider = new ActiveDataProvider([
            'query' => $advertsQuery,
            'pagination' => false,
            'sort' => false,
        ]);

        $formModel = new ApartmentForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);
        $formModel->metroStations = $model->metroStations;

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('agency-apartment-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }
            if ($formModel->validate()) {
                if ($formModel->update($model)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $formModel->apartment_id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($formModel);
            }
        }

        return $this->render('update', [
            'formModel' => $formModel,
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
        if (!Yii::$app->user->can('agency-apartment-delete')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $this->findModel($id)->delete();

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
        if (!Yii::$app->user->can('agency-apartment-multi-control')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $redirect = Yii::$app->request->referrer ? Yii::$app->request->referrer : ['index'];
        $action = Yii::$app->request->post('action');
        $ids = Yii::$app->request->post('selection');

        if (!$ids || !is_array($ids)) {
            return $this->redirect($redirect);
        }

        switch ($action) {

            case 'visible':
                $updatedRows = Apartment::updateAll(['visible' => 1, 'date_update' => date('Y-m-d H:i:s')], ['in', 'apartment_id', $ids]);
                Yii::$app->user->action(
                    Yii::$app->user->id,
                    $this->module->id,
                    'update-agency-apartment-all',
                    [
                        ['visible' => 1],
                        ['in', 'apartment_id', $ids]
                    ]
                );
                break;

            case 'invisible':
                $updatedRows = Apartment::updateAll(['visible' => 0, 'date_update' => date('Y-m-d H:i:s')], ['in', 'apartment_id', $ids]);
                Yii::$app->user->action(
                    Yii::$app->user->id,
                    $this->module->id,
                    'update-agency-apartment-all',
                    [
                        ['visible' => 0],
                        ['in', 'apartment_id', $ids]
                    ]
                );
                break;

            case 'delete':
                $deletedImages = Image::deleteAllWithFiles(['in', 'apartment_id', $ids]);
                $apartments = Apartment::findAll($ids);
                $deletedRows = 0;
                foreach ($apartments as $apartment) {
                    $deletedRows += $apartment->delete();
                }
                break;

            case 'specials':
                return $this->redirect(['/agency/specials/multi-create', 'ids' => $ids]);
                break;

            case 'advertisement':
                return $this->redirect(['/agency/advertisement/multi-create', 'ids' => $ids]);
                break;

            case 'adverts':
                return $this->redirect(['/agency/adverts/multi-update', 'ids' => $ids]);
                break;

            default:
                return $this->redirect($redirect);
        }

        return $this->redirect($redirect);
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
     * @return array|Response
     * @throws ForbiddenHttpException
     */
    public function actionDefaultImage($id)
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        if (!Yii::$app->user->can('agency-apartment-update')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

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

        if ($newDefaultImg->image_id == $oldDefaultImg->image_id) {
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
     * @return array|Response
     * @throws ForbiddenHttpException
     */
    public function actionDeleteImage($id)
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        if (!Yii::$app->user->can('agency-apartment-update')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $image = Image::findOne($id);
        $result = false;

        if ($image) {
            $apartment_id = $image->apartment_id;
            $result = $image->deleteWithFiles();
            if ($result) {
                $apartment = Apartment::findOne($apartment_id);
                $apartment->date_update = date('Y-m-d H:i:s');
                $apartment->save(false);

                $newDefaultImg = Image::find()->where(['apartment_id' => $apartment_id, 'default_img' => 1])->select('image_id')->scalar();
            }
        }

        return [
            'action' => 'deleteImage',
            'result' => $result,
            'id' => $id,
            'newDefaultImg' => isset($newDefaultImg) ? $newDefaultImg : null,
        ];
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

        if (!Yii::$app->user->can('agency-apartment-update')) {
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

        $apartment = Apartment::findOne($image->apartment_id);
        $apartment->date_update = date('Y-m-d H:i:s');
        $apartment->save(false);

        return [];
    }

    /**
     * Редактирование мета тегов картинки
     * @param $id
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdateImage($id)
    {
        if (!Yii::$app->user->can('agency-apartment-update')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = Image::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Такой картинки не существует в базе.');
        }
        $formModel = new ImageForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if ($formModel->validate()) {
                $result = $formModel->update($model);

                if (Yii::$app->request->isAjax) {
                    if ($result) {
                        $result = ['status' => 1];
                    } else {
                        $result = ['status' => 0];
                    }
                    Yii::$app->response->format = Response::FORMAT_JSON;

                    return $result;
                }

                if ($result) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update-image', 'id' => $formModel->image_id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($formModel);
            }
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_image_form', [
                'formModel' => $formModel,
                'model' => $model,
            ]);
        }

        return $this->render('_image_form', [
            'formModel' => $formModel,
            'model' => $model,
        ]);
    }
}
