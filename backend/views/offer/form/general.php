<?php

use yii\helpers\ArrayHelper;

use cms\catalog\backend\models\OfferBarcodeForm;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Vendor;
use dkhlystov\uploadimage\widgets\UploadImages;
use dkhlystov\widgets\Chosen;

//thumb size
$module = Yii::$app->controller->module;
$thumbWidth = ArrayHelper::getValue($module, 'offerThumbWidth', 100);
$thumbHeight = ArrayHelper::getValue($module, 'offerThumbHeight', 100);

//label suffix
$imageSize = '<br><span class="label label-default">' . $thumbWidth . '&times' . $thumbHeight . '</span>';

//categories
$categories = ['' => ''];
$query = Category::find()->orderBy(['lft' => SORT_ASC]);
foreach ($query->all() as $item) {
	if ($item->isLeaf() && $item->active)
		$categories[$item->id] = $item->path;
}

//vendors
$vendors = ['' => ''];
$query = Vendor::find()->orderBy(['name' => SORT_ASC]);
foreach ($query->all() as $item)
	$vendors[$item->id] = $item->name;

?>
<fieldset>
	<?= $form->field($model, 'active')->checkbox() ?>
	<?= $form->field($model, 'images')->label($model->getAttributeLabel('images') . $imageSize)->widget(UploadImages::className(), [
		'id' => 'goods-images',
		'fileKey' => 'file',
		'thumbKey' => 'thumb',
		'thumbWidth' => $thumbWidth,
		'thumbHeight' => $thumbHeight,
		'data' => function($item) {
			return [
				'id' => $item['id'],
			];
		},
	]) ?>
	<?= $form->field($model, 'category_id')->widget(Chosen::className(), [
		'items' => $categories,
		'placeholder' => ' ',
		'noResultText' => Yii::t('catalog', 'No results matched'),
	]) ?>
	<?= $form->field($model, 'name') ?>
	<?= $form->field($model, 'model') ?>
	<?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>
	<?php if (Yii::$app->controller->module->vendorEnabled) echo $form->field($model, 'vendor_id')->widget(Chosen::className(), [
		'items' => $vendors,
		'placeholder' => ' ',
		'noResultText' => Yii::t('catalog', 'No results matched'),
	]) ?>
	<?= $form->field($model, 'countryOfOrigin') ?>
	<?php if (Yii::$app->controller->module->barcodeEnabled) echo $form->field($model, 'barcodes')->widget('dkhlystov\widgets\ArrayInput', [
		'itemClass' => OfferBarcodeForm::className(),
		'columns' => [
			'barcode',
		],
		'addLabel' => Yii::t('catalog', 'Add'),
		'removeLabel' => Yii::t('catalog', 'Remove'),
	]) ?>
</fieldset>
