<?php

namespace cms\catalog\frontend\widgets;

use yii\base\Widget;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use cms\catalog\frontend\widgets\assets\ProductToolbarAsset;

class ProductToolbar extends Widget
{

    /**
     * @var yii\data\DataProviderInterface
     */
    public $dataProvider;

    /**
     * @var array
     */
    public $options = ['class' => 'product-toolbar'];

    /**
     * @var string
     */
    public $sortButtonIcon = '<span class="glyphicon glyphicon-sort-by-attributes"></span>';

    /**
     * @var string
     */
    public $sortLabel = 'Sort';

    /**
     * @var string
     */
    public $sortPriceAscendingText = 'By price ascending';

    /**
     * @var string
     */
    public $sortPriceDescendingText = 'By price descending';

    /**
     * @var string
     */
    public $sortNameText = 'By name';

    /**
     * @var array
     */
    public $sortOptions = ['class' => 'product-toolbar-sort'];

    /**
     * @var string
     */
    public $filterTarget = '.product-filter';

    /**
     * @var string
     */
    public $filterButtonText = 'Filters';

    /**
     * @var string
     */
    public $filterHideLabel = 'Hide filters';

    /**
     * @var array
     */
    public $filterOptions = ['class' => 'product-toolbar-filter'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        ProductToolbarAsset::register($this->view);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $sort = $this->renderSort();
        $filter = $this->renderFilter();

        $right = Html::tag('div', $filter, ['class' => 'pull-right']);

        return Html::tag('div', $right . $sort, $this->options);
    }

    /**
     * Render sort block
     * @return string
     */
    protected function renderSort()
    {
        if (($sort = $this->dataProvider->getSort()) === false) {
            return '';
        }

        //drop down items
        $orders = $sort->getAttributeOrders();
        $items = [];
        if ($sort->hasAttribute('price')) {
            $options = [];
            if (ArrayHelper::getValue($orders, 'price') === SORT_ASC) {
                Html::addCssClass($options, 'active');
            }
            $sort->setAttributeOrders(['price' => SORT_DESC]);
            $items[] = ['label' => $this->sortPriceAscendingText, 'url' => $sort->createUrl('price'), 'options' => $options];
            
            $options = [];
            if (ArrayHelper::getValue($orders, 'price') === SORT_DESC) {
                Html::addCssClass($options, 'active');
            }
            $sort->setAttributeOrders(['price' => SORT_ASC]);
            $items[] = ['label' => $this->sortPriceDescendingText, 'url' => $sort->createUrl('price'), 'options' => $options];
        }
        if ($sort->hasAttribute('name')) {
            $options = [];
            if (ArrayHelper::getValue($orders, 'name') === SORT_ASC) {
                Html::addCssClass($options, 'active');
            }
            $sort->setAttributeOrders(['name' => SORT_DESC]);
            $items[] = ['label' => $this->sortNameText, 'url' => $sort->createUrl('name'), 'options' => $options];
        }
        $sort->setAttributeOrders($orders);
        if (empty($items)) {
            return '';
        }

        //label
        $label = Html::tag('span', $this->sortLabel, ['class' => 'product-toolbar-sort-label']);

        //button icon
        $buttonIcon = Html::tag('span', $this->sortButtonIcon, ['class' => 'product-toolbar-sort-icon']);

        //button text
        $attribute = ArrayHelper::getValue(array_keys($orders), 0);
        $value = ArrayHelper::getValue($orders, $attribute);
        if ($attribute == 'name') {
            $s = $this->sortNameText;
        } else {
            $s = $value == SORT_DESC ? $this->sortPriceDescendingText : $this->sortPriceAscendingText;
        }
        $buttonText = Html::tag('span', $s, ['class' => 'product-toolbar-sort-text']);

        return Html::tag('div', $label . ButtonDropdown::widget([
            'label' => $buttonIcon . $buttonText,
            'encodeLabel' => false,
            'dropdown' => ['items' => $items],
            'options' => ['class' => 'btn btn-default'],
        ]), $this->sortOptions);
    }

    /**
     * Render filter toggle block
     * @return string
     */
    protected function renderFilter()
    {
        $button = Html::button($this->filterButtonText . '<span class="glyphicon glyphicon-menu-right"></span>', ['class' => 'btn btn-default']);
        $modal = Modal::widget([
            'header' => $this->filterHideLabel,
            'options' => ['class' => 'product-toolbar-filter-modal', 'data-target' => $this->filterTarget],
        ]);

        return Html::tag('div', $button . $modal, $this->filterOptions);
    }

}
