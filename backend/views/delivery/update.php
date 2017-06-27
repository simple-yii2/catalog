<?php

use yii\helpers\Html;

$title = $form->getModel()->name;

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	['label' => Yii::t('catalog', 'Delivery'), 'url' => ['index']],
	$title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?= $this->render('form', [
	'form' => $form,
]) ?>
