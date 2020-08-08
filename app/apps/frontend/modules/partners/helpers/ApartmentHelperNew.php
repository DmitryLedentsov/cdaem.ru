<?php

namespace frontend\modules\partners\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidParamException;
use common\modules\partners\models\Service;
use common\modules\partners\models\Apartment;

/**
 * Class ApartmentHelper
 * @package frontend\modules\partners\helpers
 */
class ApartmentHelperNew
{
    /**
     * Возвращает адресс апартамента
     * @param Apartment $model
     * @return string
     */
    public static function getAddress(Apartment $model)
    {
        if ($model->city) {
            $result = [];
            $result[] = $model->address;
            $result[] = $model->city->name;
            if ($model->city->country) {
                $result[] = $model->city->country->name;
            }

            return implode(', ', $result);
        }

        return '<div>Адрес не указан</div>';
    }

    public static function getPositions(Apartment $model)
    {
        if ($model->adverts) {
            $positions = '';
            foreach ($model->adverts as $advert) {
                $positions = Yii::$app->getView()->renderDynamic('if ($positions = \\common\\modules\\partners\\models\\Advert::find()->select("real_position")->where(["advert_id" => ' . $advert->advert_id . '])->scalar()) { return $positions; } else {return "pos";} ');
            }
        }

        return $positions;
    }

    /**
     * Возвращает информация апартамента
     * @param Apartment $model
     * @return string
     */
    public static function getInfo(Apartment $model)
    {
        if ($model->status == Apartment::ACTIVE) {
            if ($model->visible == Apartment::VISIBLE) {
                $status = '<b class="color-success">Активен</b>';
            } else {
                $status = '<b class="color-closed">Скрыт</b>';
            }
        } else {
            $status = '<b class="color-danger">На модерации</b>';
        }

        $contacts = $model->open_contacts ? '<span class="color-success">открыты</span>' : '<span class="color-closed">закрыты <span class="no-payment">' . Html::a('(Не оплачено)', ['/office/default/services', '#' => Service::SERVICE_APARTMENT_CONTACTS_OPEN]) . '</span></span>';

        $result = '<p>Комнат: <span class="color-info">' . $model->total_rooms . '</span></p>';
        $result .= '<p>Спальных мест: <span class="color-info">' . ArrayHelper::getValue($model->bedsList, $model->beds) . '</span></p>';
        $result .= '<p>Общая площадь: <span class="color-info">' . $model->total_area . ' м<sup>2</sup></span></p>';
        $result .= '<p><span class="color-info">' . $model->remontName . '</span></p>';
        $result .= '<p>Статус: ' . $status . '</p>';
        $result .= '<p>Контакты: ' . $contacts . '</p>';
        if (!$model->open_contacts) {
            $result .= '<p class="color-success">Доступно бронирование</p>';
        }

        return $result;
    }

    /**
     * Возвращает список объявлений апартамента
     * @param Apartment $model
     * @return string
     */
    public static function getAdverts(Apartment $model)
    {
        if ($model->adverts) {
            $result = '';
            foreach ($model->adverts as $advert) {
                $result .= '<p>';
                $result .= '<span class="advert-name">';
                $result .= Html::a($advert->rentType->name, ['/partners/default/view', 'id' => $advert->advert_id, 'city' => $advert->apartment->city->name_eng]);
                $result .= '<span class="' . ($advert->top ? 'top color-success' : 'top color-danger') . '">' . ($advert->top ? 'В топе' : 'Не в топе') . '</span>';
                $result .= '</span>';
                $position = Yii::$app->getView()->renderDynamic('if ($position = \\common\\modules\\partners\\models\\Advert::find()->select("real_position")->where(["advert_id" => ' . $advert->advert_id . '])->scalar()) { return $position; } else {return "<span style=\'color: silver\' title=\'Расчет позиции\'>Р</span>";} ');
                $result .= '<span class="color-info">' . $position . ' позиция</span>';
                $result .= '<p>';
            }

            return $result;
        } else {
            return '<p>Объявления не созданы</p>';
        }
    }
}
