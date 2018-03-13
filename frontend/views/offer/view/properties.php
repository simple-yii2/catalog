<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

$title = Yii::t('catalog', 'Properties');

?>
<div class="h3"><?= Html::encode($title) ?></div>

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
