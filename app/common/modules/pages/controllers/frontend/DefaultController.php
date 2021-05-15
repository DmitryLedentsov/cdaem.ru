<?php

namespace common\modules\pages\controllers\frontend;

use Yii;
use yii\web\HttpException;
use common\modules\pages\models\Page;
use yii\web\Response;

/**
 * Default controller
 * @package common\modules\pages\controllers\frontend
 */
class DefaultController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
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
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->module->viewPath = '@common/modules/pages/views/frontend';

        return true;
    }

    /**
     * Просмотр страницы
     *
     * @param $url
     * @return Response
     * @throws HttpException
     */
    public function actionIndex($url): Response
    {
        $model = Page::getPageByUrl($url);

        if (Yii::$app->request->isAjax) {
            return $model->text;
        }

        return $this->response($this->render('index.twig', [
            'model' => $model,
        ]));
    }
}
