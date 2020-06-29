<?php

namespace frontend\modules\partners\models\form;

use frontend\modules\partners\models\Advert;
use yii\validators\ExistValidator;
use Yii;

/**
 * @inheritdoc
 */
class AdvertForm extends \frontend\modules\partners\models\Advert
{
    /**
     * Список новых объявлений
     * @var array
     */
    public $newAdverts = [];

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            'user-create' => self::OP_ALL,
            'user-update' => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'user-create' => ['rent_type', 'price', 'currency'],
            'user-update' => ['rent_type', 'price', 'currency'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            // В случае, если пользователь при обновлении апартамента убрал все активные чекбоксы с объявлений
            // запрос не перезапишет свойство $this->rent_type и оно будет содержать предыдущие объявления.
            // В ходе этого будет небольшой баг, - при снятии активных чекбоксов не будет никаких ошибок и при этом
            // при сохранении апартамента останется одно последнее объявление.
            // Чтобы этого избежать и показать ошибку пользователю, обнулим свойство $this->rent_type, если в
            // запросе не было передано rent_type
            if (!isset(Yii::$app->request->post(self::formName())['rent_type']) || !is_array(Yii::$app->request->post(self::formName())['rent_type'])) {
                $this->rent_type = null;
            }

            // Проверить и собрать все типы аренды, которые необходимо сохранить или обновить
            if ($this->rent_type && is_array($this->rent_type)) {

                $result = [];

                foreach ($this->rent_type as $rentTypeId) {
                    if (isset($this->price[$rentTypeId]) && isset($this->currency[$rentTypeId])) {
                        $result[] = [
                            'rent_type_id' => $rentTypeId,
                            'price' => $this->price[$rentTypeId],
                            'currency' => $this->currency[$rentTypeId],
                        ];
                    }
                }

                foreach ($result as $advert) {
                    if (!$this->validateAdvert($advert)) {
                        $this->addError('type[' . $advert['rent_type_id'] . ']', 'Данные заполнены некорректно');
                    }
                }

                $this->newAdverts = $result;

            } else {
                $this->addError('rent_type', 'Укажите объявления для недвижимости');
            }

            return true;
        }

        return false;
    }

    /**
     * Валидация объявления
     * @param array $advert
     * @return bool
     */
    public function validateAdvert(array $advert)
    {
        // Проверяем тип аренды
        $ExistValidator = new ExistValidator();
        $ExistValidator->targetClass = '\common\modules\realty\models\RentType';
        $ExistValidator->targetAttribute = 'rent_type_id';

        if (!$ExistValidator->validate($advert['rent_type_id'])) {
            return false;
        }

        if (!in_array($advert['currency'], array_keys(self::getCurrencyList()))) {

            return false;
        }

        if (!$advert['price'] || !is_numeric($advert['price'])) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            //echo 1;

            //die();

            return true;
        }

        return false;
    }
}
