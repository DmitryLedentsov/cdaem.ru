<?php

namespace frontend\components;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\BaseObject;
use common\components\Response;

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

    /**
     * @param string $message
     * @param array $data
     * @return string
     */
    public function successAjaxResponse(string $message, array $data = []): string
    {
        Yii::$app->response->statusCode = 200;

        $result = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];

        return Json::encode($result);
    }

    /**
     * @param array $errors
     * @return string
     */
    public function validationErrorsAjaxResponse(array $errors): string
    {
        Yii::$app->response->statusCode = 422;

        return Json::encode($errors);
    }

    /**
     * @param string $content
     * @return Response
     */
    public function response(string $content): Response
    {
        Yii::$app->response->content = $content;

        /** @var Response $response */
        $response = Yii::$app->response;

        return $response;
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

    /**
     * @param $models
     * @param null $attributes
     * @return array
     */
    public static function validateMultiple($models, $attributes = null)
    {
        $result = [];
        /* @var $model Model */
        foreach ($models as $i => $model) {
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
