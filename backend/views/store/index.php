<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use dkhlystov\grid\GridView;
use cms\catalog\common\models\Store;

$title = Yii::t('catalog', 'Stores');
$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    Yii::t('catalog', 'Catalog'),
	$title,
];

$typeNames = Store::getTypeNames();

?>
<h1><?= Html::encode($title) ?></h1>

<div class="btn-toolbar" role="toolbar">
	<?= Html::a(Yii::t('cms', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<?= GridView::widget([
	'dataProvider' => $model->getDataProvider(),
	'filterModel' => $model,
	'summary' => '',
	'tableOptions' => ['class' => 'table table-condensed'],
	'columns' => [
		'name',
		[
			'attribute' => 'type',
			'filter' => $typeNames,
			'content' => function($model, $key, $index, $column) use ($typeNames) {
				return ArrayHelper::getValue($typeNames, $model->type);
			},
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'options' => ['style' => 'width: 50px;'],
			'template' => '{update} {delete}',
		],
	],
]) ?>
