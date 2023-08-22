<?php

namespace common\modules\agency\controllers\frontend;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\db\Exception;
use yii\web\Response;
use common\modules\geo\models\City;
use common\modules\geo\models\Metro;
use common\modules\agency\models\Advert;
use common\modules\partners\models\Advert as AdvertPartners;
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
    public function beforeAction($action): bool
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
}
