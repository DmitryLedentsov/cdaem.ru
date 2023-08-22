<?php

namespace common\modules\callback\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\modules\callback\models\CallbackForm;

/**
 * Class DefaultController
 * @package common\modules\callback\controllers\frontend
 */
class DefaultController extends \frontend\components\Controller
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
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
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

        $this->module->viewPath = '@common/modules/callback/views/frontend';

        return true;
    }

    /**
     * Обратный звонок
     * @return array|Response
     */
    public function actionIndex()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new CallbackForm(['scenario' => 'create']);
        if ($model->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($model);
            if (!$errors) {
                if ($model->create()) {
                    return [
                        'status' => 1,
                        'message' => 'Ваша заявка успешно отправлена, пожалуйста ожидайте звонка.'
                    ];
                }

                return [
                    'status' => 0,
                    'message' => 'Возникла критическая ошибка'
                ];
            }

            return $errors;
        }

        return [
            'status' => 0,
            'message' => 'Возникла критическая ошибка'
        ];
    }
}
