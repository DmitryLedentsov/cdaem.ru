<?php

namespace common\modules\helpdesk\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\modules\helpdesk\models\Helpdesk;
use common\modules\helpdesk\models\form\HelpdeskForm;

/**
 * Техническая поддержка
 * @package common\modules\helpdesk\controllers\frontend
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
                        'allow' => true,
                        'actions' => [],
                        'roles' => ['?', '@']
                    ],
                ]
            ]
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

        $this->module->viewPath = '@common/modules/helpdesk/views/frontend';

        return true;
    }

    /**
     * Задать вопрос
     *
     * @return $this|array|string|Response
     */
    public function actionIndex()
    {
        $model = new Helpdesk();
        $formModel = new HelpdeskForm(['scenario' => Yii::$app->user->isGuest ? 'guest-ask' : 'user-ask']);
        if ($formModel->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($formModel);
            if (!$errors) {
                if ($formModel->ask()) {
                    Yii::$app->session->setFlash('success', 'Обращение в техническую поддержку успешно отправлено.');
                } else {
                    Yii::$app->session->setFlash('danger', 'При отправки обращения возникла ошибка.');
                }

                return $this->refresh();
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
        }

        return $this->render('index.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    /**
     * @return array|string|\yii\console\Response|Response
     */
    public function actionWorkvac()
    {
        $model = new Helpdesk();
        $formModel = new HelpdeskForm(['scenario' => 'guest-ask-work']);
        if ($formModel->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($formModel);
            if (!$errors) {
                if ($formModel->vacant()) {
                    Yii::$app->session->setFlash('success', 'Обращение успешно отправлено.');
                } else {
                    Yii::$app->session->setFlash('danger', 'При отправки обращения возникла ошибка.');
                }

                return $this->refresh();
            } elseif (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
        }

        return $this->render('workvac.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    /**
     * @return array|string|Response
     */
    public function actionHelpphone()
    {
        $model = new Helpdesk();
        $formModel = new HelpdeskForm(['scenario' => 'guest-ask-phone']);
        if ($formModel->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($formModel);
            if (!$errors) {
                if ($formModel->phonehelp()) {
                    Yii::$app->session->setFlash('success', 'Обращение успешно отправлено. Ваша заявка будет расмотренна в течении суток.');
                } else {
                    Yii::$app->session->setFlash('danger', 'При отправки обращения возникла ошибка.');
                }

                return Yii::$app->controller->redirect(['/users/user/profile']);
            }
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
        }

        return $this->render('helpphone.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }

    /**
     * Задать вопрос
     *
     * @return $this|array|string|Response
     */
    public function actionHelp()
    {
        $model = new Helpdesk();
        $formModel = new HelpdeskForm(['scenario' => Yii::$app->user->isGuest ? 'guest-ask' : 'user-ask']);
        if ($formModel->load(Yii::$app->request->post())) {
            $errors = ActiveForm::validate($formModel);
            if (!$errors) {
                if ($formModel->help()) {
                    Yii::$app->session->setFlash('success', 'Обращение в техническую поддержку успешно отправлено.');
                } else {
                    Yii::$app->session->setFlash('danger', 'При отправки обращения возникла ошибка.');
                }

                return $this->refresh();
            }
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                return $errors;
            }
        }

        return $this->render('partners.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]);
    }
}
