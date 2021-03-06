<?php

use yii\helpers\Html;
use cms\catalog\frontend\helpers\CatalogHelper;
use cms\catalog\frontend\widgets\Categories;
use cms\catalog\frontend\widgets\ProductFilter;
use cms\catalog\frontend\widgets\ProductList;
use cms\catalog\frontend\widgets\ProductToolbar;

$title = $category->isRoot() ? Yii::t('catalog', 'Catalog') : $category->title;

$this->title = $title . ' | ' . Yii::$app->name;

//breadcrumbs
$breadcrumbs = [];
foreach ($category->getParents() as $object) {
    if (!$object->isRoot())
        $breadcrumbs[] = ['label' => $object->title, 'url' => ['index', 'alias' => $object->alias]];
}
$breadcrumbs[] = $title;
$this->params['breadcrumbs'] = $breadcrumbs;

//data provider
$dataProvider = $filterModel->getDataProvider();

?>
<h1><?= Html::encode($title) ?></h1>

<div class="row">
    <div class="col-xs-12 col-md-9">
        <?= ProductToolbar::widget([
            'dataProvider' => $dataProvider,
            'sortLabel' => Yii::t('catalog', 'Sort'),
            'sortPriceAscendingText' => Yii::t('catalog', 'By price ascending'),
            'sortPriceDescendingText' => Yii::t('catalog', 'By price descending'),
            'sortNameText' => Yii::t('catalog', 'By name'),
            'filterButtonText' => Yii::t('catalog', 'Filters'),
            'filterHideLabel' => Yii::t('catalog', 'Hide filters'),
        ]) ?>
        <?= Categories::widget([
            'filter' => $filterModel,
        ]) ?>
        <?= ProductList::widget([
            'dataProvider' => $dataProvider,
        ]) ?>
    </div>
    <div class="hidden-xs hidden-sm col-md-3">
        <?= ProductFilter::widget([
            'model' => $filterModel,
            'resetButtonText' => Yii::t('catalog', 'Reset'),
            'applyButtonText' => Yii::t('catalog', 'Apply'),
            'trueText' => Yii::t('catalog', 'Yes'),
            'falseText' => Yii::t('catalog', 'No'),
        ]) ?>
    </div>
</div>
