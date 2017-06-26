<?php

use yii\grid\GridView;
use yii\helpers\Html;

$title = Yii::t('catalog', 'Currencies');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	$title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<div class="btn-toolbar" role="toolbar">
	<?= Html::a(Yii::t('catalog', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<?= GridView::widget([
	'dataProvider' => $search->getDataProvider(),
	// 'filterModel' => $model,
	'summary' => '',
	'tableOptions' => ['class' => 'table table-condensed'],
	'columns' => [
		'code',
		'rate',
		// [
		// 	'attribute' => 'rate',
		// 	'format' => 'html',
		// 	'content' => function($model, $key, $index, $column) {
		// 		$result = '';

		// 		if (!empty($model->thumb))
		// 			$result .= Html::img($model->thumb, ['height' => 20]) . '&nbsp;';

		// 		$result .= Html::encode($model->code);

		// 		if ($model->imageCount > 0)
		// 			$result .= '&nbsp;' . Html::tag('span', $model->imageCount, ['class' => 'badge']);

		// 		return $result;
		// 	},
		// ],
		[
			'class' => 'yii\grid\ActionColumn',
			'options' => ['style' => 'width: 50px;'],
			'template' => '{update} {delete}',
		],
	],
]) ?>
