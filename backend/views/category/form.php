<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use cms\catalog\backend\assets\CategoryFormAsset;
use cms\catalog\backend\models\CategoryPropertyForm;
use cms\catalog\common\models\CategoryProperty;

CategoryFormAsset::register($this);

$typesWithValues = CategoryProperty::getTypesWithValues();

?>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<?= $form->field($model, 'active')->checkbox() ?>

	<?= $form->field($model, 'title') ?>

	<?= $form->field($model, 'properties')->widget('dkhlystov\grid\ArrayInput', [
		'itemClass' => CategoryPropertyForm::className(),
		'columns' => [
			'title',
			['attribute' => 'type', 'items' => CategoryProperty::getTypeNames(), 'inputOptions' => ['class' => 'form-control property-type']],
			['attribute' => 'values', 'content' => function($model, $key, $index, $column) use ($typesWithValues) {
				$id = Html::hiddenInput($column->getInputName($model, $index, 'id'), $model->getId());

				$name = $column->getInputName($model, $index, 'values');
				$values = Html::hiddenInput($name, '', ['class' => 'property-values']);
				foreach ($model->values as $value)
					$values .= Html::hiddenInput($name . '[]', $value);

				$options = ['class' => 'btn btn-default property-values'];
				if (!in_array($model->type, $typesWithValues))
					$options['disabled'] = true;
				$button = Html::button($model->getAttributeLabel('values'), $options);

				return $id . $values . $button;
			}],
		],
		'addLabel' => Yii::t('catalog', 'Add'),
		'removeLabel' => Yii::t('catalog', 'Remove'),
		'options' => [
			'class' => 'category-properties',
			'data-types-with-values' => $typesWithValues,
			'data-modal-title' => Yii::t('catalog', 'Property values'),
			'data-modal-add' => Yii::t('catalog', 'Add'),
			'data-modal-save' => Yii::t('catalog', 'Save'),
			'data-modal-cancel' => Yii::t('catalog', 'Cancel'),
		],
	]) ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('catalog', 'Cancel'), ['index', 'id' => $id], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
