<?php

namespace common\modules\articles\controllers\frontend;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\Response;
use yii\data\Pagination;
use yii\web\HttpException;
use common\modules\articles\models\Article;

/**
 * Статьи
 * @package common\modules\articles\controllers
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

        $this->module->viewPath = '@common/modules/articles/views/frontend';

        return true;
    }

    /**
     * Все
     *
     * @param null $city
     * @return Response
     */
    public function actionIndex($city = null): Response
    {
        $model = new Article;

        $query = Article::find()
            ->orderBy(['date_create' => SORT_DESC]);

        if (!is_null($city)) {
            $query->where(['city' => $city]);
        } else {
            $query->where('city IS NULL');
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $pages->defaultPageSize = (int)$this->module->recordsPerPage;
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->response($this->render('index.twig', [
            'model' => $model,
            'models' => $models,
            'pages' => $pages,
        ]));
    }

    /**
     * Просмотр
     *
     * @param $id
     * @param null $city
     * @return Response
     * @throws HttpException
     */
    public function actionView($id, $city = null): Response
    {
        $query = Article::find();

        if (is_string($id)) {
            $query->where('slug = :slug', [':slug' => $id]);
        } else {
            $query->where('article_id = :id', [':slug' => $id]);
        }

        if (!is_null($city)) {
            $query->andWhere(['city' => $city]);
        } else {
            $query->andWhere('city IS NULL');
        }

        $model = $query->one();

        if (!$model) {
            $url = Url::toRoute(['/articles/default/view', 'url' => $id], true);
            $url = Html::a($url, $url);

            throw new HttpException(404, 'Статьи по адресу ' . $url . ' не существует.');
        }

        return $this->response($this->render('view.twig', [
            'model' => $model,
        ]));
    }
}
