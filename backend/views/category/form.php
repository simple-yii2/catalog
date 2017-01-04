<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<?= $form->field($model, 'active')->checkbox() ?>

	<?= $form->field($model, 'title') ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('catalog', 'Cancel'), ['index', 'id' => $id], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
