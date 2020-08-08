<?php

namespace common\modules\reviews\widgets\frontend;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use common\modules\reviews\models\Review;
use common\modules\reviews\models\ReviewForm;
use common\modules\reviews\models\ReviewSearch;

/**
 * Show Reviews
 * @package common\modules\reviews\widgets\frontend
 */
class ShowReviews extends Widget
{
    /**
     * Идентификатор апартаментов
     * @var int
     */
    public $apartmentId;

    /**
     * Текущая страница пагинации
     * @var int
     */
    public $currentPage = 1;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $countReviews = Review::getCountReviewsToApartment($this->apartmentId);
        $searchModel = new ReviewSearch();
        $formModel = new ReviewForm();
        $formModel->scenario = Yii::$app->user->id ? 'user-create' : 'guest-create';
        $formModel->apartment_id = $this->apartmentId;

        $dataProvider = $searchModel->search(['apartment_id' => $this->apartmentId]);

        return $this->render('../../../views/frontend/default/_reviews-apartment.twig', [
            'countReviews' => $countReviews,
            'formModel' => $formModel,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'apartment_id' => $this->apartmentId
        ]);
    }
}
