<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use cms\catalog\common\models\Category;

$title = Yii::t('catalog', 'Goods/Services');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	$title,
];

//categories
$categories = ['' => '[' . $search->getAttributeLabel('category_id') . ']'];
$query = Category::find()->orderBy(['lft' => SORT_ASC]);
foreach ($query->all() as $item) {
	if ($item->isLeaf() && $item->active)
		$categories[$item->id] = $item->path;
}

?>
<h1><?= Html::encode($title) ?></h1>

<div class="btn-toolbar" role="toolbar">
	<?= Html::a(Yii::t('catalog', 'Create'), ['create'], ['class' => 'btn btn-primary']) ?>
</div>

<div>
	<?php $form = ActiveForm::begin([
		'enableClientValidation' => false,
	]) ?>
		<div class="input-group">
			<?= Html::activeDropDownList($search, 'category_id', $categories, ['class' => 'form-control']) ?>
			<span class="input-group-btn">
				<?= Html::submitButton('Применить', ['class' => 'btn btn-default']) ?>
			</span>
		</div>
	<?php ActiveForm::end() ?>
</div>

<?= GridView::widget([
	'dataProvider' => $search->getDataProvider(),
	'filterModel' => $search,
	'summary' => '',
	'tableOptions' => ['class' => 'table table-condensed'],
	'rowOptions' => function ($model, $key, $index, $grid) {
		return !$model->active ? ['class' => 'warning'] : [];
	},
	'columns' => [
		[
			'attribute' => 'name',
			'format' => 'html',
			'content' => function($model, $key, $index, $column) {
				$result = '';

				if (!empty($model->thumb))
					$result .= Html::img($model->thumb, ['align' => 'left', 'height' => 40, 'hspace' => 10]);

				$result .= Html::encode($model->name);

				if (!empty($model->model))
					$result .= ' ' . $model->model;

				if ($model->imageCount > 0)
					$result .= '&nbsp;' . Html::tag('span', '<span class="glyphicon glyphicon-picture"></span>&nbsp;' . $model->imageCount, ['class' => 'badge']);

				if (!empty($model->vendor))
					$result .= '&nbsp;' . Html::tag('span', Html::encode($model->vendor), ['class' => 'label label-info']);

				if ($model->category !== null)
					$result .= '<br>' . Html::tag('span', $model->category->path, ['class' => 'text-muted']);

				return $result;
			},
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'options' => ['style' => 'width: 50px;'],
			'template' => '{update} {delete}',
		],
	],
]) ?>
