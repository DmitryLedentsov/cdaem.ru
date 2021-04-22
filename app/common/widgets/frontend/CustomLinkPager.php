<?php

namespace common\widgets\frontend;

use Yii;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;

/**
 * Class CustomLinkPager
 * @package common\widgets\frontend
 */
class CustomLinkPager extends LinkPager
{
    /**
     * @inheritDoc
     */
    public $options = [
        'class' => 'pagination-panel-pages'
    ];
    /**
     * @var array HTML attributes which will be applied to all prev and next link container
     */
    public $prevNextOtions = [
        'class' => 'pagination-panel-arrows'
    ];
    /**
     * @inheritDoc
     */
    public $linkContainerOptions = [
        'class' => 'pagination-page'
    ];
    /**
     * @var array HTML attributes for the link in a pager container tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $linkOptions = [];
    /**
     * @var string the CSS class for the each page button.
     * @since 2.0.7
     */
    public $pageCssClass;
    /**
     * @inheritDoc
     */
    public $firstPageCssClass = '';
    /**
     * @inheritDoc
     */
    public $lastPageCssClass = '';
    /**
     * @var string the CSS class for the "previous" page button.
     */
    public $prevPageCssClass = 'pagination-arrows pagination-previous';
    /**
     * @var string the CSS class for the "next" page button.
     */
    public $nextPageCssClass = 'pagination-arrows pagination-next';
    /**
     * @var string the CSS class for the active (currently selected) page button.
     */
    public $activePageCssClass = 'is-active';
    /**
     * @var array the options for the disabled tag to be generated inside the disabled list element.
     * In order to customize the html tag, please use the tag key.
     *
     * ```php
     * $disabledListItemSubTagOptions = ['tag' => 'div', 'class' => 'disabled-div'];
     * ```
     * @since 2.0.11
     */
    public $disabledListItemSubTagOptions = [];
    /**
     * @var int maximum number of page buttons that can be displayed. Defaults to 10.
     */
    public $maxButtonCount = 3;
    /**
     * @inheritDoc
     */
    public $nextPageLabel = '';
    /**
     * @inheritDoc
     */
    public $prevPageLabel = '';

    /**
     * @inheritDoc
     */
    public $firstPageLabel = true;
    /**
     * @inheritDoc
     */
    public $lastPageLabel = true;

    /**
     * @inheritDoc
     */
    protected function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons = [];
        $prevNextButtons = [];
        $currentPage = $this->pagination->getPage();
        list($beginPage, $endPage) = $this->getPageRange();

        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false && $beginPage > 1) {
            $buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);

            $buttons[] = $this->renderPageButton('...', false, 'pagination-more', $this->disableCurrentPageButton, false);

        }

        // internal pages
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton($i + 1, $i, null, $this->disableCurrentPageButton && $i == $currentPage, $i == $currentPage);
        }

        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false && $pageCount - 2 > $endPage) {
            $buttons[] = $this->renderPageButton('...', false, 'pagination-more', $this->disableCurrentPageButton, false);

            $buttons[] = $this->renderPageButton($lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'ul');


        // prev page
        if ($this->prevPageLabel !== false && $currentPage > $beginPage) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $prevNextButtons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
        }

        // next page
        if ($this->nextPageLabel !== false && $currentPage < $endPage) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $prevNextButtons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        $prevNextOtions = $this->prevNextOtions;
        $prevNextTag = ArrayHelper::remove($prevNextOtions, 'tag', 'ul');

        $result = '
            <section class="pagination">
                <div class="container-fluid">
                '.Html::tag($tag, implode("\n", $buttons), $options).
                Html::tag($prevNextTag, implode("\n", $prevNextButtons), $prevNextOtions).'
                </div>
            </section>
        ';


        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);

        if ($page === false) {
            return Html::tag($linkWrapTag, $label, $options);
        }

        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        if ($active) {
            Html::addCssClass($linkOptions, $this->activePageCssClass);
        }

        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $tag = ArrayHelper::remove($disabledItemOptions, 'tag', 'span');

            return Html::tag($linkWrapTag, Html::tag($tag, $label, $disabledItemOptions), $options);
        }


        return Html::tag($linkWrapTag, Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);

    }

}
