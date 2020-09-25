<?php

namespace common\modules\helpdesk;

/**
 * Общий модуль [[Helpdesk]]
 * Осуществляет всю работу с технической поддержкой
 */
class Module extends \yii\base\Module
{
    /**
     * @var integer Количество записей на странице
     */
    public $recordsPerPage = 18;

    /**
     * Интервал времени в секундах, спустя который можно создавать следующее обращение
     * @var int
     */
    public $interval = 600;

    /**
     * Интервал времени в секундах, спустя который можно отвечать на обращение
     * @var int
     */
    public $answerInterval = 60;

    /**
     * Минимальное кол-во символов для темы обращения
     * @var int
     */
    public $themeMinCharacters = 5;

    /**
     * Максимальное кол-во символов для темы обращения
     * @var int
     */
    public $themeMaxCharacters = 30;
}
