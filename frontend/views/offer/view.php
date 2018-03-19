<?php

use yii\helpers\Html;
use cms\catalog\frontend\assets\OfferAsset;
use cms\catalog\frontend\helpers\CurrencyHelper;
use cms\catalog\frontend\helpers\PriceHelper;
use cms\catalog\frontend\helpers\PropertyHelper;
use dkhlystov\widgets\Lightbox;

OfferAsset::register($this);

$title = $model->name;
if (!empty($model->model)) {
	$title .= ' ' . $model->model;
}

$this->title = $title . ' | ' . Yii::$app->name;



//breadcrumbs
$breadcrumbs = [
	// ['label' => Yii::t('catalog', 'Categories'), 'url' => ['index']],
];
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
	$s = Html::a(Html::img($object->thumb), $object->file);
	if (empty($mainImage)) $mainImage = $s;
	else $thumbs .= $s;
}
if (!empty($thumbs))
	$thumbs = Html::tag('div', $thumbs, ['class' => 'offer-thumbs']);



//price
$currency = CurrencyHelper::getCurrency($model->currency_id);

$s = PriceHelper::render('span', $model->price, $currency);
$price = Html::tag('div', $s, ['class' => 'offer-price']);

$s = '';
if (!empty($model->oldPrice))
	$s = PriceHelper::render('s', $model->oldPrice, $currency);
$oldPrice = Html::tag('div', $s, ['class' => 'offer-old-price']);



//properties
$properties = [];
foreach ($model->getProperties()->with(['categoryProperty'])->all() as $item) {
	$properties[] = ['label' => $item->categoryProperty->name, 'value' => PropertyHelper::renderValue($item)];
}
$formatter = Yii::$app->getFormatter();
$properties[] = ['label' => Yii::t('catalog', 'Length'), 'value' => $formatter->asInteger($model->length) . ' ' . Yii::t('catalog', 'mm')];
$properties[] = ['label' => Yii::t('catalog', 'Width'), 'value' => $formatter->asInteger($model->width) . ' ' . Yii::t('catalog', 'mm')];
$properties[] = ['label' => Yii::t('catalog', 'Height'), 'value' => $formatter->asInteger($model->height) . ' ' . Yii::t('catalog', 'mm')];
$properties[] = ['label' => Yii::t('catalog', 'Weight'), 'value' => $formatter->asDecimal($model->weight) . ' ' . Yii::t('catalog', 'kg')];



?>
<h1><?= Html::encode($title) ?></h1>

<div class="row">
	<div class="col-sm-6">
		<?php Lightbox::begin(['options' => ['class' => 'offer-images']]); ?>
			<?= $mainImage ?>
			<?= $thumbs ?>
		<?php Lightbox::end(); ?>
	</div>
	<div class="col-sm-6">
		<?= $price ?>
		<?= $oldPrice ?>
		<?php if (!empty($model->description)) echo $this->render('view/description', ['model' => $model]) ?>
	</div>
</div>

<?php if (!empty($properties)) echo $this->render('view/properties', ['properties' => $properties]) ?>
