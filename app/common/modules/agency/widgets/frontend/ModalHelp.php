<?php

namespace common\modules\agency\widgets\frontend;

use Yii;

/**
 * Class ModalHelp
 * @package common\modules\agency\widgets\frontend
 */
class ModalHelp extends \yii\base\Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->getModal();
    }

    /**
     * @return string
     */
    protected function getModal()
    {
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
