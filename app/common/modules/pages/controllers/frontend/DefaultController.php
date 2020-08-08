<?php

namespace common\modules\pages\controllers\frontend;

use Yii;
use yii\web\HttpException;
use common\modules\pages\models\Page;

/**
 * Default controller
 * @package common\modules\pages\controllers\frontend
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];

        if (YII_DEBUG) {
            return $behaviors;
        }

        return array_merge($behaviors, require(__DIR__ . '/../../caching/default.php'));
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/pages/views/frontend';

        return true;
    }

    /**
     * Просмотр страницы
     * @param $url
     * @return mixed|string
     * @throws HttpException
     */
    public function actionIndex($url)
    {
        $model = Page::getPageByUrl($url);

        if (Yii::$app->request->isAjax) {
            return $model->text;
        }

        return $this->render('index.twig', [
            'model' => $model,
        ]);
    }
}
