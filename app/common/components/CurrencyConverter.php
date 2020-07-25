<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\ErrorException;

class CurrencyConverter extends Component
{
    /**
     * Ссылка на курс валют с Цетробанка России
     * @var string
     */
    public $url = 'http://www.cbr.ru/scripts/XML_daily.asp';

    /**
     * Список курса валют
     * @var array
     */
    private $_list = [];

    /**
     * Ключ кэшированных данных
     * @var string
     */
    public $cacheKey = 'quotation_list';

    /**
     * Время существования кэша
     * @var int
     */
    public $cacheTime = 3600;

    /**
     * ID Компонента кэша
     * @var string
     */
    public $cacheComponent = 'cache';


    public function rubleRate($to)
    {
        if (isset($this->list[$to])) return $this->list[$to];

        throw new ErrorException('Невозможно узнать курс валют прямо сейчас.');
    }

    public function getList()
    {
        if (!empty($this->_list)) return $this->_list;

        $cachedData = Yii::$app->get($this->cacheComponent)->get($this->cacheKey);

        if ($cachedData) {
            $this->_list = $cachedData;
        } else {
            $this->loadNewList();
        }

        return $this->_list;
    }

    /**
     * Загружает курс валют в свойтсво $_list и записывает в кэш
     */
    public function loadNewList()
    {
        $xml = new \DOMDocument();

        if (@$xml->load($this->url)) {

            $root = $xml->documentElement;
            $items = $root->getElementsByTagName('Valute');

            $this->_list = [];

            foreach ($items as $item) {
                $code = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;
                $curs = $item->getElementsByTagName('Value')->item(0)->nodeValue;

                $this->_list[$code] = str_replace(',', '.', $curs);
            }

            Yii::$app->get($this->cacheComponent)->set($this->cacheKey, $this->_list, $this->cacheTime);
        }
    }
}