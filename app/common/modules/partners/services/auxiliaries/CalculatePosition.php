<?php

namespace common\modules\partners\services\auxiliaries;

use common\modules\users\models\User;
use frontend\modules\partners\models\Advert;
use frontend\modules\partners\models\Apartment;

/**
 * Дополнительная библиотека для пересчета позиций
 *
 * @package common\modules\partners\services\auxiliaries
 */
final class CalculatePosition extends \yii\base\BaseObject
{
    /**
     * Массив кол-во объявлений в каждом типе аренды каждого города
     * @var array
     */
    public $advertsPositions = [];

    /**
     * Массив кол-во объявлений в каждом типе аренды каждого города для топ объявлений
     * @var array
     */
    public $advertsTopPositions = [];

    /**
     * Выбранные объявления для первых позиций
     * @var array
     */
    private $_selected = [];

    /**
     * Объявление показывается в поиске
     * @param Advert $advert
     * @return bool
     */
    public function isVisible(Advert $advert)
    {
        if ($advert->apartment->visible == Apartment::VISIBLE) {
            return true;
        }

        return false;
    }

    /**
     * Объявление разрешено для поиска
     * @param Advert $advert
     * @return mixed
     */
    public function isPermitted(Advert $advert)
    {
        if ($advert->apartment->status == Apartment::ACTIVE && $advert->apartment->user->status == User::STATUS_ACTIVE) {
            return true;
        }

        return false;
    }

    /**
     * Обновить позиции выбранных объявлений объявлений
     * @param array $selected
     */
    public function setSelectedAdverts(array $selected)
    {
        $this->_selected = $selected;
    }

    /**
     * @param Advert $advert
     * @return mixed
     */
    public function &getNecessaryArray(Advert $advert)
    {
        if ($advert->top) {
            if (!isset($this->advertsTopPositions[$advert->apartment->city_id]['position'])) {
                $this->advertsTopPositions[$advert->apartment->city_id]['position'] = 1;
            }
            if (!isset($this->advertsTopPositions[$advert->apartment->city_id]['real_position'])) {
                $this->advertsTopPositions[$advert->apartment->city_id]['real_position'] = 1;
            }

            return $this->advertsTopPositions[$advert->apartment->city_id];
        }

        if (!isset($this->advertsPositions[$advert->apartment->city_id][$advert->rent_type]['position'])) {
            $this->advertsPositions[$advert->apartment->city_id][$advert->rent_type]['position'] = 1;
        }
        if (!isset($this->advertsPositions[$advert->apartment->city_id][$advert->rent_type]['real_position'])) {
            $this->advertsPositions[$advert->apartment->city_id][$advert->rent_type]['real_position'] = 1;
        }

        return $this->advertsPositions[$advert->apartment->city_id][$advert->rent_type];
    }

    /**
     * Обновить позиции объявлений
     */
    public function updateAdvertsPositions()
    {
        $this->updateFirstAdvertsPositions();

        /**
         * Делаем попакетную выборку всех объявлений
         * Определяем позицию каждого объявления исходя из города и типа аренды
         */
        $query = Advert::find()
            ->joinWith([
                'apartment' => function ($query) {
                    return $query->joinWith('user');
                }
            ])
            ->orderBy([new \yii\db\Expression('CASE WHEN position=0 THEN 1 ELSE 0 END, position')])
            ->batch(100);


        foreach ($query as $adverts) {
            foreach ($adverts as &$advert) {
                if (!empty($this->_selected) && in_array($advert->advert_id, $this->_selected)) {
                    continue;
                }

                $repository = &$this->getNecessaryArray($advert);

                /**
                 * Если объявление не разрешено к показу
                 * Сбрасываем real_position и position
                 */
                if (!$this->isPermitted($advert)) {
                    $advert->old_position = $advert->position;
                    $advert->position = 0;
                    $advert->real_position = 0;
                    $advert->save(false);
                    continue;
                }

                /**
                 * Если объявление скрыто
                 * Отмечаем real_position как текущий position
                 */
                if (!$this->isVisible($advert)) {
                    $advert->old_position = $advert->position;

                    $advert->position = $repository['position'];
                    $advert->real_position = $repository['real_position'];
                    $advert->save(false);

                    $repository['real_position']--;
                } else {

                    /**
                     * Если текущая позиция не совпадает с позицией объявления
                     * Обновляем позицию
                     */
                    if (
                        $repository['position'] != $advert->position ||
                        $repository['real_position'] != $advert->real_position
                    ) {
                        $advert->old_position = $advert->position;
                        $advert->position = $repository['position'];
                        $advert->real_position = $repository['real_position'];
                        $advert->save(false);
                    }
                }

                $repository['position']++;
                $repository['real_position']++;
            }
        }
    }

    /**
     * Вначале обновить первые указанные объявляния
     */
    private function updateFirstAdvertsPositions()
    {
        if (!empty($this->_selected)) {
            $adverts = Advert::find()
                ->joinWith(['apartment'])
                ->where(['advert_id' => $this->_selected])
                ->all();

            // Обходим выбранные объявления и присваиваем им первые позиции
            foreach ($adverts as &$advert) {
                $advArray = &$this->getNecessaryArray($advert);

                $advert->old_position = $advert->position;
                $advert->position = $advArray['position'];
                $advert->real_position = $advArray['real_position'];
                $advert->save(false);

                $advArray['position']++;
                $advArray['real_position']++;
            }
        }
    }
}
