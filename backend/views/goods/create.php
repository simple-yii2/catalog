<?php

use yii\helpers\Html;

$title = Yii::t('catalog', 'Create goods');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	['label' => Yii::t('catalog', 'Goods'), 'url' => ['index']],
	$title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?= $this->render('form', [
	'model' => $model,
]) ?>