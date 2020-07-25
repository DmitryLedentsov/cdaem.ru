<?php

namespace common\modules\articles\controllers\frontend;

use common\modules\articles\models\Article;
use yii\web\HttpException;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use Yii;

/**
 * Статьи
 * @package frontend\modules\articles\controllers
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
                'class' => \yii\filters\AccessControl::className(),
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

        $this->module->viewPath = '@common/modules/articles/views/frontend';

        return true;
    }

    /**
     * Просмотр
     *
     * @param $id
     * @param null $city
     * @return string
     * @throws HttpException
     */
    public function actionView($id, $city = null)
    {
        $query = Article::find();

        if (is_string($id)) {
            $query->where('slug = :slug', [':slug' => $id]);
        } else {
            $query->where('acticle_id = :id', [':slug' => $id]);
        }

        if (!is_null($city)) {
            $query->andWhere(['city' => $city]);
        } else {
            $query->andWhere('city IS NULL');
        }

        $model = $query->one();

        if (!$model) {
            $url = Url::toRoute(['/acticles/default/view', 'url' => $id], true);
            $url = Html::a($url, $url);
            throw new HttpException(404, 'Статьи по адресу ' . $url . ' не существует.');
        }

        return $this->render('view.twig', [
            'model' => $model,
        ]);
    }

    /**
     * Все
     *
     * @param null $city
     * @return string
     */
    public function actionIndex($city = null)
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

        $pages->defaultPageSize = $this->module->recordsPerPage;
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->all();

        return $this->render('index.twig', [
            'model' => $model,
            'models' => $models,
            'pages' => $pages,
        ]);
    }


    public function actionNews($city = null)
    {
        $model = new Article;

        $query = Article::find()->limit(20)
            ->orderBy(['date_create' => SORT_DESC]);

        if (!is_null($city)) {
            $query->where(['city' => $city]);
        } else {
            $query->where('city IS NULL');
        }

        $countQuery = clone $query;
        // $pages = new Pagination(['totalCount' => $countQuery->count()]);

        // $pages->defaultPageSize = $this->module->recordsPerPage;
        $models = $query
            ->asArray()
            ->all();

        return $this->render('news.twig', [
            'model' => $model,
            'models' => $models,
            //  'pages' => $pages,
        ]);
    }


}