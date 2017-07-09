<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;

use cms\catalog\backend\assets\OfferFormAsset;

OfferFormAsset::register($this);

//fields by tab for determine tab with error
$tabFields = [
	'properties' => ['category_id', 'properties[]', 'length', 'width', 'height', 'weight'],
	'purchase' => ['currency_id', 'price', 'oldPrice', 'storeAvailable', 'pickupAvailable', 'deliveryAvailable'],
	'delivery' => ['delivery[]'],
	'recommended' => ['recommended[]'],
	'quantity' => ['stores[]'],
];

//active tab (if there are errors, make tab with first error active)
$active = 'general';
$errorFields = array_keys($model->getFirstErrors());
foreach ($tabFields as $tab => $fields) {
	foreach ($fields as $field) {
		if (in_array($field, $errorFields)) {
			$active = $tab;
			break;
		}
	}
	if ($active != 'general')
		break;
}

?>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
	'options' => [
		'data-properties-url' => Url::toRoute(['properties', 'id' => $model->getObject()->id]),
	],
]); ?>

	<?= Tabs::widget(['items' => [
		[
			'label' => Yii::t('catalog', 'General'),
			'content' => $this->render('form/general', ['form' => $form, 'model' => $model]),
			'active' => $active == 'general',
		],
		[
			'label' => Yii::t('catalog', 'Properties'),
			'content' => $this->render('form/properties', ['form' => $form, 'model' => $model]),
			'active' => $active == 'properties',
		],
		[
			'label' => Yii::t('catalog', 'Purchase'),
			'content' => $this->render('form/purchase', ['form' => $form, 'model' => $model]),
			'active' => $active == 'purchase',
		],
		[
			'label' => Yii::t('catalog', 'Delivery'),
			'content' => $this->render('form/delivery', ['form' => $form, 'model' => $model]),
			'active' => $active == 'delivery',
		],
		[
			'label' => Yii::t('catalog', 'Recommended'),
			'content' => $this->render('form/recommended', ['form' => $form, 'model' => $model]),
			'active' => $active == 'recommended',
		],
		[
			'label' => Yii::t('catalog', 'Quantity'),
			'content' => $this->render('form/quantity', ['form' => $form, 'model' => $model]),
			'active' => $active == 'quantity',
		],
	]]) ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('news', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('news', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
