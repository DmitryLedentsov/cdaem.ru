<?php
/**
 * Объявления пользователя
 * @var yii\base\View $this Представление
 * @var array $rentTypeslist Типы аренды
 * @var array $models common\modules\partners\models\Advert
 * @var bool $isOnModerationExist Есть ли объявления на модерации
 */

use yii\helpers\Html;
use common\modules\partners\widgets\frontend\PreviewAdvertTmp;

if ($models) {
    $result = '';
    foreach ($models as $advert) {
        $PreviewAdvert = PreviewAdvertTmp::widget([
            'advert' => $advert,
        ]);
        $result .= Html::tag('div', $PreviewAdvert, [
            'class' => 'item'
        ]);
    }

    echo Html::tag('div', $result, [
        'class' => 'apartment-list clearfix'
    ]);
} else {
    echo Html::tag('div', 'Вы еще не добавили ни одного объявления. ' . Html::a('Добавить', ['/office/apartment/create']), [
        'class' => 'alert alert-info'
    ]) . ($isOnModerationExist ? Html::tag('div', 'Есть объявления на модерации', ['class' => 'alert alert-warning'])
            : '');
}
