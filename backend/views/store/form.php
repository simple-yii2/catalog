<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use cms\catalog\common\models\Store;

?>
<?php $f = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<fieldset>
		<?= $f->field($form, 'type')->dropDownList(Store::getTypeNames()) ?>
		<?= $f->field($form, 'name') ?>
	</fieldset>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('catalog', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
