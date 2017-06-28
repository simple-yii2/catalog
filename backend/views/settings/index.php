<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use cms\catalog\common\models\Currency;

$title = Yii::t('catalog', 'Catalog settings');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	$title,
];


$currencies = [];
foreach (Currency::find()->all() as $currency)
	$currencies[$currency->id] = $currency->code;

?>
<h1><?= Html::encode($title) ?></h1>

<?php $f = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<fieldset>
		<?= $f->field($form, 'defaultCurrency_id')->dropDownList($currencies) ?>
		<?= $f->field($form, 'vendorImageWidth') ?>
		<?= $f->field($form, 'vendorImageHeight') ?>
		<?= $f->field($form, 'offerImageWidth') ?>
		<?= $f->field($form, 'offerImageHeight') ?>
	</fieldset>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('catalog', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
