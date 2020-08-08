<?php

namespace backend\components;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\base\InvalidParamException;
use yii\base\InvalidConfigException;

/**
 * Navigation
 */
class Navigation extends \yii\base\Component
{
    /**
     * @var array Общий массив всех секций
     */
    private $_sections = [];

    /**
     * Получить массив секций
     * @return array
     */
    public function getSections()
    {
        return $this->_sections;
    }

    /**
     * Добавить новую секцию
     * @param array $section
     */
    public function addSection(array $section)
    {
        if (!isset($section['module'])) {
            throw new InvalidParamException('Key module is not found');
        }

        foreach ($this->_sections as &$_section) {
            if ($_section['module'] == $section['module']) {
                throw new InvalidParamException('Module ' . $section['module'] . ' already exists');
            }
        }

        $this->_sections[] = $section;
    }

    /**
     * Рендер
     * @return string
     */
    public function render()
    {
        $currentModule = Yii::$app->controller->module->id;
        $currentController = Yii::$app->controller->id;
        $currentAction = Yii::$app->controller->action->id;

        $this->setActiveSection($currentModule, $currentController, $currentAction);

        return $this->recursivelyGenerateHtml();
    }

    /**
     * Установить активные секции
     *
     * @param $currentModule
     * @param $currentController
     * @param $currentAction
     * @param array $sections
     * @param null $currentDropdownModule
     * @return array
     */
    private function setActiveSection($currentModule, $currentController, $currentAction, array $sections = [], $currentDropdownModule = null)
    {
        $dropdown = true;
        if (!$sections) {
            $sections = &$this->_sections;
            $dropdown = false;
        }

        foreach ($sections as &$section) {
            $section['options'] = isset($section['options']) ? $section['options'] : [];

            if ($dropdown === false) {
                $section['module'] = isset($section['module']) ? $section['module'] : '';
                $currentDropdownModule = $section['module'];

                if ($section['module'] === $currentModule) {
                    $section['options']['class'] = 'active';
                }
            } else {
                $section['controller'] = isset($section['controller']) ? $section['controller'] : '';
                $section['action'] = isset($section['action']) ? $section['action'] : '';

                if ($currentDropdownModule === $currentModule && $section['controller'] === $currentController && $section['action'] === $currentAction) {
                    $section['options']['class'] = 'active';
                }
            }

            if (isset($section['dropdown']) && !empty($section['dropdown'])) {
                $section['dropdown'] = $this->setActiveSection($currentModule, $currentController, $currentAction, $section['dropdown'], $currentDropdownModule);
            }
        }

        return $sections;
    }

    /**
     * Рекурсивно генерируем Html
     * @param array $sections
     * @return string
     */
    private function recursivelyGenerateHtml(array $sections = [])
    {
        $result = '';
        $sections = (empty($sections)) ? $this->_sections : $sections;
        foreach ($sections as &$section) {
            $url = isset($section['url']) ? htmlspecialchars($section['url']) : [];
            $name = isset($section['name']) ? Html::tag('span', htmlspecialchars($section['name'])) : Html::tag('span', '');
            $icon = isset($section['icon']) ? $section['icon'] : '';
            $options = isset($section['options']) ? $section['options'] : [];
            $access = isset($section['access']) ? $section['access'] : true;

            if (is_callable($access)) {
                $access = call_user_func($access);
            }

            if ($access) {
                $content = '';
                if (isset($section['dropdown']) && !empty($section['dropdown'])) {
                    $content = $this->recursivelyGenerateHtml($section['dropdown']);
                    $content = Html::tag('ul', $content);
                }

                if (isset($section['callback']) && is_callable($section['callback'])) {
                    $params = [
                        'url' => $url,
                        'name' => $name,
                        'icon' => $icon,
                        'options' => $options,
                    ];
                    $result .= call_user_func_array($section['callback'], [$params, $content]) . PHP_EOL;
                } else {
                    $result .= Html::tag('li', Html::a($name . $icon, $url) . $content, $options) . PHP_EOL;
                }
            }
        }

        return $result;
    }
}
