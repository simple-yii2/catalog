<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use cms\catalog\common\models\Settings;
use dkhlystov\uploadimage\widgets\UploadImage;

$settings = Settings::find()->one();
if ($settings === null)
	$settings = new Settings;

$imageSize = '<br><span class="label label-default">' . $settings->vendorImageWidth . '&times' . $settings->vendorImageHeight . '</span>';

$width = $settings->vendorImageWidth;
if ($width < 20) $width = 20;
if ($width > 282) $width = 282;
$height = $settings->vendorImageHeight / $settings->vendorImageWidth * $width;
if ($height < 20) $height = 20;

?>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<fieldset>
		<?= $form->field($model, 'name') ?>
		<?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>
		<?= $form->field($model, 'url') ?>
		<?= $form->field($model, 'file')->label($model->getAttributeLabel('file') . $imageSize)->widget(UploadImage::className(), [
			'thumbAttribute' => 'thumb',
			'thumbWidth' => $settings->vendorImageWidth,
			'thumbHeight' => $settings->vendorImageHeight,
			'width' => $width,
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
