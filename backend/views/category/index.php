<?php

use yii\helpers\Html;
use yii\web\JsExpression;

use dkhlystov\widgets\NestedTreeGrid;

$title = Yii::t('catalog', 'Categories');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	$title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<div class="btn-toolbar" role="toolbar">
	<?= Html::a(Yii::t('catalog', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
	<?= Html::a(Yii::t('catalog', 'Common properties'), ['properties'], ['class' => 'btn btn-primary']) ?>
</div>

<?= NestedTreeGrid::widget([
	'dataProvider' => $search->getDataProvider(),
	'initialNode' => $initial,
	'moveAction' => ['move'],
	'tableOptions' => ['class' => 'table table-condensed'],
	'rowOptions' => function ($model, $key, $index, $grid) {
		$options = ['data-offer-count' => $model->offerCount];

		if (!$model->active)
			Html::addCssClass($options, 'warning');

		return $options;
	},
	'pluginOptions' => [
		'onMoveOver' => new JsExpression('function (item, helper, target, position) {
			if (position == 1)
				return target.data("offerCount") == 0;

			return true;
		}'),
	],
	'columns' => [
		[
			'attribute' => 'title',
			'format' => 'html',
			'content' => function($model, $key, $index, $column) {
				$result = Html::encode($model->title);

				if ($model->offerCount > 0)
					$result .= '&nbsp;' . Html::tag('span', $model->offerCount, ['class' => 'badge']);

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
					return $model->offerCount == 0;
				},
				'create' => function($model, $key, $index) {
					return $model->offerCount == 0;
				},
			],
		],
	],
]) ?>
