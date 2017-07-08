<?php

use yii\helpers\Html;

$title = Yii::t('catalog', 'Create category');

$this->title = $title . ' | ' . Yii::$app->name;

$breadcrumbs = [
	['label' => Yii::t('catalog', 'Categories'), 'url' => ['index']],
];
foreach ($parents as $object) {
	if (!$object->isRoot())
		$breadcrumbs[] = $object->title;
}
$breadcrumbs[] = $title;
$this->params['breadcrumbs'] = $breadcrumbs;

?>
<h1><?= Html::encode($title) ?></h1>

<?= $this->render('form', [
	'model' => $model,
	'id' => $id,
]) ?>
