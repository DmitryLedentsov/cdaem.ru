<?php

namespace common\modules\merchant\models\frontend;

use Yii;
use yii\helpers\Json;
use common\modules\users\models\User;
use common\modules\merchant\models\Invoice;
use common\modules\partners\models\Service;
use common\modules\partners\traits\ModuleTrait;
use common\modules\partners\interfaces\ServiceInterface;

/**
 * Оплата счета
 */
class Pay extends \yii\base\Model
{
    use ModuleTrait;

    /**
     * Название сервиса
     * @var string
     */
    public $service;

    /**
     * Система оплаты
     * @var string
     */
    public $system;

    /**
     * Идентификатор процесса активации сервиса
     * @var string
     */
    public $processId;

    /**
     * Кол-во средств для пополнения
     * @var float
     */
    public $amount;

    /**
     * Дополнительные данные
     * @var array
     */
    public $data;

    /**
     * Общая стоимость к оплате
     * @var float
     */
    private $_price;

    /**
     * Инициализация сервиса
     * @var ServiceInterface
     */
    private $_service;

    /**
     * Инициализация процесса
     * @var \common\modules\partners\models\Service
     */
    private $_process;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'pay-account' => ['service', 'data'],
            'pay-system' => ['service', 'system', 'data'],
            'payment' => ['system', 'amount'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Сервис
            ['service', 'required'],
            ['service', 'in', 'range' => array_keys(Yii::$app->service->getList())],

            // Способ оплаты
            ['system', 'required', 'message' => 'Выберите подходящий способ оплаты.'],
            ['system', 'string'],

            // Кол-во средств
            ['amount', 'required'],
            ['amount', 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'service' => 'Сервис',
            'user_id' => 'Пользователь',
            'system' => 'Способ оплаты',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            // Определяем сценарий
            if (!in_array($this->scenario, array_keys($this->scenarios()))) {
                $this->scenario = 'pay-account';
            }

            if (!Yii::$app->user->id) {
                $this->scenario = 'pay-system';
            }

            if ($this->scenario != 'payment') {

                // Формируем массив данных
                if (empty($this->data) || !is_array($this->data)) {
                    $this->data = [];
                }

                // Проверяем передал ли параметр processId, который указывает на идентификатор процесса на активацию сервиса
                // Если processId не передан, создадим запись нового идентификатора процесса на активацию сервиса
                if (!$this->processId) {
                    if (!Yii::$app->user->isGuest) {
                        $this->data['user_id'] = Yii::$app->user->id;
                    }
                    $this->processId = Service::addProcess($this->service, null, null, $this->data);
                }

                $this->_process = Service::findProcessById($this->processId);

                $error = true;

                if (!Yii::$app->user->isGuest) {
                    if ($this->_process->user_id == Yii::$app->user->id) {
                        $error = false;
                    }
                } else {
                    if ($this->_process->user_id == null) {
                        $error = false;
                    }
                }

                if ($error) {
                    $this->addError('', 'Возникла ошибка при проверке идентификатора процесса активации сервиса, пожалуйста попробуйте еще раз или обратитесь в службу технической поддержки.');
                } else {
                    $data = Json::decode($this->_process->data);
                    $this->service = $this->_process->service;
                    $this->_price = isset(Json::decode($this->_process->data)['price']) ? $data['price'] : null;
                    $this->data = array_merge($this->data, $data);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if ($this->scenario !== 'payment') {
            // Инициализация сервиса
            $this->_service = Yii::$app->service->load($this->service);
            $this->_price = $this->_price ? $this->_price : $this->_service->getPrice();

            // Проверка данных пользователя при оплате с личного счета
            if ($this->scenario == 'pay-account') {
                if (Yii::$app->user->isGuest) {
                    $this->addError('user', 'Вы не авторизованы. Пожалуйста войдите на сайт под своим логином и паролем.');

                    return false;
                }
                if (!Yii::$app->user->isGuest && Yii::$app->user->identity->funds_main < $this->_price) {
                    $this->addError('user', 'На Вашем счету недостаточно средств. Пожалуйста пополните Ваш счет.');

                    return false;
                }
            }

            // Валидация данных для активации сервиса
            $this->_service = $this->_service->setProcess($this->_process);

            // dd($this->_service, $this->_service->validate()); // todo false при покупки сервиса попасть в слайдер

            if (!$this->_service->validate()) {
                $this->addError('', 'Возникла ошибка при проверке данных для активации сервиса, пожалуйста попробуйте еще раз или обратитесь в службу технической поддержки.');

                return false;
            }
            if (!$this->_service->validateContact()) {
                $this->addError('', 'Ваше объявление под бронью, Вы не можете оплатить!.');

                return false;
            }
            if (!$this->_service->validateContactOpen()) {
                $this->addError('', 'У Вашего объявления открыты контакты или дождитесь активации уже оплаченых. Около 10 минут');

                return false;
            }
        }
    }

    /**
     * ПРОЦЕСС ОПЛАТЫ
     *
     * Процесс оплаты состоит из двух способов, каждый из которых включает дополнительные проверки.
     *
     * 1 Способ: Оплата с личного счета.
     * Пользователь производит оплату, после чего у него списываются средства с личного счета.
     *  - Если процесс активации сервиса уже был в очереди, то ему присваевается идентификатор платежа и
     *    в зависимости от типа сервиса он ожидает выполнения или выполняется сразу.
     *  - Если процесс активации сервиса не найден в очереди, то создается новый процесс с уже
     *    присвоенным идентификатором платежа и в зависимости от типа сервиса он ожидает выполнения
     *    или выполняется сразу.
     *
     * 2 Способ: Оплата через платежную систему.
     * Пользователь выбирает способ оплаты, после чего создается счет-фактура и новый процесс
     * активации сервиса, который ожидает оплаты.
     * В свою очередь для пользователя срабатывает редирект на выбранную платежную систему.
     *
     * @param $transaction
     * @return array|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function process($transaction)
    {
        if ($this->scenario == 'pay-account') {

            // Чтобы исключить случайные покупки на сайте, пользователь может оплачивать услуги 1 раз в минуту
            /* if (Service::getCountRecordsForInterval(Yii::$app->user->id)) {
                $transaction->rollBack();
                return [
                    'status' => 0,
                    'message' => 'Последнее время оплаты услуги было зафиксировано менее 10 секунд назад. Чтобы исключить случайные покупки на сайте действует временное ограничение.',
                ];
            } */

            // Списать средства у пользователя
            $paymentId = Yii::$app->balance
                ->setModule($this->module->id)
                ->setUser(User::findOne(Yii::$app->user->id))
                ->costs($this->_price, $this->service);

            // Идентификатор платежа у процесса оплаты
            $this->_process->payment_id = $paymentId;
            $this->_process->save(false);

            $transaction->commit();

            // Если сервис выполняется мгновенно
            if ($this->_service->isInstant()) {
                $this->_service->runBackgroundProcess();
            } else {
                // Уведомить пользователя на email об оплате сервиса.
                Yii::$app->consoleRunner->run('service/send-mail ' . $this->_process->id . ' payment');
            }

            Yii::$app->session->setFlash('info', 'Услуга успешно оплачена. Пожалуйста обратите внимание на поле "Дата включения" и без необходимости повторно не оплачивайте сервис.');

            return [
                'status' => 1,
                'message' => 'Услуга успешно оплачена. В течении нескольких минут Вам будет отправлено письмо с дальнейшими инструкциями.',
                'funds' => Yii::$app->formatter->asCurrency(Yii::$app->user->identity->funds_main - $this->_price, 'RUB'),
                'redirect' => ['/office/orders']
            ];
        }

        // Оплата с помощью платежной системы
        return $this->paymentSystem($transaction);
    }

    /**
     * Оплата с помощью платежной системы
     *
     * После верификации переданных данных для активации сервиса создается новая запись
     * процесса активации, создается новая счет-фактура, которой присваевается номер процесса
     * активации сервиса, после чего сработает редирект на выбранную платежную систему.
     *
     * @param $transaction
     * @return mixed
     */
    private function paymentSystem($transaction)
    {
        // Создаем счет-фактуру
        $invoice = new Invoice();
        $invoice->hash = md5(uniqid(true, true));
        $invoice->user_id = Yii::$app->user->id;
        $invoice->system = 'ROBOKASSA: ' . $this->system;
        $invoice->funds = $this->_price;
        $invoice->process_id = $this->_process->id;
        $invoice->save(false);

        $email = null;

        if (!Yii::$app->user->isGuest) {
            $email = Yii::$app->user->identity->email;
        } else {
            $data = Json::decode($this->_process->data);
            if (isset($data['email'])) {
                $email = $data['email'];
            }
        }

        $serviceName = 'Услуга: "' . $this->_service->getName() . '"';
        // $adverts_selected = $this->_service->_selected;

        $transaction->commit();

        // Редирект на робокассу
        return Yii::$app->robokassa->payment($invoice->funds, $invoice->invoice_id, $serviceName, $this->system, $email, 'ru');
    }
}
