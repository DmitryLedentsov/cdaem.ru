<?php

namespace common\modules\agency\controllers\backend;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\modules\agency\models\Advert;
use common\modules\realty\models\RentType;
use common\modules\agency\models\Apartment;
use common\modules\agency\models\backend\form\AdvertForm;

/**
 * Adverts Controller
 * @package common\modules\agency\controllers\backend
 */
class AdvertsController extends Controller
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
     * Создать объявление
     * @param $id
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreate($id)
    {
        if (!Yii::$app->user->can('agency-advert-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = new Advert();
        $formModel = new AdvertForm(['scenario' => 'create']);
        $formModel->apartment_id = $id;
        $apartment = Apartment::findOne($id);
        if (!$apartment) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($formModel->load(Yii::$app->request->post())) {
            if ($formModel->validate()) {
                if ($formModel->create()) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }
                //return $this->redirect(['/agency/default/update', 'id' => $id]);

                return $this->redirect(['/agency/default/index']);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return ActiveForm::validate($model);
            }
        }

        return $this->render('create', [
            'formModel' => $formModel,
            'apartment' => $apartment,
            'model' => $model,
        ]);
    }

    /**
     * Редактировать объявление
     * @param $id
     * @return array|string|Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('agency-advert-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $formModel = new AdvertForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('agency-advert-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }

            if ($formModel->validate()) {
                if ($formModel->update($model)) {
                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }

                return $this->redirect(['update', 'id' => $formModel->advert_id]);
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
     * Удалить объявление
     * @param $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('agency-advert-delete')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $formModel = new AdvertForm(['scenario' => 'delete']);
        //Юрл редактирования адверта, нужен для того, чтобы не перенаправить после удаления на несуществующий адверт
        $update_url = Url::toRoute(['adverts/update', 'id' => $id], true);

        $apartment_id = $model->apartment_id;

        $formModel->delete($model);

        if (Yii::$app->request->referrer == $update_url) {
            return $this->redirect(['/agency/default/update', 'id' => $apartment_id]);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the ApartmentAdverts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Advert the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Advert::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Массовое создание
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionMultiUpdate()
    {
        if (!Yii::$app->user->can('agency-advert-multi-update')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $ids = Yii::$app->request->get('ids');
        $models = Apartment::findAll($ids);

        $rentTypesList = RentType::rentTypeslist();

        if (Yii::$app->request->isPost) {
            $activeRentTypesList = (array)Yii::$app->request->post('rent-types-list');
            $adverts = (array)Yii::$app->request->post('Advert');
            $countRecords = 0;
            $saveRecords = 0;

            if ($adverts) {
                foreach ($adverts as $advertId => $advert) {
                    $currentAdvert = Advert::findOne($advertId);
                    if ($currentAdvert) {
                        $currentAdvert->scenario = 'update';

                        // Пропускаем все объявления с типом аренды, который не числится в списке активных
                        if (!in_array($currentAdvert->rent_type, $activeRentTypesList)) {
                            continue;
                        }

                        ++$countRecords;

                        $currentAdvert->load($advert, '');

                        if ($currentAdvert->save()) {
                            ++$saveRecords;
                        }
                    }
                }
            }

            Apartment::updateAll(['date_update' => date('Y-m-d H:i:s')], ['apartment_id' => $ids]);

            Yii::$app->session->setFlash('info', "Сохранено записей: {$saveRecords}  из {$countRecords}");
        }


        return $this->render('multi-update', [
            'models' => $models,
            'rentTypesList' => $rentTypesList,
        ]);
    }
}
