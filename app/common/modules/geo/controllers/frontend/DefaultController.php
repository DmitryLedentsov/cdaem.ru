<?php

namespace common\modules\geo\controllers\frontend;

use yii\web\NotFoundHttpException;
use common\modules\geo\models\City;

/**
 * Главный гео контроллер
 * @package common\modules\geo\controllers\frontend
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/geo/views/frontend';

        return true;
    }

    /**
     * Общая карта
     *
     * @param null $city
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($city = null)
    {
        $city = City::findByNameEng($city ? $city : 'msk');

        if (!$city) {
            throw new NotFoundHttpException();
        }

        return $this->render('index.twig', ['city' => $city]);
    }
}
