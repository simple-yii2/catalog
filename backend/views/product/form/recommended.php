<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

//recommended
$dataProvider = new ArrayDataProvider([
	'allModels' => $model->recommended,
	'pagination' => false,
]);

$name = Html::getInputName($model, 'recommended');

?>
<fieldset>
	<div class="form-group">
		<div class="col-sm-9">
			<?= AutoComplete::widget([
				'id' => 'recommended-add',
				'options' => [
					'class' => 'form-control',
					'placeholder' => Yii::t('catalog', 'Search'),
					'data-url' => Url::toRoute(['recommended-add', 'id' => $model->getObject()->id]),
				],
				'clientOptions' => [
					'source' => Url::toRoute(['recommended-product']),
					'select' => new JsExpression('function(e, ui) {
						e.preventDefault();
						$(this).val("").data("item", ui.item).trigger("selected");
					}'),
				],
			]) ?>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-9">
			<?= Html::hiddenInput($name, '') ?>
			<?= GridView::widget([
				'id' => 'product-recommended',
				'dataProvider' => $dataProvider,
				'emptyText' => false,
				'summary' => '',
				'showHeader' => false,
				'tableOptions' => ['class' => 'table table-condensed'],
				'rowOptions' => function($model, $key, $index, $grid) {
					return ['data-id' => $model->getObject()->id];
				},
				'columns' => [
					[
						'format' => 'raw',
						'value' => function($model, $key, $index, $column) use ($name) {
							$result = Html::hiddenInput($name . '[][id]', $model->id);

							if (!empty($model->thumb))
								$result .= Html::img($model->thumb, ['height' => 20]) . '&nbsp;';

							$result .= Html::encode($model->name);

							return $result;
						},
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'options' => ['style' => 'width: 25px;'],
						'buttons' => [
							'remove' => function($url, $model, $key) {
								return Html::a('<span class="glyphicon glyphicon-remove"></span>', '#', [
									'class' => 'recommended-remove',
									'title' => Yii::t('catalog', 'Delete'),
									'data-pjax' => '0',
								]);
							},
						],
						'template' => '{remove}',
					],
				],
			]) ?>
		</div>
	</div>
</fieldset>
