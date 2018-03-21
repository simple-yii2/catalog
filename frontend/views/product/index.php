<?php

use yii\helpers\Html;
use cms\catalog\frontend\widgets\ProductList;

$title = $category->isRoot() ? Yii::t('catalog', 'Catalog') : $category->title;

$this->title = $title . ' | ' . Yii::$app->name;

//breadcrumbs
$breadcrumbs = [
	// ['label' => Yii::t('catalog', 'Categories'), 'url' => ['index']],
];
foreach ($category->getParents() as $object) {
	if (!$object->isRoot())
		$breadcrumbs[] = ['label' => $object->title, 'url' => ['index', 'alias' => $object->alias]];
}
$breadcrumbs[] = $title;
$this->params['breadcrumbs'] = $breadcrumbs;

//product list widget config
$module = Yii::$app->controller->module;
if ($model->getPropertyCount()) {
	$config = $module->productListWithFilterConfig;
} else {
	$config = $module->productListConfig;
}
$config['model'] = $model;

?>
<h1><?= Html::encode($title) ?></h1>

<?= ProductList::widget($config) ?>
