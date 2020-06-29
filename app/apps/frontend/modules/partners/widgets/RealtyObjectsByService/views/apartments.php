<?php
/**
 * Объекты пользователя
 * @var yii\base\View $this Представление
 * @var array $rentTypeslist Типы аренды
 * @var array $models frontend\modules\partners\models\Apartments
 */

use frontend\modules\partners\widgets\PreviewAdvertTmp;
use yii\helpers\Html;


if ($models) {

    $result = '';
    foreach ($models as $apartment) {
        $PreviewAdvert = PreviewAdvertTmp::widget([
            'apartment' => $apartment,
        ]);
        $result .= Html::tag('div', $PreviewAdvert, [
            'class' => 'item'
        ]);
    }

    echo Html::tag('div', $result, [
        'class' => 'apartment-list clearfix'
    ]);

} else {

    echo Html::tag('div', 'Вы еще не добавили ни одного объявления. ' . Html::a('Добавить', ['/partners/default/create']), [
        'class' => 'alert alert-info'
    ]);
}

