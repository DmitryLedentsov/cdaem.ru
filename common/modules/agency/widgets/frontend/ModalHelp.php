<?php

namespace common\modules\agency\widgets\frontend;

use yii\bootstrap\Modal;
use common\modules\agency\models\SpecialAdvert;
use common\modules\agency\models\Advert;
use yii\helpers\Html;
use Yii;


class ModalHelp extends \yii\base\Widget {

    public function init() {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run() {

        return $this->getModal();
    }

 
    protected function getModal() {
        return ('
            <div class="apartment-agency">
                
                    <div class="header">
                        <div class="address"><span>' . $this->_district . ' ' . $this->_metro . '</span></div>
                        <div class="rooms">' . Yii::t('app', '{n, plural, =0{нет комнат} one{# комната} =5{# комнат}  few{# комнаты} many{# комнат} other{# комнаты} }', ['n' => $this->advert->apartment->total_rooms]) . '</div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="image">
                    <a href="' . $href . '" class="inside-fullink"></a>
                        ' . $this->getPreviewTitleImage() . '
                        <div class="group clearfix">
                            
                            ' . $type . '
                        </div>
                        <div class="special-text">
                        ' . $this->_specialText . '
                        </div>
                        <div class="metro">
                            Метро: ' . $this->_metroAll . '
                        </div>
                    </div>
                
            </div>
        ');
       
    }

  
}
