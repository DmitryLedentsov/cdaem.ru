<?php

namespace common\modules\messages\controllers\frontend;

use common\modules\messages\models\frontend\MailboxSearch;
use common\modules\messages\models\Mailbox;
use common\modules\messages\models\Message;
use common\modules\users\models\User;
use common\modules\users\models\UsersList;
use frontend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use yii\web\Response;
use Yii;

/**
 * Class DefaultController
 * @package common\modules\messages\controllers\frontend
 */
class DefaultController extends Controller
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
                        'roles' => ['@']
                    ],
                ]
            ],
        ];

        if (YII_DEBUG) {
            return $behaviors;
        } else {
            return array_merge($behaviors, require(__DIR__ . '/../../caching/default.php'));
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->module->viewPath = '@common/modules/messages/views/frontend';
            return true;
        } else {
            return false;
        }
    }

    /**
     * Все сообщения
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MailboxSearch();
        $dataProvider = $searchModel->conversationsSearch(Yii::$app->request->queryParams);

        return $this->render('index.twig', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Чат с собеседником
     * @param $interlocutorId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($interlocutorId)
    {
        $interlocutor = $this->findInterlocutor($interlocutorId);

        $usersList = UsersList::findOne(['user_id' => Yii::$app->user->id, 'interlocutor_id' => $interlocutorId]);

        $searchModel = new MailboxSearch();
        $dataProvider = $searchModel->dialogSearch(['interlocutor_id' => $interlocutorId]);

        foreach ($dataProvider->models as $mailbox) {
            $mailbox->readMessage();
        }

        return $this->render('view.twig', [
            'interlocutor' => $interlocutor,
            'dataProvider' => $dataProvider,
            'usersList' => $usersList,
        ]);
    }

    /**
     * Написать сообщение
     * @param $interlocutorId
     * @return array
     */
    public function actionCreate($interlocutorId)
    {
        $model = new Message(['scenario' => 'create']);
        $model->interlocutor_id = $interlocutorId;

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());

            $errors = ActiveForm::validate($model);

            if (!$errors) {
                if (Yii::$app->request->post('submit')) {
                    if ($model->save(false)) {
                        return [
                            'status' => 1,
                            'message' => 'Сообщение отправлено',
                        ];
                    } else {
                        return [
                            'status' => 0,
                            'message' => 'Возникла критическая ошибка',
                        ];
                    }
                }
                return [];
            }
            return $errors;
        }
    }

    /**
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    protected function findInterlocutor($id)
    {
        $model = User::find()
            ->where([User::tableName() . '.id' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Такого пользователя несуществует');
        } elseif ($model->isBanned()) {
            $this->readAllMessages($model->id);
            throw new NotFoundHttpException('Такого пользователя несуществует');
        } else {
            return $model;
        }
    }

    protected function readAllMessages($id)
    {
        Mailbox::updateAll(['read' => 1], ['user_id' => Yii::$app->user->id, 'interlocutor_id' => $id]);
    }
}
