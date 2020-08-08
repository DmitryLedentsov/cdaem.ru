<?php

namespace backend\components;

use Yii;
use yii\helpers\Url;

/**
 * Base controller
 */
class Controller extends \yii\web\Controller
{
    /**
     * @var string Заголовок
     */
    public $metaTitle = '';
}
