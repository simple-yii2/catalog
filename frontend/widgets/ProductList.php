<?php

namespace cms\catalog\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\widgets\ListView;

class ProductList extends Widget
{

    /**
     * @var array the HTML attributes for the container tag of the product list container.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'row'];

    /**
     * @var string the layout that determines how different sections of the product list should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{filter}`: the filter section. See [[renderFilter()]].
     * - `{list}`: the list items. See [[renderList()]].
     */
    public $layout = "{filter}\n{list}";

    /**
     * @var array Filter block HTML options
     */
    public $filterOptions = ['class' => 'col-sm-3'];

    /**
     * @var array List block HTML options
     */
    public $listOptions = ['class' => 'col-sm-9'];

    /**
     * @var array List item block HTML options
     */
    public $listItemOptions = ['class' => 'col-sm-4'];

    /**
     * @var integer
     */
    public $pageSize = 24;

    /**
     * @var array Filter widget config
     */
    public $filterConfig = [];

    /**
     * @var array List widget config
     */
    public $listConfig = [];

    /**
     * @var ProductFilter Product filter model
     */
    public $model;

    /**
     * Runs the widget.
     */
    public function run()
    {
        $content = preg_replace_callback('/{\\w+}/', function ($matches) {
            $content = $this->renderSection($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $this->layout);

        echo Html::tag('div', $content, $this->options);
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{filter}`, `{list}`.
     * @return string|bool the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{filter}':
                return $this->renderFilter();
            case '{list}':
                return $this->renderList();
            default:
                return false;
        }
    }

    /**
     * Render filter block
     * @return string
     */
    protected function renderFilter()
    {
        $config = array_replace([
            'model' => $this->model,
            'buttonText' => Yii::t('catalog', 'Apply'),
            'trueText' => Yii::t('catalog', 'Yes'),
            'falseText' => Yii::t('catalog', 'No'),
        ], $this->filterConfig);

        return Html::tag('div', ProductFilter::widget($config), $this->filterOptions);
    }

    /**
     * Render list block
     * @return string
     */
    protected function renderList()
    {
        $itemClass = 'cms\purchase\frontend\widgets\ProductItem1';
        // $itemClass = 'cms\purchase\frontend\widgets\ProductItem';
        if (!class_exists($itemClass)) {
            $itemClass = ProductItem::className();
        }

        $config = array_replace([
            'dataProvider' => $this->model->getDataProvider(['pagination' => ['defaultPageSize' => $this->pageSize]]),
            'layout' => '<div class="row">{items}</div>{pager}',
            'itemOptions' => $this->listItemOptions,
            'itemView' => function($model, $key, $index, $widget) use ($itemClass) {
                return $itemClass::widget(['model' => $model]);
            },
        ], $this->listConfig);

        return Html::tag('div', ListView::widget($config), $this->listOptions);
    }

}
