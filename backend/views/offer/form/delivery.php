<?php

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

//delivery
$deliveryName = Html::getInputName($formModel, 'delivery');

?>
<fieldset>
	<div>
		<?= Html::activeCheckbox($formModel, 'defaultDelivery') ?>
	</div>
	<?= Gridview::widget([
		'dataProvider' => new ArrayDataProvider([
			'allModels' => $formModel->delivery,
			'pagination' => false,
		]),
		'summary' => '',
		'options' => ['class' => 'offer-delivery'],
		'columns' => [
			[
				'attribute' => 'name',
				'format' => 'html',
				'content' => function($model, $key, $index, $column) use ($formModel, $deliveryName) {
					$checkbox = Html::activeCheckbox($model, 'active', [
						'label' => $model->name,
						'name' => $deliveryName . '[' . $model->delivery_id . '][active]',
						'disabled' => $formModel->defaultDelivery != 0,
					]);

					return Html::tag('div', $checkbox, ['class' => 'checkbox']);
				},
			],
			[
				'attribute' => 'cost',
				'format' => 'html',
				'options' => ['style' => 'width: 200px;'],
				'content' => function($model, $key, $index, $column) use ($formModel, $deliveryName) {
					$base = $deliveryName . '[' . $model->delivery_id . ']';

					$hidden = Html::activeHiddenInput($model, 'defaultCost', ['name' => $base . '[defaultCost]']);
					$checkbox = Html::checkbox('', $model->defaultCost, [
						'title' => $model->getAttributeLabel('defaultCost'),
						'disabled' => $formModel->defaultDelivery || !$model->active,
					]);
					$addon = Html::tag('span', $checkbox, ['class' => 'input-group-addon']);
					$input = Html::activeTextInput($model, 'cost', [
						'class' => 'form-control',
						'name' => $base . '[cost]',
						'placeholder' => $model->getTemplate()->cost,
						'disabled' => $formModel->defaultDelivery || !$model->active || $model->defaultCost,
					]);

					return $hidden . Html::tag('div', $addon . $input, ['class' => 'input-group offer-delivery-cost']);
				},
			],
			[
				'attribute' => 'days',
				'format' => 'html',
				'options' => ['style' => 'width: 200px;'],
				'content' => function($model, $key, $index, $column) use ($formModel, $deliveryName) {
					$base = $deliveryName . '[' . $model->delivery_id . ']';

					$hidden = Html::activeHiddenInput($model, 'defaultDays', ['name' => $base . '[defaultDays]']);
					$checkbox = Html::checkbox('', $model->defaultDays, [
						'title' => $model->getAttributeLabel('defaultDays'),
						'disabled' => $formModel->defaultDelivery || !$model->active,
					]);
					$addon = Html::tag('span', $checkbox, ['class' => 'input-group-addon']);
					$input = Html::activeTextInput($model, 'days', [
						'class' => 'form-control',
						'name' => $base . '[days]',
						'placeholder' => $model->getTemplate()->days,
						'disabled' => $formModel->defaultDelivery || !$model->active || $model->defaultDays,
					]);

					return $hidden . Html::tag('div', $addon . $input, ['class' => 'input-group offer-delivery-days']);
				},
			],
		],
	]) ?>
</fieldset>
