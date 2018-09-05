<?php

use yii\helpers\Html;
use cms\catalog\common\helpers\CurrencyHelper;
use cms\catalog\common\helpers\PriceHelper;
use cms\catalog\frontend\assets\ProductViewAsset;
use cms\catalog\frontend\helpers\PropertyHelper;
use dkhlystov\widgets\Lightbox;

ProductViewAsset::register($this);



//title
$title = $model->getTitle();
$this->title = $title . ' | ' . Yii::$app->name;



//breadcrumbs
$breadcrumbs = [];
$category = $model->category;
if ($category !== null) {
    foreach ($category->parents()->all() as $object) {
        if (!$object->isRoot())
            $breadcrumbs[] = ['label' => $object->title, 'url' => ['index', 'alias' => $object->alias]];
    }
    $breadcrumbs[] = ['label' => $category->title, 'url' => ['index', 'alias' => $category->alias]];
}
$breadcrumbs[] = $title;
$this->params['breadcrumbs'] = $breadcrumbs;



//images
$mainImage = '';
$thumbs = '';
foreach ($model->images as $object) {
    $s = Html::a(Html::img($object->thumb, ['alt' => $title]), $object->file, ['title' => $title]);
    if (empty($mainImage)) {
        $mainImage = $s;
    } else {
        $thumbs .= $s;
    }
}
if (!empty($thumbs)) {
    $thumbs = Html::tag('div', $thumbs, ['class' => 'product-thumbs']);
}



//price
$currency = CurrencyHelper::getCurrency($model->currency_id);

$s = PriceHelper::render('span', $model->price, $currency);
$price = Html::tag('div', $s, ['class' => 'product-price']);

$s = '';
if (!empty($model->oldPrice)) {
    $s = PriceHelper::render('s', $model->oldPrice, $currency);
}
$oldPrice = Html::tag('div', $s, ['class' => 'product-old-price']);



//availability
$s = Html::encode($model->getAvailabilityName());
$availability = Html::tag('div', $s, ['class' => 'product-availability availability-' . $model->availability]);





//properties
$properties = [];
foreach ($model->getProperties()->with(['categoryProperty'])->all() as $item) {
    $properties[] = ['label' => $item->categoryProperty->name, 'value' => PropertyHelper::renderValue($item)];
}
$formatter = Yii::$app->getFormatter();
if (!empty($model->length)) {
    $properties[] = ['label' => Yii::t('catalog', 'Length'), 'value' => $formatter->asInteger($model->length) . ' ' . Yii::t('catalog', 'mm')];
}
if (!empty($model->width)) {
    $properties[] = ['label' => Yii::t('catalog', 'Width'), 'value' => $formatter->asInteger($model->width) . ' ' . Yii::t('catalog', 'mm')];
}
if (!empty($model->height)) {
    $properties[] = ['label' => Yii::t('catalog', 'Height'), 'value' => $formatter->asInteger($model->height) . ' ' . Yii::t('catalog', 'mm')];
}
if (!empty($model->weight)) {
    if ($model->weight < 1) {
        $value = $formatter->asInteger($model->weight * 1000) . ' ' . Yii::t('catalog', 'g');
    } else {
        $value = $formatter->asDecimal($model->weight) . ' ' . Yii::t('catalog', 'kg');
    }
    $properties[] = ['label' => Yii::t('catalog', 'Weight'), 'value' => $value];
}



?>
<h1><?= Html::encode($title) ?></h1>

<div class="row">
    <div class="col-sm-6 col-md-5 col-lg-4">
        <?php Lightbox::begin(['options' => ['class' => 'product-images']]); ?>
            <?= $mainImage ?>
            <?= $thumbs ?>
            <?= Html::tag('div', '<span class="glyphicon glyphicon-camera"></span>' . $model->imageCount, ['class' => 'product-image-count']) ?>
        <?php Lightbox::end(); ?>
    </div>
    <div class="col-sm-6 col-md-7 col-lg-8">
        <?= $price ?>
        <?= $oldPrice ?>
        <div class="product-title h5"><?= Html::encode($title) ?></div>
        <?= $availability ?>
        <?php if (!empty($model->description)) echo $this->render('view/description', ['model' => $model]) ?>
    </div>
</div>

<?php if (!empty($properties)) echo $this->render('view/properties', ['properties' => $properties]) ?>
