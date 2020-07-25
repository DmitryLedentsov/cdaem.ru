<?php

namespace frontend\modules\merchant\controllers;

use common\modules\merchant\models\Payment;
use frontend\modules\merchant\models\Invoice;
use common\modules\partners\models\Service;
use common\modules\users\models\User;
use yii\web\HttpException;
use Yii;

/**
 * @TODO: Полностью переделать
 * Robokassa
 * @package frontend\modules\merchant\controllers
 */
class RobokassaController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['success', 'failure'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['result'],
                        'roles' => ['?', '@']
                    ],
                ]
            ]
        ];
    }

    /**
     * Url адрес взаимодействия
     * Принимает данные от Robokassa, верифицирует платеж и записывает логи
     * @return \yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionResult()
    {
        if (!Yii::$app->request->post()) {
            Yii::info('ERROR: Возникла критическая ошибка: сервер Robokassa не смог передать данные для верификации платежа.', 'robokassa');
            return $this->goHome();
        }

        $invoiceId = Yii::$app->request->getBodyParam('InvId', null);
        //Yii::info('TRACE: Запрос от Robokassa на  верификацию платежа, счет №' . $invoiceId, 'robokassa');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $invoice = Invoice::findOne($invoiceId);
            if ($invoice) {
                if ($invoice->paid) {
                    Yii::info('WARNING: Счет №' . $invoiceId . ' уже оплачен', 'robokassa');
                } else {
                    if ($this->verify(Yii::$app->request->getBodyParams(), $invoice, Yii::$app->robokassa->mrchPassword2)) {
                        Yii::info('SUCCESS: Верификация платежа, счет №' . $invoiceId . ', прошла успешно', 'robokassa');
                    } else {
                        Yii::info('ERROR: Ошибка верификации платежа, счет №' . $invoiceId, 'robokassa');
                    }
                }
            } else {
                Yii::info('ERROR: Счет №' . $invoiceId . ' не найден в базе данных', 'robokassa');
            }
            $transaction->commit();
            echo 'OK' . $invoiceId;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::info('ERROR: Возникла критическая ошибка: ' . $e->getMessage(), 'robokassa');
        }
    }

    /**
     * Успешный платеж
     * @return \yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionSuccess()
    {
        if (!Yii::$app->request->post()) {
            if (Yii::$app->user->id) {
                Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста, обратитесь в службу технической поддержки.');
                return $this->redirect(['/merchant/default/index']);
            }
            return $this->goHome();
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $invoiceId = Yii::$app->request->post('InvId', null);
            $invoice = Invoice::findOne($invoiceId);
            if ($invoice) {
                if ($invoice->paid) {
                    Yii::$app->session->setFlash('success', 'Ваш счет успешно оплачен.');
                } else {
                    Yii::$app->session->setFlash('success', 'Ожидание оплаты счета. При успешной оплате средства будут зачислены в течении нескольких минут.');
                }
            } else {
                Yii::$app->session->setFlash('danger', 'Возникла ошибка. Счет №' . $invoiceId . ' не найден в базе данных. Пожалуйста обратитесь в службу технической поддержки.');
            }
            $transaction->commit();
            if ($invoice && $invoice->process_id) {
                return $this->redirect(['/office/default/orders']);
            }
            return $this->redirect(['/merchant/default/index']);
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста, обратитесь в службу технической поддержки.');
            return $this->redirect(['/merchant/default/index']);
        }
    }

    /**
     * Ошибка платежа
     * @return \yii\web\Response
     */
    public function actionFailure()
    {
        if (!Yii::$app->request->post()) {
            if (Yii::$app->user->id) {
                Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста, обратитесь в службу технической поддержки.');
                return $this->redirect(['/merchant/default/index']);
            }
            return $this->goHome();
        }
        Yii::$app->session->setFlash('danger', 'Возникла критическая ошибка. Пожалуйста, обратитесь в службу технической поддержки.');
        return $this->redirect(['/merchant/default/index']);
    }

    /**
     * Верификация платежа
     *
     * @param $data
     * @param Invoice $invoice
     * @param $password
     * @return bool
     */
    protected function verify($data, Invoice $invoice, $password)
    {
        $hash = isset($data['SignatureValue']) ? $data['SignatureValue'] : null;
        $nOutSum = isset($data['OutSum']) ? $data['OutSum'] : null;
        $nInvId = isset($data['InvId']) ? $data['InvId'] : null;

        // Проверка цифровой печати
        if (!empty($invoice) && Yii::$app->robokassa->checkHash($hash, $nOutSum, $nInvId, $password, null)) {

            // Начисление средств на счет пользователя
            $invoice->paid = 1;
            $invoice->date_payment = date('Y-m-d H:i:s');
            $invoice->save(false);

            // Начислить средства пользователю
            if (!is_null($invoice->user_id)) {
                Yii::$app->balance
                    ->setModule($this->module->id)
                    ->setUser(User::findOne($invoice->user_id))
                    ->deposit($invoice->funds, Invoice::ROBOKASSA);
            }

            // Если есть идентификатор процесса, значит оплата идет сразу за сервис
            if (!empty($invoice->process_id) && $process = Service::findProcessById($invoice->process_id)) {

                if (!is_null($invoice->user_id)) {

                    // Списать средства у пользователя
                    $paymentId = Yii::$app->balance
                        ->setModule('partners')
                        ->setUser(User::findOne($invoice->user_id))
                        ->costs($invoice->funds, $process->service);

                } else {

                    // Инициализация сервиса
                    $service = Yii::$app->service->load($process->service);
                    $service->setProcess($process);

                    $paymentId = Payment::newPayment(
                        'partners',
                        Payment::COSTS,
                        $process->service,
                        null,
                        0,
                        $invoice->funds
                    );

                    // Если сервис выполняется мгновенно
                    if ($service->isInstant()) {
                        $service->runBackgroundProcess();
                    }

                }

                // Идентификатор платежа у процесса оплаты
                $process->payment_id = $paymentId;
                $process->save(false);

            }

            return true;
        }

        return false;
    }
}
