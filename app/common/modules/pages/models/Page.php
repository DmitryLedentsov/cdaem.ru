<?php

namespace common\modules\pages\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use common\modules\pages\traits\ModuleTrait;

/**
 * Page
 * @package common\modules\pages\models
 */
class Page extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new PageQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pages}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => Yii::t('pages', 'ID'),
            'title' => Yii::t('pages', 'TITLE'),
            'description' => Yii::t('pages', 'DESCRIPTION'),
            'keywords' => Yii::t('pages', 'KEYWORDS'),
            'name' => Yii::t('pages', 'NAME'),
            'url' => Yii::t('pages', 'URL'),
            'text' => Yii::t('pages', 'TEXT'),
            'status' => Yii::t('pages', 'STATUS'),
            'active' => Yii::t('pages', 'ACTIVE'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // Обязательные поля
            [['name', 'text', 'url', 'status'], 'required'],

            // Заголовок, описание, ключевые слова и название страницы
            [['title', 'description', 'keywords'], 'filter', 'filter' => 'trim'],
            [['title', 'description', 'keywords'], 'string'],

            [['name'], 'filter', 'filter' => 'trim'],
            [['name'], 'string', 'max' => 100],

            // статус
            ['status', 'in', 'range' => array_keys(self::getStatusArray())],

            // Текст [[text]]
            //['text', 'filter', 'filter' => 'htmlspecialchars'],

            // Url адрес
            ['url', 'unique', 'targetAttribute' => 'url'],
            ['url', 'string', 'max' => 16],
            ['url', 'match', 'pattern' => '/^[a-zA-Z0-9-_.]+$/'],
        ];
    }

    /**
     * Статусы статических страниц
     * - Доступна Всем
     * - Доступна Зарегистрированным
     * - Доступна Активным
     * - Закрыта от пользователей
     */
    const STATUS_ALL = 1;

    const STATUS_REGISTER = 2;

    const STATUS_CLOSE = 0;

    /**
     * @return array Массив доступных данных статуса статических страниц
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_ALL => [
                'label' => Yii::t('pages', 'STATUS_ALL'),
                'color' => 'green',
            ],
            self::STATUS_REGISTER => [
                'label' => Yii::t('pages', 'STATUS_REGISTER'),
                'color' => 'blue',
            ],
            self::STATUS_CLOSE => [
                'label' => Yii::t('pages', 'STATUS_CLOSE'),
                'color' => 'red',
            ],
        ];
    }

    /**
     * Получить данные страницы
     * @param $url
     * @return array|null|ActiveRecord
     * @throws HttpException
     */
    public static function getPageByUrl($url)
    {
        $model = Page::find()
            ->where('url = :url', [':url' => $url])
            ->andWhere('active = 1')
            ->one();

        $url = \yii\helpers\Url::toRoute(['/pages/default/index', 'url' => $url], true);
        $url = \yii\helpers\Html::a($url, $url);

        if (!$model) {
            throw new HttpException(404, Yii::t('pages', 'PAGE_NOT_FOUND {URL}', ['url' => $url]));
        }

        if ($model->status === self::STATUS_REGISTER && !Yii::$app->user->id) {
            throw new HttpException(403, Yii::t('pages', 'PAGE_IS_AVAILABLE_ONLY_FOR_REGISTERED {URL}', ['url' => $url]));
        }

        if ($model->status === self::STATUS_CLOSE) {
            throw new HttpException(403, Yii::t('pages', 'PAGE_IS_NOT_AVAILABLE {URL}', ['url' => $url]));
        }

        return $model;
    }
}
