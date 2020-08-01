<?php

namespace common\modules\articles\controllers\backend;

use common\modules\articles\models\backend\ArticleSearch;
use common\modules\articles\models\backend\ArticleForm;
use common\modules\articles\models\backend\ArticleForm2;
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

        $this->module->viewPath = '@common/modules/articles/views/backend';

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
                'url' => Yii::$app->params['siteDomain'] . $this->module->imageUrl,
                'path' => $this->module->imagePath,
            ],
        ];
    }

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

        $model = new Article();
        $searchModel = new ArticleSearch();
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

        $model = new Article();
        $modelForm = new ArticleForm(['scenario' => 'create']);

        if ($modelForm->load(Yii::$app->request->post())) {
            if ($modelForm->validate()) {
                $imageName = time();
                $imageNameBg = 'bg' . time();
                $modelForm->file = UploadedFile::getInstance($modelForm, 'file');

                if (!empty($modelForm->file)) {
                    $modelForm->file->saveAs(Yii::getAlias($modelForm->imagesPath) . '/' . $imageName . '.' . $modelForm->file->extension);
                    $modelForm->title_img = $imageName . '.' . $modelForm->file->extension;

                }
                $modelForm->bgfile = UploadedFile::getInstance($modelForm, 'bgfile');
                if (!empty($modelForm->bgfile)) {
                    $modelForm->bgfile->saveAs(Yii::getAlias($modelForm->imagesPath2) . '/' . $imageNameBg . '.' . $modelForm->bgfile->extension);
                    $modelForm->background = $imageNameBg . '.' . $modelForm->bgfile->extension;

                }
                if ($modelForm->create()) {

                    Yii::$app->session->setFlash('success', 'Данные успешно сохранены.');
                } else {
                    Yii::$app->session->setFlash('danger', 'Возникла ошибка.');
                }
                return $this->redirect(['update', 'id' => $modelForm->article_id]);
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

        $formModel = new ArticleForm(['scenario' => 'update']);
        $formModel->setAttributes($model->getAttributes(), false);

        if ($formModel->load(Yii::$app->request->post())) {
            if (!Yii::$app->user->can('articles-update')) {
                throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
            }
            if ($formModel->validate()) {
                $imageName = time();
                $imageNameBg = 'bg' . time();
                $formModel->file = UploadedFile::getInstance($formModel, 'file');
                if (!empty($formModel->file)) {
                    $formModel->file->saveAs(Yii::getAlias($formModel->imagesPath) . '/' . $imageName . '.' . $formModel->file->extension);
                    $formModel->title_img = $imageName . '.' . $formModel->file->extension;

                }
                $formModel->bgfile = UploadedFile::getInstance($formModel, 'bgfile');
                if (!empty($formModel->bgfile)) {
                    $formModel->bgfile->saveAs(Yii::getAlias($formModel->imagesPath) . '/' . $imageNameBg . '.' . $formModel->bgfile->extension);
                    $formModel->background = $imageNameBg . '.' . $formModel->bgfile->extension;

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
        $formModel = new ArticleForm(['scenario' => 'delete']);
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
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
