<?php

use yii\helpers\Html;
use cms\catalog\frontend\widgets\OfferList;

$title = $category->isRoot() ? Yii::t('catalog', 'Catalog') : $category->title;

$this->title = $title . ' | ' . Yii::$app->name;

//breadcrumbs
$breadcrumbs = [
	['label' => Yii::t('catalog', 'Categories'), 'url' => ['index']],
];
foreach ($category->getParents() as $object) {
	if (!$object->isRoot())
		$breadcrumbs[] = ['label' => $object->title, 'url' => ['index', 'alias' => $object->alias]];
}
$breadcrumbs[] = $title;
$this->params['breadcrumbs'] = $breadcrumbs;

var_dump($model->getPropertyCount()); die();

?>
<h1><?= Html::encode($title) ?></h1>

<?= OfferList::widget([
	'model' => $model,
]) ?>
