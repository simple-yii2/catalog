<?php

use yii\helpers\Html;
use cms\catalog\frontend\widgets\OfferList;

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

//offer list widget config
$module = Yii::$app->controller->module;
if ($model->getPropertyCount()) {
	$config = $module->offerListWithFilterConfig;
} else {
	$config = $module->offerListConfig;
}
$config['model'] = $model;

?>
<h1><?= Html::encode($title) ?></h1>

<?= OfferList::widget($config) ?>
