<?php

namespace common\modules\helpdesk\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use common\modules\helpdesk\models\Helpdesk;
use common\modules\helpdesk\models\form\HelpdeskForm;

/**
 * Ajax Controller
 * @package common\modules\helpdesk\controllers\frontend
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

        $this->module->viewPath = '@common/modules/helpdesk/views/frontend';

        return true;
    }

    /**
     * Отправить жалобу на владельца апартаментов
     * @param $advert_id
     * @return array|bool
     */
    public function actionComplaint($advert_id)
    {
        $model = new Helpdesk();

        if (Yii::$app->user->isGuest) {
            $formModel = new HelpdeskForm(['scenario' => 'guest-complaint']);
        } else {
            $formModel = new HelpdeskForm(['scenario' => 'user-complaint']);
        }

        $formModel->partners_advert_id = (int)$advert_id;

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $formModel->load(Yii::$app->request->getBodyParams());
            $errors = ActiveForm::validate($formModel);
            if (!$errors) {
                if ($formModel->complaint()) {
                    return [
                        'status' => 1,
                        'message' => 'Ваша заявка успешно остановлена, пожалуйста ожидайте звонка от диспетчера.'
                    ];
                }

                return [
                    'status' => 0,
                    'message' => 'Возникла критическая ошибка.'
                ];
            }

            return $errors;
        }

        return $this->renderAjax('complaint.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }
}
