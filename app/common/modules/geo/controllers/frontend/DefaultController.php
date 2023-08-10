<?php

namespace common\modules\geo\controllers\frontend;

use yii\web\Response;
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
    public function beforeAction($action): bool
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
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionIndex($city = null): Response
    {
        $city = City::findByNameEng($city ?: 'msk');

        if (!$city) {
            throw new NotFoundHttpException();
        }

        return $this->response($this->render('index.twig', ['city' => $city]));
    }
}
