<?php

use yii\bootstrap\Tabs;

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

//tabs
$tabs = [];
$tabs[] = [
	'label' => Yii::t('catalog', 'General'),
	'content' => $this->render('form/general', ['form' => $form, 'model' => $model]),
	'active' => $active == 'general',
];
if (Yii::$app->controller->module->propertiesEnabled) {
	$tabs[] = [
		'label' => Yii::t('catalog', 'Properties'),
		'content' => $this->render('form/properties', ['form' => $form, 'model' => $model]),
		'active' => $active == 'properties',
	];
}
$tabs[] = [
	'label' => Yii::t('catalog', 'Purchase'),
	'content' => $this->render('form/purchase', ['form' => $form, 'model' => $model]),
	'active' => $active == 'purchase',
];
$tabs[] = [
	'label' => Yii::t('catalog', 'Delivery'),
	'content' => $this->render('form/delivery', ['form' => $form, 'model' => $model]),
	'active' => $active == 'delivery',
];
$tabs[] = [
	'label' => Yii::t('catalog', 'Recommended'),
	'content' => $this->render('form/recommended', ['form' => $form, 'model' => $model]),
	'active' => $active == 'recommended',
];
$tabs[] = [
	'label' => Yii::t('catalog', 'Quantity'),
	'content' => $this->render('form/quantity', ['form' => $form, 'model' => $model]),
	'active' => $active == 'quantity',
];

?>
<?= Tabs::widget(['items' => $tabs]) ?>
