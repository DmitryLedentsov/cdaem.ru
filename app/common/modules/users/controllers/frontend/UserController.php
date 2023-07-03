<?php

namespace common\modules\users\controllers\frontend;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use frontend\components\Controller;
use common\modules\users\models as models;

/**
 * Контроллер для управления пользователями
 */
class UserController extends Controller
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
                        'roles' => ['@']
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
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/users/views/frontend';

        if (Yii::$app->request->getCurrentCitySubDomain() !== null) {
            $this->redirect(Yii::$app->request->getCurrentUrlWithoutSubDomain());
        }

        return true;
    }

    /**
     * Выход
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->refresh();
    }

    /**
     * Профиль
     * @return Response
     */
    public function actionProfile(): Response
    {
        $profile = models\Profile::findByUserId(Yii::$app->user->id);
        $profile->setScenario('update');
        $post = Yii::$app->request->post();

        $clearPhone = function ($rawPhone) {
            // Убираем всё кроме цифрт из телефона, иначе он не сохдарнится в БД
            return preg_replace('/[^\d]/iu', '', $rawPhone);
        };

        if ($rawPhone = ArrayHelper::getValue($post, 'Profile.phone2')) {
            $post['Profile']['phone2'] = $clearPhone($rawPhone);
        }

        if ($rawUserPhone = ArrayHelper::getValue($post, 'User.phone')) {
            $post['User']['phone'] = $clearPhone($rawUserPhone);
        }

        $user = models\User::findOne(['id' => Yii::$app->user->id]);
        $user->setScenario('update');

        $errors = [];
        $isSuccess = true;
        $isUpdate = false;

        if ($user->load($post)) {
            $isUpdate = true;
            $errors = $this->validate($user);

            if (empty($errors)) {
                $user->save(false);
            }
            else {
                $isSuccess = false;
            }
        }

        // Проверяем только если пользователь решил поменять пароль
        $newPassword = ArrayHelper::getValue($post, "PasswordForm.password");

        if (!empty($newPassword)) {
            $isUpdate = true;
            $passwordForm = new models\frontend\PasswordForm();

            if ($passwordForm->load($post)) {
                $errors = array_merge($errors, $this->validate($passwordForm));

                if (empty($errors)) {
                    $user->setPassword($newPassword);
                    $user->save(false);
                }
                else {
                    $isSuccess = false;
                }
            }
        }

        if ($profile->load($post)) {
            $isUpdate = true;
            $errors = array_merge($errors, $this->validate($profile));
            if (empty($errors)) {
                $profile->save(false);
            }
            else {
                $isSuccess = false;
            }

        }

        // Ошибки со всех трёх моделей
        if ($errors) {
            return $this->validationErrorsAjaxResponse($errors);
        }

        if ($isUpdate && $isSuccess) {
            return $this->successAjaxResponse(Yii::t('users', 'SUCCESS_UPDATE'));
        }

        return $this->response($this->render('profile', [
            'profile' => $profile,
            'avatarPath' => $this->module->avatarUrl
        ]));
    }

    /**
     * Изменить пароль
     * @return Response
     */
    public function actionPassword(): Response
    {
        $model = new models\frontend\PasswordForm();

        if ($model->load(Yii::$app->request->post())) {
            $errors = $this->validate($model);
            if (empty($errors)) {
                $model->password();

                return $this->successAjaxResponse(Yii::t('users', 'SUCCESS_PASSWORD_CHANGE'));
            }

            return $this->validationErrorsAjaxResponse($errors);
        }

        return $this->response($this->render('password', [
            'model' => $model,
        ]));
    }

    /**
     * Изменить данные юридического лица
     * @return Response
     */
    public function actionLegalPerson(): Response
    {
        if (!Yii::$app->user->identity->profile->legal_person) {
            return $this->goBack();
        }

        if ($model = models\LegalPerson::findByUserId(Yii::$app->user->id)) {
            $model->scenario = 'create';
        } else {
            $model = new models\LegalPerson(['scenario' => 'update']);
        }

        if ($model->load(Yii::$app->request->post())) {
            $errors = $this->validate($model);
            if (empty($errors)) {
                $model->save(false);

                return $this->successAjaxResponse(Yii::t('users', 'SUCCESS_LEGAL_PERSON_UPDATE'));
            }

            return $this->validationErrorsAjaxResponse($errors);
        }

        return $this->response($this->render('legal-person', [
            'model' => $model
        ]));
    }

    /**
     * Вернуть токен пользователя, для обновления
     * @return string
     */
    public function actionGetCsrfToken() {
        $csrfToken = Yii::$app->request->getCsrfToken();
        Yii::$app->response->headers->set('X-CSRF-Token', $csrfToken);
        return $csrfToken;
    }
}
