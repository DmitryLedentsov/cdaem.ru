<?php

namespace common\modules\articles\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * Статьи
 * @package common\modules\articles\models
 */
class Article extends \yii\db\ActiveRecord
{
    public $file;

    public $bgfile;

    public string $imagesPath = '@frontend/web/images';

    /**
     * @inheritdoc
     * @return ArticleQuery
     */
    public static function find(): ArticleQuery
    {
        return new ArticleQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%articles}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
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
     * Массив доступных данных статуса статических страниц
     * @return array
     */
    public static function getStatusArray(): array
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

    /**
     * @return ActiveQuery
     */
    public function getArticlelink(): ActiveQuery
    {
        return $this->hasMany(ArticleLink::class, ['article_id' => 'article_id']);
    }

    /**
     * @return string
     */
    public function getTitleImg(): string
    {
        $pathToImg = sprintf('%s/%s', Yii::getAlias($this->imagesPath), $this->title_img);

        if (file_exists($pathToImg) && is_file($pathToImg)) {
            return sprintf('/images/%s', $this->title_img);
        }

        return '/basic-images/no-photo.png';
    }
}
