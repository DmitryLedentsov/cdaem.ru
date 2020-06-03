<?php

/**
 * Просмотр логов
 * @var yii\base\View $this Представление
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\modules\admin\models\LogSearch $searchModel
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'Просмотр логов сервера';

echo \backend\modules\admin\widgets\HeaderWidget::widget([
    'title' => 'Логи сервера',
    'description' => $this->title,
    'breadcrumb' => [
        [
            'label' => 'Все логи',
            'url' => ['/admin/default/log'],
        ]
    ]
]);
?>


<?php echo $this->render('log_search', ['model' => $searchModel]); ?>


<?= GridView::widget([
    'tableOptions' => ['class' => 'table table-bordered'],
    'layout' => '<div class="GridViewSummary">{summary}</div><div class="panel panel-default"><div class="table-responsive">{items}</div><div class="table-footer">  {pager}</div></div>',
    'dataProvider' => $dataProvider,
    'columns' => [

        [
            'attribute' => 'level',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return $model->level;
            }
        ],

        [
            'attribute' => 'category',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return $model->category;
            }
        ],

        [
            'attribute' => 'log_time',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center tdDate'],
            'value' => function ($model) {
                //return $model->log_time;
                /*$dateTime = \DateTime::createFromFormat('U.u', $model->log_time);
                $date = $dateTime->format('Y-m-d');
                $microTime = $dateTime->format('H:i:s.u');

                $result = $microTime . '<br/>' . $date;*/

                $result = null;
                $time = explode('.', $model->log_time);
                if (is_array($time) && isset($time[0])) {
                    $result = date('Y-m-d H:i:s', $time[0]);
                }
                return $result;
            }
        ],

        [
            'attribute' => 'prefix',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return $model->prefix;
            }
        ],

        [
            'attribute' => 'message',
            'format' => 'html',
            'contentOptions' => ['class' => 'text-left'],
            'value' => function ($model) {
                return '<pre>' . $model->message . '</pre>';
            }
        ],

    ],


]);