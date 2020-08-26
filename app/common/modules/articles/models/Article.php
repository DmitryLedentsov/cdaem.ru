<?php

namespace common\modules\articles\models;

use Yii;

/**
 * Статьи
 * @package common\modules\articles\models
 */
class Article extends \yii\db\ActiveRecord
{
    public $file;

    public $bgfile;

    public $imagesPath = '@frontend/web/images';

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new ArticleQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%articles}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'article_id' => 'ID',
            'slug' => 'Слаг',
            'title_img' => 'Имя файла изображения',
            'background' => 'Обои в шапку',
            'file' => 'Выбрать изображение',
            'bgfile' => 'Изображение в шапку',
            'name' => 'Название',
            'title' => 'Заголовок (Title)',
            'description' => 'Описание (Description)',
            'keywords' => 'Ключевые слова (Keywords)',
            'short_text' => 'Краткий текст',
            'full_text' => 'Подробный текст',
            'visible' => 'Просмотр',
            'status' => 'Статус',
            'date_create' => 'Дата создания',
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
                'label' => 'Доступ всем	',
                'style' => 'color: green',
            ],
            self::STATUS_REGISTER => [
                'label' => 'Только зарегистрированным',
                'style' => 'color: blue',
            ],
            self::STATUS_CLOSE => [
                'label' => 'Закрыт',
                'style' => 'color: red',
            ],
        ];
    }

    public function getArticlelink()
    {
        return $this->hasMany(ArticleLink::class, ['article_id' => 'article_id']);
    }

    public function getTitleImg()
    {
        $pathToImg = sprintf('%s/%s', Yii::getAlias($this->imagesPath), $this->title_img);

        if (file_exists($pathToImg) && is_file($pathToImg)) {
            return sprintf('/images/%s', $this->title_img);
        }

        return '/pics/no-photo.png';
    }
}
