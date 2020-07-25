<?php

namespace common\modules\merchant\components;

use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\base\Module;
use Yii;


/**
 * Управление балансом пользователя
 */
class Balance
{
    /**
     * Счета пользователя
     * @var array
     */
    public $accounts = ['funds_main'];

    /**
     * @var string
     */
    private $_model = 'common\modules\merchant\models\Payment';

    /**
     * @var string
     */
    private $_method = 'newPayment';

    /**
     * Имя текущего модуля
     * @var string
     */
    private $_module;

    /**
     * @var \common\modules\users\models\User
     */
    private $_user;

    /**
     * Устанавливаем модуль
     * @param $module
     * @return $this
     */
    public function setModule($module)
    {
        if (!Yii::$app->getModule($module) instanceof Module) {
            throw new InvalidParamException(get_class($this) . ': Unkown module: ' . $module);
        }
        $this->_module = $module;
        return $this;
    }

    /**
     * Устанавливаем пользователя
     * @param $user
     * @return $this
     */
    public function setUser($user)
    {
        if (!$user instanceof ActiveRecord) {
            throw new InvalidParamException(get_class($this) . ': Class user must extends ActiveRecord');
        }
        $this->_user = $user;
        return $this;
    }

    /**
     *  Модель для учета истории денежного оборота
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        if (!$model instanceof ActiveRecord) {
            throw new InvalidParamException(get_class($this) . ': Payment model must extends ActiveRecord');
        }
        $this->_model = $model;
        return $this;
    }

    /**
     * Пополнение
     * @param $amount
     * @param $system
     * @param null $account
     * @return mixed
     */
    public function deposit($amount, $system, $account = null)
    {
        $model = $this->_model;
        return $this->change($model::DEPOSIT, $system, $amount, $account);
    }

    /**
     * Начисление
     * @param $amount
     * @param $system
     * @param null $account
     * @return mixed
     */
    public function billing($amount, $system, $account = null)
    {
        $model = $this->_model;
        return $this->change($model::BILLING, $system, $amount, $account);
    }

    /**
     * Расходы
     * @param $amount
     * @param $system
     * @param null $account
     * @return mixed
     */
    public function costs($amount, $system, $account = null)
    {
        $model = $this->_model;
        return $this->change($model::COSTS, $system, $amount, $account);
    }

    /**
     * Изменить Баланс Пользователя
     * @param $type
     * @param $system
     * @param $amount
     * @param $account
     * @return mixed
     */
    private function change($type, $system, $amount, $account)
    {
        if (!Yii::$app->getModule('merchant')) {
            throw new InvalidParamException('Module ' . $this->_module . '" not initialized');
        }

        $systemsArray = Yii::$app->getModule('merchant')->systems;

        if (!isset($systemsArray[$this->_module])) {
            throw new InvalidParamException(get_class($this) . ': Module "' . $this->_module . '" not founds in systems list');
        }

        if (!isset($systemsArray[$this->_module][$system])) {
            throw new InvalidParamException(get_class($this) . ': System "' . $system . '" not founds in systems list');
        }

        $account = $account ? $account : $this->accounts[0];

        if ($account === null) {
            $account = isset($this->accounts[0]) ? $this->accounts[0] : null;
        }

        if (!isset($this->_user->$account)) {
            throw new InvalidParamException(get_class($this) . ': Unkown account: ' . $account);
        }

        // Было средств на счету
        $was = $this->_user->$account;

        $model = $this->_model;

        if ($type == $model::DEPOSIT || $type == $model::BILLING) {
            $this->_user->$account += $amount;
        } else {
            $this->_user->$account -= $amount;
        }

        // Обновляем счет пользователя
        $this->_user->save(false);

        // Запись в историю денежного оборота
        return call_user_func_array([$model, $this->_method], [$this->_module, $type, $system, $this->_user->id, $was, $amount]);
    }

}
