<?php

namespace common\modules\messages\controllers\frontend;

use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\web\NotFoundHttpException;
use frontend\components\Controller;
use common\modules\users\models\User;
use common\modules\users\models\UsersList;
use common\modules\messages\models\Mailbox;
use common\modules\messages\models\Message;
use common\modules\messages\models\frontend\MailboxSearch;

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
        }

        return array_merge($behaviors, require(__DIR__ . '/../../caching/default.php'));
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action): bool
    {
        if (parent::beforeAction($action)) {
            $this->module->viewPath = '@common/modules/messages/views/frontend';

            return true;
        }

        return false;
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
                    }

                    return [
                        'status' => 0,
                        'message' => 'Возникла критическая ошибка',
                    ];
                }

                return [];
            }

            return $errors;
        }

        return [];
    }

    /**
     * @param $id
     * @return array|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findInterlocutor($id)
    {
        $model = User::find()
            ->where([User::tableName() . '.id' => $id])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Такого пользователя не существует');
        }

        if ($model->isBanned()) {
            $this->readAllMessages($model->id);

            throw new NotFoundHttpException('Такого пользователя не существует');
        }

        return $model;
    }

    /**
     * @param $id
     */
    protected function readAllMessages($id)
    {
        Mailbox::updateAll(['read' => 1], ['user_id' => Yii::$app->user->id, 'interlocutor_id' => $id]);
    }
}
