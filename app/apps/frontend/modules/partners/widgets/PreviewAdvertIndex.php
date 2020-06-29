<?php

namespace frontend\modules\partners\widgets;

use yii\helpers\Html;
use yii\base\Widget;
use Yii;
use yii\bootstrap;

/**
 * Виджет отображает блок объявления на главной странице сайта
 */
class PreviewAdvertIndex extends Widget
{
    /**
     * @var \frontend\modules\partners\models\Advert
     */
    public $advert;

    /**
     * @var string
     */
    private $_rentTypes;

    /**
     * @var string
     */
    private $_address;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->advert->apartment->metroStation) {
            $this->_address = $this->advert->apartment->address;
        } else {
            $this->_address = 'м. ' . $this->advert->apartment->metroStation->metro->name;
        }

        foreach ($this->advert->apartment->adverts as $advert) {

            $icon = '';
            if ($advert->rentType['icons']) {
                $icons = @json_decode($advert->rentType['icons'], true);
                if (isset($icons['small-blue'])) {
                    $icon = $icons['small-blue'];
                }
            }

            $active = '';
            if ($this->advert['rent_type'] == $advert['rent_type']) {
                $active = ' active';
            }
            
             $href = Yii::$app->urlManager->createUrl([
            '/partners/default/view',
            'id' => $advert->advert_id,
            'city' => $advert->apartment->city->name_eng
        ]);

            $this->_rentTypes .= Html::tag('div', $icon . '<a href=" '. $href  .'">' . '<span class="price">' . $advert->priceText . '</span></a>', [
                'class' => 'item' . $active,
                'title' => $advert->rentType['name']
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $href = Yii::$app->urlManager->createUrl([
            '/partners/default/view',
            'id' => $this->advert->advert_id,
            'city' => $this->advert->apartment->city->name_eng
        ]);

        // Yii::t('app', '{n, plural, =0{нет комнат} one{# комната} =5{# комнат}  few{# комнаты} many{# комнат} other{# комнаты} }', ['n' => $this->advert->apartment->total_rooms])

        $online = '';
        if ($this->advert->apartment->user->isOnline()) {
            $online = '<span class="online" title="Владелец сейчас на сайте"></span>';
        }

        $now_available = '';
        if ($this->advert->apartment->now_available) {
            $now_available = 'Сейчас свободно';
        }

        $selected =  $this->advert->selected ? ' selected' : '';
        $contacts =  $this->advert->apartment->open_contacts ? '<div class="contacts" title="Контакты открыты"></div>' : '';
        if(!$this->advert->apartment->open_contacts && $this->advert->apartment->user->checkActivityIfOlderThan('month', 1)) {
            $contacts = '<div class="contacts" title="Контакты открыты"></div>';
        }

        return ('
            <div class="apartment-board col-sm-6 col-xs-6 col-md-3'.$selected.'">
                
                <span class="res-info">' . $now_available . '</span>
                    <div class="image">
                    <a href="' . $href . '" class="board-inside-fullink"></a>
                        ' . $this->previewTitleImage . '
                        ' . $online . '
                        
                        <div class="city"><span>' . $this->advert->apartment->city->name . '</span></div>
                        ' . $contacts . '
                        <div class="address"><span>' . $this->_address . '</span></div>
                    </div>
                    <div class="info">
                        ' . $this->_rentTypes . '
                    </div>
                
            </div>
        ');
    }

    /**
     * Возвращает превью изображение заглавной картинки с необходимыми атрибутами alt и title
     * @return string
     */
    protected function getPreviewTitleImage()
    {
        $rentTypeName = $this->advert->rentType->name;

        $title = $this->advert->apartment->city->name . ', ';
        if ($this->advert->apartment->metroStation) {
            $title .= 'м.' . $this->advert->apartment->metroStation->metro->name;
        } else {
            $title .= $this->advert->apartment->address;
        }
        $alt = $this->advert->rentType->name;
        $alt .= ' ' . $title;

        return Html::img($this->advert->apartment->titleImageSrc, [
            'alt' => $alt,
            'title' => $title,
        ]);
    }
}
