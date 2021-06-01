<?php

namespace common\modules\helpdesk\controllers\frontend;

use Yii;
use yii\web\Response;
use common\modules\pages\models\Page;
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
    public function behaviors(): array
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
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (Yii::$app->request->getCurrentCitySubDomain() !== null) {
            $this->redirect(Yii::$app->request->getCurrentUrlWithoutSubDomain());
        }

        $this->module->viewPath = '@common/modules/helpdesk/views/frontend';

        return true;
    }

    /**
     * Задать вопрос
     *
     * --- было удалено из-за полного дубля с actionHelp
     *
     * @return Response
     */
    /*public function actionIndex(): Response
    {
        $model = new Helpdesk();
        $formModel = new HelpdeskForm(['scenario' => Yii::$app->user->isGuest ? 'guest-ask' : 'user-ask']);

        if ($formModel->load(Yii::$app->request->post())) {
            $errors = $this->validate($formModel);
            if (empty($errors)) {
                $formModel->ask();
                Yii::$app->session->setFlash('success', 'Обращение в техническую поддержку успешно отправлено.');
                return $this->refresh();
            }

            return $this->validationErrorsAjaxResponse($errors);
        }

        return $this->response($this->render('index.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]));
    }*/

    /**
     * Страница "Вакансии"
     *
     * @return Response
     */
    public function actionVacancy(): Response
    {
        $model = new Helpdesk();
        $formModel = new HelpdeskForm(['scenario' => 'guest-ask-work']);

        if ($formModel->load(Yii::$app->request->post())) {
            $errors = $this->validate($formModel);
            if (empty($errors)) {
                $formModel->vacancy();

                return $this->successAjaxResponse('Спасибо, ваше обращение успешно отправлено, мы свяжемся с вами в ближайшее время.');
            }

            return $this->validationErrorsAjaxResponse($errors);
        }

        return $this->response($this->render('vacancy.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]));
    }

    /**
     * Страница "Помощь"
     *
     * @param string|null $url
     * @return Response
     * @throws \yii\web\HttpException
     */
    public function actionHelp(?string $url = null): Response
    {
        $page = null;

        if ($url) {
            $page = Page::getPageByUrl($url);
        }

        if ($page) {
            return $this->response($this->render('help_page.twig', [
                'page' => $page
            ]));
        }

        $formModel = new HelpdeskForm(['scenario' => Yii::$app->user->isGuest ? 'guest-ask' : 'user-ask']);

        if ($formModel->load(Yii::$app->request->post())) {
            $errors = $this->validate($formModel);
            if (empty($errors)) {
                $formModel->help();

                return $this->successAjaxResponse('Спасибо, ваше обращение успешно отправлено, мы свяжемся с вами в ближайшее время.');
            }

            return $this->validationErrorsAjaxResponse($errors);
        }

        return $this->response($this->render('help.twig', [
            'model' => new Helpdesk(),
            'formModel' => $formModel,
        ]));
    }

    /**
     * @return Response
     */
    public function actionHelpphone(): Response
    {
        $model = new Helpdesk();
        $formModel = new HelpdeskForm(['scenario' => 'guest-ask-phone']);

        if ($formModel->load(Yii::$app->request->post())) {
            $errors = $this->validate($formModel);
            if (empty($errors)) {
                $formModel->phonehelp();
                Yii::$app->session->setFlash('success', 'Обращение успешно отправлено. Ваша заявка будет расмотренна в течении суток.');

                return Yii::$app->controller->redirect(['/users/user/profile']);
            }
            return $this->validationErrorsAjaxResponse($errors);
        }

        return $this->response($this->render('helpphone.twig', [
            'model' => $model,
            'formModel' => $formModel,
        ]));
    }
}
