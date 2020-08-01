<?php

namespace common\modules\articles\controllers\backend;

use common\modules\articles\models\backend\ArticleLinkSearch;
use common\modules\articles\models\backend\ArticleLinkForm;
use common\modules\articles\models\ArticleLink;
use common\modules\articles\models\Article;
use backend\components\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\helpers\Url;
use Yii;

/**
 * Default Controller
 * @package common\modules\articles\controllers\backend
 */
class ArticlelinkController extends Controller
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


    /**
     * @inheritdoc
     */


    /**
     * Все
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('articles-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        Url::remember();

        $model = new ArticleLink();
        $searchModel = new ArticleLinkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $model,
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
        if (!Yii::$app->user->can('articles-create')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = new ArticleLink();
        $modelForm = new ArticleLinkForm(['scenario' => 'create']);

        if ($modelForm->load(Yii::$app->request->post())) {
            if ($modelForm->validate()) {
                $imageName = 'article_' . time();
                $modelForm->file = UploadedFile::getInstance($modelForm, 'file');
                if (!empty($modelForm->file)) {
                    $modelForm->file->saveAs(Yii::getAlias($modelForm->imagesPath) . '/' . $imageName . '.' . $modelForm->file->extension);
                    $modelForm->thumb_img = $imageName . '.' . $modelForm->file->extension;

                }
                if ($modelForm->create()) {

                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }
                return $this->redirect(['update', 'id' => $modelForm->id]);
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($modelForm);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'formModel' => $modelForm,

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
        if (!Yii::$app->user->can('articles-view')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $model = $this->findModel($id);
        $articles = new Article();
        $formModel = new ArticleLinkForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('articles-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }
            if ($formModel->validate()) {
                $imageName = 'article_' . time();
                $formModel->file = UploadedFile::getInstance($formModel, 'file');
                if (!empty($formModel->file)) {
                    $formModel->file->saveAs(Yii::getAlias($formModel->imagesPath) . '/' . $imageName . '.' . $formModel->file->extension);
                    $formModel->thumb_img = $imageName . '.' . $formModel->file->extension;

                }
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
     * @param $id
     * @return Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('articles-delete')) {
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }
        $model = $this->findModel($id);
        $formModel = new ArticleLinkForm(['scenario' => 'delete']);
        if ($formModel->validate() && $formModel->delete($model)) {
            Yii::$app->session->setFlash('success', 'Данные успешно удалены.');
        } else {
            Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
        }
        return $this->redirect(Url::previous());
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArticleLink::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
