<?php

namespace common\modules\users\controllers\frontend;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use frontend\components\Controller;
use common\modules\users\models as models;

/**
 * @package common\modules\users\controllers\frontend
 */
class GuestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@']
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            $this->module->viewPath = '@common/modules/users/views/frontend';
            if (!Yii::$app->user->isGuest) {
                $this->goBack();
            }

            return true;
        }

        return false;
    }

    /**
     * Авторизация
     * @return Response
     */
    public function actionLogin(): Response
    {
        $formModel = new models\LoginForm(['scenario' => 'user']);

        if ($formModel->load(Yii::$app->request->post())) {
            $errors = $this->validate($formModel);
            if (empty($errors)) {
                $formModel->login();

                return $this->redirect(['/office/default/index']);
            }

            return $this->response($this->validationErrorsAjaxResponse($errors));
        }

        return $this->response($this->render('login', [
            'model' => $formModel,
        ]));
    }

    /**
     * Регистрация
     * @return Response
     */
    public function actionSignup(): Response
    {
        $user = new models\User(['scenario' => 'signup']);
        $profile = new models\Profile(['scenario' => 'create']);

        if ($user->load(Yii::$app->request->post()) && $profile->load(Yii::$app->request->post())) {
            $errors = array_merge($this->validate($user), $this->validate($profile));
            if (empty($errors)) {
                $user->populateRelation('profile', $profile);
                $user->save(false);
                if ($this->module->requireEmailConfirmation === true) {
                    Yii::$app->consoleRunner->run('users/control/send-email ' . $user->email . ' signup "' . Yii::t('users', 'SUBJECT_SIGNUP') . '"');
                    Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_SIGNUP_WITHOUT_LOGIN', [
                        'url' => Url::toRoute('resend')
                    ]));
                } else {
                    Yii::$app->user->login($user);
                    Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_SIGNUP_WITH_LOGIN'));
                }

                return $this->redirect(['login']);
            }

            return $this->response($this->validationErrorsAjaxResponse($errors));
        }

        return $this->response($this->render('signup', [
            'user' => $user,
            'profile' => $profile,
        ]));
    }

    /**
     * Повторная отправка ключа активации
     * @return Response
     */
    public function actionResend(): Response
    {
        $formModel = new models\frontend\ResendForm();

        if ($formModel->load(Yii::$app->request->post())) {
            $errors = $this->validate($formModel);
            if (empty($errors)) {
                if ($this->module->requireEmailConfirmation === true) {
                    $user = $formModel->resend();
                    Yii::$app->consoleRunner->run('users/control/send-email ' . $user->email . ' signup "' . Yii::t('users', 'SUBJECT_SIGNUP') . '"');
                    Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_RESEND'));

                    return $this->redirect(['login']);
                }
            }

            return $this->response($this->validationErrorsAjaxResponse($errors));
        }

        return $this->response($this->render('resend', [
            'model' => $formModel,
        ]));
    }

    /**
     * Активация
     * @param string $token
     * @return Response
     */
    public function actionActivation(string $token): Response
    {
        $model = new models\frontend\ActivationForm(['secure_key' => $token]);

        if ($model->validate() && $model->activation()) {
            Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_ACTIVATION'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('users', 'FAIL_ACTIVATION'));
        }

        return $this->redirect(['login']);
    }

    /**
     * Восстановить пароль
     * @return Response
     */
    public function actionRecovery(): Response
    {
        $formModel = new models\frontend\RecoveryForm();

        if ($formModel->load(Yii::$app->request->post())) {
            $errors = $this->validate($formModel);
            if (empty($errors)) {
                $user = $formModel->recovery();
                Yii::$app->consoleRunner->run('users/control/send-email ' . $user->email . ' recovery "' . Yii::t('users', 'SUBJECT_RECOVERY') . '"');
                Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_RECOVERY'));

                return $this->redirect(['login']);
            }

            return $this->response($this->validationErrorsAjaxResponse($errors));
        }

        return $this->response($this->render('recovery', [
            'model' => $formModel,
        ]));
    }

    /**
     * Подтверждение восстановления пароля
     * @param string $token
     * @return Response
     */
    public function actionRecoveryConfirmation(string $token): Response
    {
        $formModel = new models\frontend\RecoveryConfirmationForm(['secure_key' => $token]);

        if ($formModel->isValidToken() === false) {
            Yii::$app->session->setFlash('danger', Yii::t('users', 'FAIL_RECOVERY_CONFIRMATION_WITH_INVALID_KEY'));

            return $this->redirect(['recovery']);
        }

        if ($formModel->load(Yii::$app->request->post())) {
            $errors = $this->validate($formModel);
            if (empty($errors)) {
                $formModel->recovery();
                Yii::$app->session->setFlash('success', Yii::t('users', 'SUCCESS_RECOVERY_CONFIRMATION'));

                return $this->redirect(['login']);
            }

            return $this->response($this->validationErrorsAjaxResponse($errors));
        }

        return $this->response($this->render('recovery-confirmation', [
            'model' => $formModel,
        ]));
    }
}
