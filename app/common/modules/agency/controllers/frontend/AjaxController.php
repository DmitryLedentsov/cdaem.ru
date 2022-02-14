<?php

namespace common\modules\agency\controllers\frontend;

use common\modules\partners\models\Advert as AdvertPartners;
use common\modules\agency\models\Advert;
use common\modules\geo\models\Metro;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\Response;
use common\modules\geo\models\City;
use common\modules\agency\models\Apartment as ApartmentAgency;
use common\modules\partners\models\frontend\Apartment as ApartmentPartners;

/**
 * Ajax гео контроллер
 * @package common\modules\geo\controllers\frontend
 */
class AjaxController extends \frontend\components\Controller
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->enableCsrfValidation = false;
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['X-Request-With'],
                ],
            ],
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
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!Yii::$app->request->isAjax) {
            return $this->goBack();
        }

        $this->module->viewPath = '@common/modules/agency/views/frontend';

        return true;
    }

    // Обновляем цены объявления по типу аренды
    public function actionUpdatePrice() {
        // todo убережёт ли эта проверка от обновления цены по id чужого объявления
        if (!Yii::$app->user->can('agency-advert-view')) { // todo почему на update проверяется право view?
            throw new ForbiddenHttpException(Yii::t('users.rbac', 'ACCESS_DENIED'));
        }

        $paramList = Yii::$app->request->getBodyParams();

        $advertId = $paramList['advert-id'];
        $rentPrice = $paramList['rent-price'];
        $isApplyForAll = isset($paramList['apply-for-all']) ? true : false;

        // dd($paramList, $advertId, $rentPrice, $isApplyForAll);

        $advert = AdvertPartners::findOne($advertId);

        if (!$advert) {
            // throw new Exception('test');
            Yii::$app->session->setFlash('danger', 'Не найдено объявление'); // Не работает
            $this->redirect('/office/apartments');

            // вернуть ошибку так, чтобы кастомный ajax-обработчик её показал
            // return json_encode([
            // 'status' => 'success',
            // 'message' => "Не найдено объявление",
            // 'data' => ''
            // ]);
        }

        $advert->price = $rentPrice;
        $advert->save(false);

        if ($isApplyForAll) {
            // Применяем цену для всех типов аренды
            $advertList = AdvertPartners::findAll(['apartment_id' => $advert->apartment_id]);

            foreach ($advertList as $advert) {
                $advert->price = $rentPrice;
                $advert->save(false);
            }
        }

        $this->redirect('/office/apartments');
    }
}
