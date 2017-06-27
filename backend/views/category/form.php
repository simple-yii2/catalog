<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use cms\catalog\backend\assets\CategoryFormAsset;
use cms\catalog\backend\models\PropertyForm;
use cms\catalog\common\models\Property;

CategoryFormAsset::register($this);

$typesWithValues = Property::getTypesWithValues();

?>
<?php $f = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<?= $f->field($form, 'active')->checkbox() ?>

	<?= $f->field($form, 'title') ?>

	<?= $f->field($form, 'properties')->widget('dkhlystov\widgets\ArrayInput', [
		'itemClass' => PropertyForm::className(),
		'columns' => [
			'name',
			['attribute' => 'type', 'items' => Property::getTypeNames(), 'inputOptions' => ['class' => 'form-control property-type']],
			['attribute' => 'values', 'content' => function($model, $key, $index, $column) use ($typesWithValues) {
				if ($model->getReadOnly())
					return '';

				$id = Html::hiddenInput($column->getInputName($model, $index, 'id'), $model->id);

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
		'readOnlyAttribute' => 'readOnly',
		'options' => [
			'class' => 'category-properties',
			'data-types-with-values' => $typesWithValues,
			'data-modal-title' => Yii::t('catalog', 'Values'),
			'data-modal-add' => Yii::t('catalog', 'Add'),
			'data-modal-ok' => Yii::t('catalog', 'OK'),
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
