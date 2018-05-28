<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use cms\catalog\backend\assets\ProductListAsset;
use cms\catalog\common\helpers\PriceHelper;
use cms\catalog\common\models\Category;

ProductListAsset::register($this);

$title = Yii::t('catalog', 'Goods/Services');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	$title,
];

//create url
$createUrl = ['create'];
if (!empty($search->category_id)) {
	$createUrl['category_id'] = $search->category_id;
}

//categories
$categories = ['' => '[' . $search->getAttributeLabel('category_id') . ']'];
$query = Category::find()->orderBy(['lft' => SORT_ASC]);
foreach ($query->all() as $item) {
	if ($item->isLeaf() && $item->active) {
		$categories[$item->id] = $item->path;
	}
}

?>
<h1><?= Html::encode($title) ?></h1>

<div class="btn-toolbar" role="toolbar">
	<?= Html::a(Yii::t('catalog', 'Create'), $createUrl, ['class' => 'btn btn-primary']) ?>
</div>

<div>
	<?php $form = ActiveForm::begin([
		'enableClientValidation' => false,
	]) ?>
		<?= Html::dropDownList('category_id', $search->category_id, $categories, ['class' => 'form-control']) ?>
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
			'attribute' => 'price',
			'format' => 'html',
			'value' => function ($model) {
				return PriceHelper::render('span', $model->price, $model->currency);
			},
		],
		[
			'class' => 'yii\grid\ActionColumn',
			'options' => ['style' => 'width: 50px;'],
			'template' => '{update} {delete}',
			'urlCreator' => function ($action, $model, $key, $index, $column) use ($search) {
				$params = is_array($key) ? $key : ['id' => (string) $key];
				if (!empty($search->category_id)) {
					$params['category_id'] = $search->category_id;
				}
				$params[0] = $column->controller ? $column->controller . '/' . $action : $action;
				return Url::toRoute($params);
			},
		],
	],
]) ?>
