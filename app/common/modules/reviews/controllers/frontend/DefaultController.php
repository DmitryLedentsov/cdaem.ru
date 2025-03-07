<?php

namespace common\modules\reviews\controllers\frontend;

use common\modules\reviews\models\ReviewSearch;

/**
 * Default Controller
 * @package common\modules\reviews\controllers\frontend
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
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
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

        $this->module->viewPath = '@common/modules/reviews/views/frontend';

        return true;
    }

    /**
     * Все отзывы
     * @param null $id
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        //$countReviews = Review::getCountReviewsToApartmentByUser($id);
        $searchModel = new ReviewSearch();
        $dataProvider = $searchModel->search(['apartment_id' => $id], true);
        $countReviews = $dataProvider->totalCount;

        return $this->render('index.twig', [
            'countReviews' => $countReviews,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
