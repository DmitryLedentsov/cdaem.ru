<?php

namespace common\modules\partners\interfaces;

/**
 * Интерфейс для реалицации класса сервиса
 * @package common\modules\partners\interfaces
 */
interface ServiceInterface
{
    /**
     * Возвращает идентификатор сервиса
     *
     * @return string
     */
    public function getId();

    /**
     * Возвращает название сервиса
     *
     * @return double
     */
    public function getName();

    /**
     * Возвращает стоимость сервиса
     *
     * @return double
     */
    public function getPrice();

    /**
     * Возвращает скидку, при оплате за несколько единиц
     *
     * @param $amount - Кол-во выбранных объектов
     * @param array $data - Дополнительные данные для уточнения
     * @return double
     */
    public function calculateDiscount($amount, array $data = []);

    /**
     * Сервис зависит от интервала времени
     *
     * Запуск активации некоторых сервисов может зависить от указанной даты старта
     *
     * @return bool
     */
    public function isTimeInterval();

    /**
     * Запуск сервиса происходит сразу
     *
     * Если параметр установлен как true, то процесс сервиса будет запущен сразу после оплаты
     * Если параметр установлен как false, то процесс сервиса будет запущен через специальный CRON сценарий
     *
     * @return bool
     */
    public function isInstant();

    /**
     * Установить конфигурацию сервиса
     *
     * @param \common\modules\partners\models\Service $process
     * @return mixed
     */
    public function setProcess(\common\modules\partners\models\Service $process);

    /**
     * Валидация данных
     *
     * @return bool
     */
    public function validate();

    /**
     * Включение процесса активации сервиса
     *
     * @return bool
     */
    public function enable();

    /**
     * Выключение процесса активации сервиса
     *
     * @return bool
     */
    public function disable();

    /**
     * Возвращает данные для отправки сообщения на почтовый адресс
     *
     * return [
     *     'subject' => null,
     *     'view' => null,
     *     'data' => [
     *
     *      ],
     *     'email' => 'example@mail.ru',
     * ];
     *
     * @return array
     */
    public function getMailData();

    /**
     * Возвращает строку для отправки sms. Если null, значит смс отправлять не нужно.
     *
     * @return string
     */
    public function getSmsString();

    /**
     * Необходимость откатить процесс
     *
     * true - Если сервис необходимо откатать по истечению времени (например закрыть контакты или убрать топ)
     * false - Если сервис не нуждается в откате (например открыть контакты или поднять позицию)
     *
     * @return bool
     */
    public function isNeedRollBackProcess();

    /**
     * Запуск процесса в фоновом режиме
     *
     * @return bool
     */
    public function runBackgroundProcess();
}