<?php

namespace common\modules\merchant\controllers\frontend;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\db\Transaction;
use yii\widgets\ActiveForm;
use common\modules\merchant\models\Invoice;
use common\modules\merchant\models\Payment;
use common\modules\merchant\models\frontend\Pay;
use common\modules\merchant\models\frontend\PaymentSearch;

/**
 * Платежный контроллер
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/merchant/views/frontend';

        return true;
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'pay'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['service', 'payment-widget'],
                        'roles' => ['@', '?']
                    ],
                ]
            ]
        ];

        if (YII_DEBUG) {
            return $behaviors;
        }

        return array_merge($behaviors, require(__DIR__ . '/../../caching/default.php'));
    }

    /**
     * История денежного оборота
     * @return Response
     */
    public function actionIndex(): Response
    {
        $model = new Payment();
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search();

        return $this->response($this->render('index.twig', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'systemArray' => $this->module->systems
        ]));
    }

    /**
     * История денежного оборота
     * @return Response
     */
    public function actionPay(): Response
    {
        $model = new Pay(['scenario' => 'payment']);

        if ($model->load(Yii::$app->request->post())) {
            $errors = $this->validate($model);
            if (empty($errors)) {
                // Создаем счет-фактуру
                $invoice = new Invoice();
                $invoice->hash = md5(uniqid(true, true));
                $invoice->user_id = Yii::$app->user->id;
                $invoice->system = $model->system;
                $invoice->funds = $model->amount;
                $invoice->save(false);

                $title = 'Пополнение счета';
                $email = Yii::$app->user->identity->email;

                // Редирект на робокассу
                return Yii::$app->robokassa->payment(
                    $invoice->funds,
                    $invoice->invoice_id,
                    $title,
                    $invoice->system,
                    $email,
                    'ru'
                );
            }

            return $this->validationErrorsAjaxResponse($errors);
        }

        return $this->response($this->render('pay.twig', [
            'model' => $model
        ]));
    }

    /**
     * Оплата сервиса
     * @return Response
     */
    public function actionService(): Response
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model = new Pay();
            $model->scenario = Yii::$app->request->post('type');
            $model->service = Yii::$app->request->post('service');
            $model->system = Yii::$app->request->post('system');
            $model->data = Yii::$app->request->post('data', []);
            $model->processId = Yii::$app->request->post('processId');
            if ($model->validate()) {
                $model->process($transaction);
            } else {
                $transaction->rollBack();
            }

            // Возникла ошибка, возвращаем первую
            foreach ($model->getErrors() as $error) {
                return $this->validationTotalErrorAjaxResponse($error[0]);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->criticalErrorsAjaxResponse($e);
        }

        return $this->successAjaxResponse('');
    }

    /**
     * Генерирует платежный виджет для выбора оплаты
     * @return Response
     */
    public function actionPaymentWidget(): Response
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        return $this->response($this->renderAjax('../ajax/payment-widget.php', [
            'service' => Yii::$app->request->post('service'),
            'data' => Yii::$app->request->post('data', []),
        ]));
    }
}
