<?php

namespace common\modules\office\controllers\frontend;

use Yii;
use yii\web\Response;
use common\modules\users\models\UsersList;
use frontend\modules\partners\models as models;

/**
 * Ajax контроллер офиса
 * @package common\modules\office\controllers\frontend
 */
class AjaxController extends \frontend\components\Controller
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
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Социальные возможности
     * @return array|Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionSocial()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goHome();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $usersList = UsersList::find()->where([
            'user_id' => Yii::$app->user->id,
            'interlocutor_id' => Yii::$app->request->post('interlocutor')
        ])->one();

        if ($usersList) {
            if (Yii::$app->request->post('type') == 'bookmark') {
                if ($usersList->type == UsersList::BOOKMARK) {
                    if ($usersList->delete()) {
                        return [
                            'status' => 1,
                            'message' => 'Пользователь успешно удален из списка избранных'
                        ];
                    }
                } else {
                    $usersList->type = UsersList::BOOKMARK;
                    if ($usersList->save()) {
                        return [
                            'status' => 1,
                            'message' => 'Пользователь успешно добавлен в список избранных'
                        ];
                    }
                }
            }

            if (Yii::$app->request->post('type') == 'blacklist') {
                if ($usersList->type == UsersList::BLACKLIST) {
                    if ($usersList->delete()) {
                        return [
                            'status' => 1,
                            'message' => 'Пользователь успешно удален из списка заблокированных'
                        ];
                    }
                } else {
                    $usersList->type = UsersList::BLACKLIST;
                    if ($usersList->save()) {
                        return [
                            'status' => 1,
                            'message' => 'Пользователь успешно добавлен в список заблокированных'
                        ];
                    }
                }
            }

            if (Yii::$app->request->post('type') == 'remove') {
                if ($usersList->delete()) {
                    return [
                        'status' => 1,
                        'message' => 'Пользователь успешно удален из списка'
                    ];
                }
            }
        }

        if (!$usersList) {
            $usersList = new UsersList;
            $usersList->user_id = Yii::$app->user->id;
            $usersList->interlocutor_id = Yii::$app->request->post('interlocutor');
            if (Yii::$app->request->post('type') == 'bookmark') {
                $usersList->type = UsersList::BOOKMARK;
                if ($usersList->save()) {
                    return [
                        'status' => 1,
                        'message' => 'Пользователь успешно добавлен в список избранных'
                    ];
                }
            }
            if (Yii::$app->request->post('type') == 'blacklist') {
                $usersList->type = UsersList::BLACKLIST;
                if ($usersList->save()) {
                    return [
                        'status' => 1,
                        'message' => 'Пользователь успешно добавлен в список заблокированных'
                    ];
                }
            }
        }

        return [
            'status' => 0,
            'message' => 'Возникла критическая ошибка'
        ];
    }

    /**
     * Удаление рекламы Top-slider
     *
     * @param $advertisement_id
     * @return array|Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteTopSlider($advertisement_id)
    {
        if (!Yii::$app->request->isAjax) {
            return $this->goHome();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $advertisementSlider = models\AdvertisementSlider::find()->where([
            'advertisement_id' => $advertisement_id,
            'user_id' => Yii::$app->user->id
        ])->one();

        if ($advertisementSlider && $advertisementSlider->delete()) {
            return [
                'status' => 1,
                'message' => 'Рекламное объявление успешно удалено.'
            ];
        }

        return [
            'status' => 0,
            'message' => 'Возникла критическая ошибка.'
        ];
    }
}
