<?php

namespace common\modules\reviews\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use common\modules\reviews\models\ReviewForm;

/**
 * Ajax Controller
 * common\modules\reviews\controllers\frontend
 */
class AjaxController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ]
                ],
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

        if (!Yii::$app->request->isAjax) {
            return $this->goHome();
        }

        $this->module->viewPath = '@common/modules/reviews/views/frontend';

        return true;
    }

    /**
     * Создать отзыв о апартаментах
     * @param $apartment_id
     * @return array|bool
     */
    public function actionCreate($apartment_id)
    {
        $model = new ReviewForm();
        $model->scenario = Yii::$app->user->isGuest ? 'guest-create' : 'user-create';
        $model->apartment_id = (int)$apartment_id;

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model->load(Yii::$app->request->getBodyParams());

            $errors = ActiveForm::validate($model);

            if (!$errors) {
                if ($model->save(false)) {
                    return [
                        'status' => 1,
                        'message' => 'Ваш отзыв добавлен в базу данных и ожидает модерации.'
                    ];
                }

                return [
                    'status' => 0,
                    'message' => 'Возникла критическая ошибка. Пожалуйста обратитесь в техническую поддержку.'
                ];
            }

            return $errors;
        }

        return $this->renderAjax('_form.twig', [
            'model' => $model,
        ]);
    }
}
