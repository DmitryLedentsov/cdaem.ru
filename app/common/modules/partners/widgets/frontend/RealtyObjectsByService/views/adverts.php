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

$isAdvertExist = $models || $isOnModerationExist;

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
    ]) . ($isOnModerationExist ? Html::tag('div', 'Есть объявления на модерации', ['class' => 'alert alert-warning']) : '');
} else {
    if ($isAdvertExist) {
        // Все объявления находятся на моредации
        echo Html::tag('div', 'Есть объявления на модерации', ['class' => 'alert alert-warning']);
    } else {
        echo Html::tag('div', 'Вы еще не добавили ни одного объявления. ' . Html::a('Добавить', ['/office/apartment/create']), [
            'class' => 'alert alert-info'
        ]);
    }
}
