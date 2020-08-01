<?php

namespace frontend\modules\merchant\controllers;

use frontend\modules\merchant\models\PaymentSearch;
use frontend\modules\merchant\models\Payment;
use frontend\modules\merchant\models\Invoice;
use frontend\modules\merchant\models\Pay;
use yii\widgets\ActiveForm;
use yii\web\Response;
use Yii;

/**
 * Платежный контроллер
 * @package frontend\modules\merchant\controllers
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
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

        return array_merge($behaviors, require(__DIR__ . '/../caching/default.php'));
    }

    /**
     * История денежного оборота
     * @return string
     */
    public function actionIndex()
    {
        $model = new Payment();
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search();

        return $this->render('index.twig', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'systemArray' => $this->module->systems
        ]);
    }

    /**
     * История денежного оборота
     * @return array|string
     */
    public function actionPay()
    {
        $model = new Pay(['scenario' => 'payment']);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {

                // Создаем счет-фактуру
                $invoice = new Invoice();
                $invoice->hash = md5(uniqid(true));
                $invoice->user_id = Yii::$app->user->id;
                $invoice->system = $model->system;
                $invoice->funds = $model->amount;
                $invoice->save(false);

                // Редирект на робокассу
                $title = 'Пополнение счета';
                $email = Yii::$app->user->identity->email;
                return Yii::$app->robokassa->payment($invoice->funds, $invoice->invoice_id, $title, $invoice->system, $email, 'ru');

            } else if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
        }

        return $this->render('pay.twig', [
            'model' => $model
        ]);
    }

    /**
     * Оплата сервиса
     * @return array|Response
     */
    public function actionService()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $model = new Pay();
            $model->scenario = Yii::$app->request->post('type', null);
            $model->service = Yii::$app->request->post('service', null);
            $model->system = Yii::$app->request->post('system', null);
            $model->data = Yii::$app->request->post('data', []);
            $model->processId = Yii::$app->request->post('processId', null);

            if ($model->validate()) {
                return $model->process($transaction);
            }

            // Возникла ошибка
            foreach ($model->getErrors() as $error) {
                return [
                    'status' => 0,
                    'message' => $error[0]
                ];
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'status' => 0,
                //'message' => $e->getMessage() . ' ' . $e->getCode(),
                'message' => 'Возникла критическая ошибка, пожалуйста обратитесь в техническую поддержку.',
            ];
        }
    }

    /**
     * Генерирует платежный виджет для выбора оплаты
     * @return string|Response
     */
    public function actionPaymentWidget()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        return $this->renderAjax('../ajax/payment-widget.php', [
            'service' => Yii::$app->request->post('service'),
            'data' => Yii::$app->request->post('data', []),
        ]);
    }
}