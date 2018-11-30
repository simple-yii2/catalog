<?php

use yii\bootstrap\Tabs;

//fields by tab for determine tab with error
$tabFields = [
    // 'general' => ['active', 'images[]', 'category_id', 'name', 'model', 'description', 'vendor_id', 'countryOfOrigin', 'barcodes[]'],
    // 'properties' => ['category_id', 'properties[]', 'length', 'width', 'height', 'weight'],
    // 'purchase' => ['currency_id', 'price', 'oldPrice'],
    // 'recommended' => ['recommended[]'],
    // 'quantity' => ['stores[]'],
];

//active tab (if there are errors, make tab with first error active)
$active = '';
$errorFields = array_keys($model->getFirstErrors());
foreach ($tabFields as $tab => $fields) {
    foreach ($fields as $field) {
        if (in_array($field, $errorFields)) {
            $active = $tab;
            break;
        }
    }
    if (!empty($active)) {
        break;
    }
}
if (empty($active)) {
    $active = 'general';
}

//tabs
$tabs = [];
$tabs[] = [
    'label' => Yii::t('catalog', 'General'),
    'content' => $this->render('general', ['form' => $form, 'model' => $model]),
    'active' => $active == 'general',
];
$tabs[] = [
    'label' => Yii::t('catalog', 'Customer'),
    'content' => $this->render('customer', ['form' => $form, 'model' => $model]),
    'active' => $active == 'customer',
];
$tabs[] = [
    'label' => Yii::t('catalog', 'Products'),
    'content' => $this->render('products', ['form' => $form, 'model' => $model]),
    'active' => $active == 'products',
];
$tabs[] = [
    'label' => Yii::t('catalog', 'Delivery'),
    'content' => $this->render('delivery', ['form' => $form, 'model' => $model]),
    'active' => $active == 'delivery',
];

//render
echo Tabs::widget(['items' => $tabs]);
