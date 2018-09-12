<?php

namespace cms\catalog\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\Request;
use yii\widgets\Menu;
use cms\catalog\frontend\widgets\assets\CategoriesAsset;

/**
 * Show next level categories using filter model
 */
class Categories extends Widget
{

    /**
     * @var ProductFilter
     */
    public $filter;

    /**
     * @var string
     */
    public $route = '/catalog/product/index';

    /**
     * @var array
     */
    public $options = ['class' => 'categories'];

    /**
     * @var array
     */
    public $countOptions = ['class' => 'categories-count text-muted'];

    /**
     * @var array
     */
    private $_items = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        CategoriesAsset::register($this->view);

        $this->prepareItems();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (empty($this->_items)) {
            return '';
        }

        return Menu::widget([
            'items' => $this->_items,
            'options' => $this->options,
        ]);
    }

    /**
     * Prepare category items for menu widget
     * @return void
     */
    private function prepareItems()
    {
        $this->_items = [];

        if ($this->filter->category === null) {
            return;
        }

        $query = clone $this->filter->getQuery();
        $rows = $query->select(['category_id', 'category_lft', 'category_rgt', 'COUNT(*) AS cnt'])->groupBy(['category_id'])->asArray()->all();
        if (empty($rows)) {
            return;
        }

        $request = Yii::$app->getRequest();
        $params = $request instanceof Request ? $request->getQueryParams() : [];
        unset($params['p']);
        $params[0] = $this->route;

        $items = [];
        foreach ($this->filter->category->children(1)->andWhere(['active' => true])->select(['lft', 'rgt', 'title', 'alias'])->asArray()->all() as $row) {
            $cnt = 0;
            foreach ($rows as $key => $value) {
                if ($value['category_lft'] >= $row['lft'] && $value['category_rgt'] <= $row['rgt']) {
                    $cnt += $value['cnt'];
                    unset($rows[$key]);
                }
            }
            if ($cnt > 0) {
                $params['alias'] = $row['alias'];
                $s = Html::tag('span', $cnt, $this->countOptions);
                $items[] = ['label' => $row['title'], 'url' => $params, 'template' => '<a href="{url}">{label}</a> ' . $s];
            }
        }

        $this->_items = $items;
    }

}
