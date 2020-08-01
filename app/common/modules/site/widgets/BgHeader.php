<?php

namespace common\modules\site\widgets;

use yii\helpers\Html;
use yii\base\Widget;
use Yii;

/**
 * Class BgHeader
 * @package common\modules\site\widgets
 */
class BgHeader extends Widget
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $dir = '@app/web/basic-images/bg-header';

    /**
     * @var string
     */
    public $url = '/basic-images/bg-header';

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
        $content = Html::tag('h1', ($this->title), ['id' => 'bg-header-title']);
        return Html::tag('div', $content, [
            'class' => 'bg-header',
            'style' => 'background-image: url(' . $this->url . '/' . $this->_file . ')',
        ]);
    }
}
