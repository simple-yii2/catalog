<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

$title = Yii::t('catalog', 'Properties');

?>
<h2><?= Html::encode($title) ?></h2>

<?= GridView::widget([
	'dataProvider' => new ArrayDataProvider([
		'allModels' => $properties,
		'pagination' => false,
	]),
	'layout' => '{items}',
	'showHeader' => false,
	'tableOptions' => ['class' => 'table offer-properties-table'],
	'columns' => [
		['content' => function ($model, $key, $index, $column) {
			return Html::encode($model['label']);
		}],
		['content' => function ($model, $key, $index, $column) {
			return Html::encode($model['value']);
		}],
	],
]) ?>
