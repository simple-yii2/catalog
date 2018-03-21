<?php

use yii\helpers\Html;
use yii\web\JsExpression;

use dkhlystov\widgets\NestedTreeGrid;

$title = Yii::t('catalog', 'Categories');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	$title,
];

$maxDepth = Yii::$app->controller->module->maxCategoryDepth;

?>
<h1><?= Html::encode($title) ?></h1>

<div class="btn-toolbar" role="toolbar">
	<?= Html::a(Yii::t('catalog', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
	<?php if (Yii::$app->controller->module->propertiesEnabled) echo Html::a(Yii::t('catalog', 'Common properties'), ['properties'], ['class' => 'btn btn-primary']) ?>
</div>

<?= NestedTreeGrid::widget([
	'dataProvider' => $search->getDataProvider(),
	'initialNode' => $initial,
	'moveAction' => ['move'],
	'tableOptions' => ['class' => 'table table-condensed'],
	'rowOptions' => function ($model, $key, $index, $grid) {
		$options = ['data-product-count' => $model->productCount];

		if (!$model->active)
			Html::addCssClass($options, 'warning');

		return $options;
	},
	'pluginOptions' => [
		'onMoveOver' => new JsExpression('function (item, helper, target, position) {
			if (position == 1)
				return target.data("productCount") == 0;

			return true;
		}'),
	],
	'columns' => [
		[
			'attribute' => 'title',
			'format' => 'html',
			'content' => function($model, $key, $index, $column) {
				$result = Html::encode($model->title);

				if ($model->productCount > 0)
					$result .= '&nbsp;' . Html::tag('span', $model->productCount, ['class' => 'badge']);

				return $result;
			},
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'options' => ['style' => 'width: 75px;'],
			'template' => '{update} {delete} {create}',
			'buttons' => [
				'create' => function ($url, $model, $key) {
					$title = Yii::t('catalog', 'Create');

					return Html::a('<span class="glyphicon glyphicon-plus"></span>', $url, [
						'title' => $title,
						'aria-label' => $title,
						'data-pjax' => 0,
					]);
				},
			],
			'visibleButtons' => [
				'delete' => function($model, $key, $index) {
					return $model->productCount == 0;
				},
				'create' => function($model, $key, $index) use ($maxDepth) {
					if ($model->productCount > 0)
						return false;

					if ($maxDepth !== null && $model->depth >= $maxDepth)
						return false;

					return true;
				},
			],
		],
	],
]) ?>
