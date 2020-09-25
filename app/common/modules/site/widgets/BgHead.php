<?php

namespace common\modules\site\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Виджет отображает случайшую шапку
 */
class BgHead extends Widget
{
    /**
     * @var string
     */
    public $title;

    public $background;

    /**
     * @var string
     */
    public $dir = '@app/web/images';

    /**
     * @var string
     */
    public $url = '/images';

    /**
     * @var array
     */
    public $extensions = ['jpg', 'png'];

    /**
     * @var string
     */
    private $_file;

    /**
     * Init
     */
    public function init()
    {
        parent::init();

        $dir = Yii::getAlias($this->dir);

        $files = scandir($dir);

        foreach ($files as $key => $file) {
            $array = array_filter(explode(".", $file));
            $ext = end($array);

            if (!in_array($ext, $this->extensions)) {
                unset($files[$key]);
            }
        }

        $this->_file = $files[array_rand($files)];
    }

    /**
     * Render
     */
    public function run()
    {
        $content = Html::tag('h1', ($this->title), ['id' => 'bg-head-title']);
        $background = $this->background;
        if ($this->background == null) {
            $background = 'article-no-img.jpg';
        }

        return Html::tag('div', $content, [
            'class' => 'bg-header-2',
            'style' => 'background: url(' . $this->url . '/' . $background . ')center center;background-size:cover',
        ]);
    }
}
