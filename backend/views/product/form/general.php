<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use cms\catalog\backend\forms\ProductBarcodeForm;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Vendor;
use dkhlystov\uploadimage\widgets\UploadImages;
use dkhlystov\widgets\Chosen;

//thumb size
$module = Yii::$app->controller->module;
$thumbWidth = ArrayHelper::getValue($module, 'productThumbWidth', 100);
$thumbHeight = ArrayHelper::getValue($module, 'productThumbHeight', 100);

//label suffix
$imageSize = '<br><span class="label label-default">' . $thumbWidth . '&times' . $thumbHeight . '</span>';

//categories
$categories = ['' => ''];
$query = Category::find()->orderBy(['lft' => SORT_ASC]);
foreach ($query->all() as $item) {
    if ($item->isLeaf() && $item->active)
        $categories[$item->id] = $item->path;
}

//description settings
$settings = [
    'minHeight' => 200,
    'toolbarFixedTopOffset' => 50,
    'plugins' => [
        'video',
        'table',
    ],
];
if (isset(Yii::$app->storage) && (Yii::$app->storage instanceof dkhlystov\storage\components\StorageInterface)) {
    $settings['imageUpload'] = Url::toRoute('image');
    $settings['fileUpload'] = Url::toRoute('file');
}


//vendors
$vendors = ['' => ''];
$query = Vendor::find()->orderBy(['name' => SORT_ASC]);
foreach ($query->all() as $item)
    $vendors[$item->id] = $item->name;

?>
<fieldset>
    <?= $activeForm->field($form, 'active')->checkbox() ?>
    <?= $activeForm->field($form, 'images')->label($form->getAttributeLabel('images') . $imageSize)->widget(UploadImages::className(), [
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
    <?= $activeForm->field($form, 'category_id')->widget(Chosen::className(), [
        'items' => $categories,
        'placeholder' => ' ',
        'noResultText' => Yii::t('catalog', 'No results matched'),
    ]) ?>
    <?= $activeForm->field($form, 'name') ?>
    <?= $activeForm->field($form, 'model') ?>
    <?= $activeForm->field($form, 'description')->widget(\vova07\imperavi\Widget::className(), ['settings' => $settings]) ?>
    <?php if (Yii::$app->controller->module->vendorEnabled) echo $activeForm->field($form, 'vendor_id')->widget(Chosen::className(), [
        'items' => $vendors,
        'placeholder' => ' ',
        'noResultText' => Yii::t('catalog', 'No results matched'),
    ]) ?>
    <?= $activeForm->field($form, 'countryOfOrigin') ?>
    <?php if (Yii::$app->controller->module->barcodeEnabled) echo $activeForm->field($form, 'barcodes')->widget('dkhlystov\widgets\ArrayInput', [
        'itemClass' => ProductBarcodeForm::className(),
        'columns' => [
            'barcode',
        ],
        'addLabel' => Yii::t('catalog', 'Add'),
        'removeLabel' => Yii::t('catalog', 'Remove'),
    ]) ?>
</fieldset>
