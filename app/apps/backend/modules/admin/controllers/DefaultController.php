<?php

namespace backend\modules\admin\controllers;

use common\modules\callback\models\Callback;
use common\modules\agency\models\WantPass;
use common\modules\agency\models\Select;
use common\modules\agency\models\DetailsHistory;
use common\modules\agency\models\Reservation;
use common\modules\helpdesk\models\Helpdesk;
use backend\modules\admin\models\LogSearch;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;

/**
 * Default Controller
 * @package backend\modules\admin\controllers
 */
class DefaultController extends \backend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [

                    [
                        'actions' => ['index', 'get-info', 'log', 'test'],
                        'allow' => true,
                        'roles' => Yii::$app->getModule('users')->accessGroupsToControlpanel,
                    ],
                    [
                        'actions' => ['error'],
                        'allow' => true,
                        'roles' => ['?', '@']
                    ],
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
                'view' => '@backend/themes/basic/default/error',
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $callbacks = Callback::find()
            ->where(['active' => Callback::UNPROCESSED])
            ->orderBy(['date_create' => SORT_DESC])
            ->limit(10)
            ->all();

        $wantPasses = WantPass::find()
            ->where(['status' => WantPass::UNPROCESSED])
            ->orderBy(['apartment_want_pass_id' => SORT_DESC])
            ->limit(10)
            ->all();

        $selects = Select::find()
            ->where(['status' => Select::UNPROCESSED])
            ->orderBy(['apartment_select_id' => SORT_DESC])
            ->limit(10)
            ->all();

        $helpdesks = Helpdesk::find()
            ->where(['close' => Helpdesk::AWAITING])
            ->orderBy(['date_create' => SORT_DESC])
            ->limit(10)
            ->all();

        $agencyReservations = Reservation::find()
            ->with(['apartment' => function ($query) {
                $query->with('titleImage');
            }])
            ->where(['processed' => Reservation::UNPROCESSED])
            ->orderBy(['date_create' => SORT_DESC])
            ->limit(10)
            ->all();

        $detailsHistory = DetailsHistory::find()
            ->with(['advert' => function ($query) {
                $query->with(['apartment' => function ($query) {
                    $query->with('titleImage');
                }]);
            }])
            ->where(['processed' => 0])
            ->orderBy(['date_create' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('index', [
            'callbacks' => $callbacks,
            'wantPasses' => $wantPasses,
            'selects' => $selects,
            'helpdesks' => $helpdesks,
            'detailsHistory' => $detailsHistory,
            'agencyReservations' => $agencyReservations,
            'callbacks_count' => Callback::find()->where(['active' => Callback::UNPROCESSED])->count(),
            'wantPasses_count' => WantPass::find()->where(['status' => WantPass::UNPROCESSED])->count(),
            'selects_count' => Select::find()->where(['status' => Select::UNPROCESSED])->count(),
            'helpdesks_count' => Helpdesk::find()->where(['close' => Helpdesk::AWAITING])->count(),
            'agencyReservations_count' => Reservation::find()->where(['processed' => Reservation::UNPROCESSED])->count(),
            'detailsHistory_count' => DetailsHistory::find()->where(['processed' => 0])->count(),
        ]);
    }

    /**
     * @return string
     */
    public function actionLog()
    {
        $searchModel = new LogSearch(['scenario' => 'search']);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('log', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array
     */
    public function actionGetInfo()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goHome();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $array = [];

        if (Yii::$app->user->can('callback-update')) {
            $array['callbacks'] = [
                'count' => Callback::find()->where(['active' => Callback::UNPROCESSED])->count(),
                'message' => 'Заявки на "Обратный звонок"',
            ];
        }

        if (Yii::$app->user->can('agency-want-pass-update')) {
            $array['wantPasses'] = [
                'count' => WantPass::find()->where(['status' => WantPass::UNPROCESSED])->count(),
                'message' => 'Заявки на "Хочу сдать"',
            ];
        }

        if (Yii::$app->user->can('agency-select-update')) {
            $array['selects'] = [
                'count' => Select::find()->where(['status' => Select::UNPROCESSED])->count(),
                'message' => 'Заявки на "Подберем квартиру"',
            ];
        }

        if (Yii::$app->user->can('helpdesk-answer')) {
            $array['helpdesks'] = [
                'count' => Helpdesk::find()->where(['close' => Helpdesk::AWAITING])->count(),
                'message' => 'Обращение в "Техническую поддержку" ',
            ];
        }

        if (Yii::$app->user->can('agency-reservation-update')) {
            $array['agencyReservations'] = [
                'count' => Reservation::find()->where(['processed' => Reservation::UNPROCESSED])->count(),
                'message' => 'Заявки на бронирование',
            ];
        }

        if (Yii::$app->user->can('agency-details-history-update')) {
            $array['detailsHistory'] = [
                'count' => DetailsHistory::find()->where(['processed' => 0])->count(),
                'message' => 'Заявки на "Отправку реквизитов"',
            ];
        }

        $array['promotion'] = array_sum(ArrayHelper::getColumn($array, 'count')) > 0 ? 1 : 0;

        return $array;
    }

}
