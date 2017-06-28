<?php

use cms\catalog\backend\models\OfferBarcodeForm;
use cms\catalog\common\models\Vendor;
use dkhlystov\uploadimage\widgets\UploadImages;

$settings = Yii::$app->params['catalogSettings'];

//images
$width = $settings->offerImageWidth;
$height = $settings->offerImageHeight;
$imageSize = '<br><span class="label label-default">' . $width . '&times' . $height . '</span>';

//vendors
$vendors = ['' => ''];
$query = Vendor::find();
foreach ($query->all() as $model)
	$vendors[$model->id] = $model->name;

?>
<fieldset>
	<?= $activeForm->field($formModel, 'active')->checkbox() ?>
	<?= $activeForm->field($formModel, 'images')->label($formModel->getAttributeLabel('images') . $imageSize)->widget(UploadImages::className(), [
		'id' => 'goods-images',
		'fileKey' => 'file',
		'thumbKey' => 'thumb',
		'thumbWidth' => $width,
		'thumbHeight' => $height,
		'data' => function($item) {
			return [
				'id' => $item['id'],
			];
		},
	]) ?>
	<?= $activeForm->field($formModel, 'name') ?>
	<?= $activeForm->field($formModel, 'model') ?>
	<?= $activeForm->field($formModel, 'description')->textarea(['rows' => 5]) ?>
	<?= $activeForm->field($formModel, 'vendor_id')->dropDownList($vendors) ?>
	<?= $activeForm->field($formModel, 'countryOfOrigin') ?>
	<?= $activeForm->field($formModel, 'barcodes')->widget('dkhlystov\widgets\ArrayInput', [
		'itemClass' => OfferBarcodeForm::className(),
		'columns' => [
			'barcode',
		],
		'addLabel' => Yii::t('catalog', 'Add'),
		'removeLabel' => Yii::t('catalog', 'Remove'),
	]) ?>
</fieldset>
