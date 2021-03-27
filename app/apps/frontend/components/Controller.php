<?php

namespace frontend\components;

use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Общий контроллер
 * @package frontend\components
 */
class Controller extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public $layout = false;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        include_once(Yii::getAlias('@common/config/clickfrogru_udp_tcp.php'));

        return true;
    }

    /**
     * TODO: IE BUG REDIRECT
     * ВНИМАНИЕ:
     * На момент написания сайта был баг с 302 редиректом почти во всех браузерах IE:
     * https://github.com/yiisoft/yii2/issues/9670
     *
     * @inheritdoc
     */
    public function refresh($anchor = '')
    {
        return Yii::$app->getResponse()->redirect(Yii::$app->getRequest()->getUrl() . $anchor, 308);
    }

    public function validation()
    {

    }

    /**
     * Валидация
     *
     * По сути аналог для ActiveForm::validate($model)
     * Разница в том, что ActiveForm приводит название модели к нижнему регистру, чтобы ориентироваться на id формы.
     * В нашем случае фронт рассчитывает на поиск полей по имени и приводить к нижнему регистру не нужно.
     *
     * @param $model
     * @param null $attributes
     * @return array
     */
    public function validate($model, $attributes = null)
    {
        $result = [];
        if ($attributes instanceof Model) {
            // validating multiple models
            $models = func_get_args();
            $attributes = null;
        } else {
            $models = [$model];
        }
        /* @var $model Model */
        foreach ($models as $model) {
            $model->validate($attributes);
            foreach ($model->getErrors() as $attribute => $errors) {
                $name = Html::getInputName($model, $attribute);
                $name = str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], $name);
                $result[$name] = $errors;
            }
        }

        return $result;
    }
}
