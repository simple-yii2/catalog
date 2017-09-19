<?php

use yii\helpers\Html;

$title = $category->isRoot() ? Yii::t('catalog', 'Catalog') : $category->title;

$this->title = $title . ' | ' . Yii::$app->name;

//breadcrumbs
$breadcrumbs = [
	['label' => Yii::t('catalog', 'Categories'), 'url' => ['index']],
];
foreach ($category->parents()->all() as $object) {
	if (!$object->isRoot())
		$breadcrumbs[] = ['label' => $object->title, 'url' => ['index', 'alias' => $object->alias]];
}
$breadcrumbs[] = $title;
$this->params['breadcrumbs'] = $breadcrumbs;

?>
<h1><?= Html::encode($title) ?></h1>

<div class="row">
	<div class="col-sm-3"><?= $this->render('index/filter', ['model' => $model]) ?></div>
	<div class="col-sm-9"><?= $this->render('index/list', ['model' => $model]) ?></div>
</div>

