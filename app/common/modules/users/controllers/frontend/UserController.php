<?php

namespace common\modules\users\controllers\frontend;

use Yii;
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

        if ($profile->load(Yii::$app->request->post())) {
            $errors = $this->validate($profile);
            if (empty($errors)) {
                $profile->save(false);

                return $this->successAjaxResponse(Yii::t('users', 'SUCCESS_UPDATE'));
            }

            // TODO:
            // dd(self::class, Yii::$app->request->post());

            return $this->validationErrorsAjaxResponse($errors);
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
}
