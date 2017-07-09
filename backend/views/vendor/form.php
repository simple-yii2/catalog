<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use cms\catalog\common\models\Settings;
use dkhlystov\uploadimage\widgets\UploadImage;

//thumb size
$module = Yii::$app->controller->module;
$thumbWidth = ArrayHelper::getValue($module, 'vendorThumbWidth', 100);
$thumbHeight = ArrayHelper::getValue($module, 'vendorThumbHeight', 100);

//label suffix
$imageSize = '<br><span class="label label-default">' . $thumbWidth . '&times' . $thumbHeight . '</span>';

//widget size
$width = $thumbWidth;
if ($width < 20) $width = 20;
if ($width > 282) $width = 282;
$height = $thumbHeight / $thumbWidth * $width;
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
			'thumbWidth' => $thumbWidth,
			'thumbHeight' => $thumbHeight,
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
