<?php

namespace common\components;

use ReflectionClass;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidArgumentException;
use common\modules\partners\interfaces\ServiceInterface;

/**
 * Компонент обеспечивает работу с платежными сервисами приложения
 *
 * Необходимо сконфигурировать приложение добавив список сервисов:
 *
 *   'service' => [
 *       'class' => 'common\components\Service',
 *       'services' => [
 *           'MODULE' => [
 *               'SERVICE_1' => '\common\modules\MODULE\services\Service1',
 *               'SERVICE_2' => '\common\modules\MODULE\services\Service2',
 *           ],
 *       ],
 *   ],
 *
 * Классы сервисов должны реализовывать интерфейс: common\interfaces\ServiceInterface
 *
 */
class Service extends Component
{
    /**
     * @var array
     */
    public $services = [];

    /**
     * @var array
     */
    private $_servicesInstanceList = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        foreach ($this->services as $module => $services) {
            foreach ($services as $service => $class) {
                if (isset($this->_servicesInstanceList[$service])) {
                    throw new InvalidConfigException('Service name "' . $service . '" must be unique');
                }

                $oReflectionClass = new ReflectionClass($class);
                $instance = $oReflectionClass->newInstance();

                if (!$instance instanceof ServiceInterface) {
                    throw new InvalidArgumentException('"' . $class . '" his class must instanceof ServiceAdvertInterface');
                }

                $this->_servicesInstanceList[$service] = $instance;
            }
        }
    }

    /**
     * Возвращает инстанс класса сервиса
     * @param $service
     * @return object
     */
    public function load($service)
    {
        if (!isset($this->_servicesInstanceList[$service])) {
            throw new InvalidArgumentException('Service name "' . $service . '" not found');
        }

        return $this->_servicesInstanceList[$service];
    }

    /**
     * Возвращает список всех сервисов
     *
     * Результат:
     *   Array
     *   (
     *      [СЕРВИС] => Название,
     *      [СЕРВИС] => Название,
     *      [СЕРВИС] => Название,
     *   )
     *
     * @return array
     */
    public function getList()
    {
        $list = [];

        foreach ($this->_servicesInstanceList as $service => $instance) {
            $list[$service] = $instance->getName();
        }

        return $list;
    }
}
