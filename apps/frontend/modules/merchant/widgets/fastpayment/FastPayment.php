<?php

namespace frontend\modules\merchant\widgets\fastpayment;

use frontend\modules\merchant\widgets\fastpayment\assets\FastPaymentAsset;
use common\modules\partners\models\Service;
use frontend\modules\merchant\models\Pay;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

/**
 * Виджет для быстрой оплаты
 * @package frontend\modules\merchant\widgets\fastpayment
 */
class FastPayment extends \yii\base\Widget
{
    /**
     * Описание
     * @var string
     */
    public $description;

    /**
     * Идентификатор сервиса
     * @var string
     */
    public $service;

    /**
     * Дополнительные данные
     * @var array
     */
    public $data;

    /**
     * Идентификатор процесса очереди для активации сервиса
     *
     * id записи из таблицы Service::tableName()
     * @var int
     */
    public $processServiceId;

    /**
     * Инициализированный объект сервиса
     * @var Yii::$app->service
     */
    private $_service;

    /**
     * Необработанный процесс активации сервиса
     * @var Service
     */
    private $_processService;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->description)) {
            $this->description = "&nbsp;";
        }

        // Необходимо передать параметр service для идентификации и оплаты разовых или одиночных сервисов
        // При этом параметр processServiceId указывает на оплату сервиса, который ожидает активации
        if (empty($this->service) && empty($this->processServiceId)) {
            throw new InvalidParamException('Params "service" or "processServiceId" can not be empty');
        }

        if (!empty($this->service)) {
            if (!in_array($this->service, array_keys(Yii::$app->service->getList()))) {
                throw new InvalidParamException('Service "'.$this->service.'" not initialized');
            }
            $this->_service = Yii::$app->service->load($this->service);
        }

        if (!empty($this->processServiceId)) {
            if (!$this->_processService = Service::findProcessById($this->processServiceId)) {
                throw new InvalidParamException('Service process by id "'.$this->processServiceId.'" not found');
            }
            $this->_service = Yii::$app->service->load($this->_processService->service);
        }

        if (empty($this->data) || !is_array($this->data)) {
            $this->data = [];
        }

        $view = $this->getView();
        FastPaymentAsset::register($view);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = new Pay();

        return $this->render('index.php', [
            'model' => $model,
            'description' => $this->description,
            'data' => $this->data,
            'service' => $this->_service,
            'processService' => $this->_processService,
        ]);
    }
}
