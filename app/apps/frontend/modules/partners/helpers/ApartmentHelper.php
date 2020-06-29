<?php

namespace frontend\modules\partners\helpers;

use common\modules\partners\models\Apartment;
use common\modules\partners\models\Service;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use Yii;

/**
 * Class ApartmentHelper
 * @package frontend\modules\partners\helpers
 */
class ApartmentHelper
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
    
    public static function getInfoNew(Apartment $model)
    {
        
        $contacts = $model->open_contacts ? '<span class="color-success">открыты</span>' : '<span class="color-closed-2">закрыты <span class="no-payment-2">' . Html::a('(Оплатить)', ['/office/default/services', '#' => Service::SERVICE_APARTMENT_CONTACTS_OPEN]) . '</span></span>';
         $result = '<p><span class="opencontact">Контакты:</span><span class="opencontactstatus">' . $contacts . '</span></p>';
        if (!$model->open_contacts) {
            $result .= '<p class="bronopen"><span class="bronopen_title">Бронирование</span>: <span class="bronopen_status">Открыто</span></p>';
        }else
        {
             $result .= '<p class="bronopen"><span class="bronopen_title">Бронирование</span>: <span class="bronopen_status" style="color:red;">Закрыто</span></p>';
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
    
     
    
    
       public static function getAdvertsNew(Apartment $model)
    {
            $mypos;
            
           if ($model->status == Apartment::ACTIVE) {
            if ($model->visible == Apartment::VISIBLE) {
                $status = '<b class="color-success">Активен</b>';
            } else {
                $status = '<b class="color-closed">Скрыт</b>';
            }
        } else {
            $status = '<b class="color-danger">На модерации</b>';
        }
        if ($model->adverts) {
            $result = '';
            
            foreach ($model->adverts as $advert) {
             
                 if ($advert->position < $advert->old_position) {
                $positionClass = 'up';
                $positionText = 'Позиция поднята';
            } else if ($advert->position > $advert->old_position) {
                $positionClass = 'down';
                $positionText = 'Позиция опущена';
            } else {
                $positionClass = 'no-change';
                $positionText = 'Без изменений';
            }        
                        
              $mypos = Html::tag('div', '', [
                'class' => 'position advertinfo-pos ' . $positionClass,
                'title' => $positionText,
            ]);  
              
              
                
               $result .= '<div class="row advertinfo-row">';
               
                $result .= '<span class="statushelper_title">Статус: ' . $status . '</span>';
                
                $position = Yii::$app->getView()->renderDynamic('if ($position = \\common\\modules\\partners\\models\\Advert::find()->select("real_position")->where(["advert_id" => ' . $advert->advert_id . '])->scalar()) { return $position; } else {return "<span style=\'color: silver\' title=\'Расчет позиции\'>Р</span>";} ');
                
                $result .= '<span class="advertposition_info">' . $position . ' позиция</span>';
                $result .= '<span class="advert-name-new">';
                $result .= '<span class="possition_name ' . ($advert->top ? 'top color-success' : 'top color-danger') . '">' . ($advert->top ? '(В топе)' : '(Не в топе)') . '</span>';
                $result .= '</span>';
                $result .= $mypos;
                $result .= Html::a($advert->rentType->name, ['/partners/default/view', 'id' => $advert->advert_id, 'city' => $advert->apartment->city->name_eng], $options = ['class' => 'rentypelink'] );
                
                $result .= '</div>';
                
            }
            return $result;
        } else {
            return '<p>Объявления не созданы</p>';
        }
    }
    
    
    
        public static function getAdvertsMini(Apartment $model)
    {
            $mypos;
            
       
        if ($model->adverts) {
            $result = '';
            $i = 0;
            foreach ($model->adverts as $advert) {
             if($advert->position > $advert->old_position ){
             if ($advert->position > $advert->old_position) {
                $positionClass = 'down-new';
                $positionText = 'Позиция опущена';
            }         
                        
              $mypos = Html::tag('div', '', [
                'class' => 'position advertinfo-pos ' . $positionClass,
                'title' => $positionText,
            ]);            
                $result .= '<div class="advertinfo-row" style="margin-top:-5px;padding:0px;border-bottom:0px;display:inline-block;">';    
                $position = Yii::$app->getView()->renderDynamic('if ($position = \\common\\modules\\partners\\models\\Advert::find()->select("real_position")->where(["advert_id" => ' . $advert->advert_id . '])->scalar()) { return $position; } else {return "<span style=\'color: silver\' title=\'Расчет позиции\'>Р</span>";} ');     
                $result .= '<span class="advertposition_info">' . $position . '</span>';   
                
                $result .= '</span>';
                $result .= $mypos;   
                $result .= '</div>';
                $i++;
                }else if($advert->position == $advert->old_position){
                    $positionClass = 'no-change-new';
                $positionText = 'Без изменений';
                     $mypos = Html::tag('div', '', [
                'class' => 'position ' . $positionClass,
                'title' => $positionText,
            ]);            
                $result .= '<div class="advertinfo-row" style="margin-top:-5px;padding:0px;border-bottom:0px;display:inline-block;">';    
                $position = Yii::$app->getView()->renderDynamic('if ($position = \\common\\modules\\partners\\models\\Advert::find()->select("real_position")->where(["advert_id" => ' . $advert->advert_id . '])->scalar()) { return $position; } else {return "<span style=\'color: silver\' title=\'Расчет позиции\'>Р</span>";} ');     
                $result .= '<span class="advertposition_info">' . $position . '</span>';   
                
                $result .= '</span>';
                $result .= $mypos;   
                $result .= '</div>';
                $i++;
                }
                else if($advert->position < $advert->old_position){
                    $positionClass = 'up-new';
                $positionText = 'Позиция поднята';
                     $mypos = Html::tag('div', '', [
                'class' => 'position ' . $positionClass,
                'title' => $positionText,
            ]);            
                $result .= '<div class="advertinfo-row" style="margin-top:-5px;padding:0px;border-bottom:0px;display:inline-block;">';    
                $position = Yii::$app->getView()->renderDynamic('if ($position = \\common\\modules\\partners\\models\\Advert::find()->select("real_position")->where(["advert_id" => ' . $advert->advert_id . '])->scalar()) { return $position; } else {return "<span style=\'color: silver\' title=\'Расчет позиции\'>Р</span>";} ');     
                $result .= '<span class="advertposition_info">' . $position . '</span>';   
                
                $result .= '</span>';
                $result .= $mypos;   
                $result .= '</div>';
                $i++;
                }
            }
            return $result;
        } else {
            return '<p>Объявления не созданы</p>';
        }
    }
    
    
    
    

}
