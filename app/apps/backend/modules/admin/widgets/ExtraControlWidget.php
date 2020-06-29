<?php

namespace backend\modules\admin\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;

/**
 * Admin Extra Control Widget
 */
class ExtraControlWidget extends Widget
{
    /**
     * @var array
     */
    public $control = [];

    /**
     * @var string
     */
    private $_htmlControlList = '';

    /**
     * Init
     */
    public function init()
    {
        parent::init();

        if (is_array($this->control)) {
            foreach ($this->control as $control) {
                $url = isset($control['url']) ? $control['url'] : '';
                $label = isset($control['label']) ? $control['label'] : '';
                $this->_htmlControlList .= '<li>' . Html::a($label, $url) . '</li>';
            }
        }
    }

    /**
     * Render
     */
    public function run()
    {
        print('
            <!-- Page tabs -->
            <div class="tabbable page-tabs">
                <ul class="nav nav-tabs">
                    <li class="pull-right dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"> <i class="icon-cog3"></i> Дополнительно <b class="caret"></b> </a>
                        <ul class="dropdown-menu dropdown-menu-right icons-right">
                            ' . $this->_htmlControlList . '
                        </ul>
                    </li>
                </ul>
            </div>
        ');
    }
}
