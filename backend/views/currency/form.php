<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<?php $activeForm = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<fieldset>
		<?= $activeForm->field($form, 'name') ?>
		<?= $activeForm->field($form, 'code') ?>
		<?= $activeForm->field($form, 'rate') ?>
		<?= $activeForm->field($form, 'precision')->dropDownList([0 => '1', 1 => '0.1', 2 => '0.01']) ?>
		<?= $activeForm->field($form, 'prefix') ?>
		<?= $activeForm->field($form, 'suffix') ?>
		<?= $activeForm->field($form, 'default')->checkbox() ?>
	</fieldset>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('catalog', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
