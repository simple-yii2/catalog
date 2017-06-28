<?php

use cms\catalog\common\models\Vendor;
use cms\catalog\common\models\Settings;
use dkhlystov\uploadimage\widgets\UploadImages;

$settings = Settings::find()->one();
if ($settings === null)
	$settings = new Settings;

//images
$imageSize = '<br><span class="label label-default">' . $settings->offerImageWidth . '&times' . $settings->offerImageHeight . '</span>';

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
		'thumbWidth' => $settings->offerImageWidth,
		'thumbHeight' => $settings->offerImageHeight,
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
</fieldset>
