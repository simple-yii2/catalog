<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use cms\catalog\common\models\Currency;

$title = Yii::t('catalog', 'Catalog settings');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
	$title,
];

//currencies
$currencies = [];
foreach (Currency::find()->all() as $currency)
	$currencies[$currency->id] = $currency->name;

?>
<h1><?= Html::encode($title) ?></h1>

<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<fieldset>
		<?= $form->field($model, 'defaultCurrency_id')->dropDownList($currencies) ?>
	</fieldset>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('catalog', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
