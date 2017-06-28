<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use cms\catalog\common\models\Settings;
use dkhlystov\uploadimage\widgets\UploadImage;

$settings = Settings::find()->one();
if ($settings === null)
	$settings = new Settings;

$imageSize = '<br><span class="label label-default">' . $settings->vendorImageWidth . '&times' . $settings->vendorImageHeight . '</span>';
$height = $settings->vendorImageHeight / $settings->vendorImageWidth * 282;
if ($height < 20)
	$height = 20;

?>
<?php $f = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<fieldset>
		<?= $f->field($form, 'name') ?>
		<?= $f->field($form, 'description')->textarea(['rows' => 5]) ?>
		<?= $f->field($form, 'url') ?>
		<?= $f->field($form, 'file')->label($form->getAttributeLabel('file') . $imageSize)->widget(UploadImage::className(), [
			'thumbAttribute' => 'thumb',
			'thumbWidth' => $settings->vendorImageWidth,
			'thumbHeight' => $settings->vendorImageHeight,
			'width' => 282,
			'height' => $height,
		]) ?>
	</fieldset>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('catalog', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
