<?php

use yii\bootstrap\Tabs;

//fields by tab for determine tab with error
$tabFields = [
    'general' => ['active', 'images[]', 'category_id', 'name', 'model', 'description', 'vendor_id', 'countryOfOrigin', 'barcodes[]'],
    'properties' => ['category_id', 'properties[]', 'length', 'width', 'height', 'weight'],
    'purchase' => ['currency_id', 'price', 'oldPrice'],
    'recommended' => ['recommended[]'],
    // 'quantity' => ['stores[]'],
];

//active tab (if there are errors, make tab with first error active)
$active = '';
$errorFields = array_keys($form->getFirstErrors());
foreach ($tabFields as $tab => $fields) {
    foreach ($fields as $field) {
        if (in_array($field, $errorFields)) {
            $active = $tab;
            break;
        }
    }
    if (!empty($active))
        break;
}
if (empty($active))
    $active = 'general';

//tabs
$tabs = [];
$tabs[] = [
    'label' => Yii::t('catalog', 'General'),
    'content' => $this->render('general', ['activeForm' => $activeForm, 'form' => $form]),
    'active' => $active == 'general',
];
if (Yii::$app->controller->module->propertiesEnabled) {
    $tabs[] = [
        'label' => Yii::t('catalog', 'Properties'),
        'content' => $this->render('properties', ['activeForm' => $activeForm, 'form' => $form]),
        'active' => $active == 'properties',
    ];
}
$tabs[] = [
    'label' => Yii::t('catalog', 'Purchase'),
    'content' => $this->render('purchase', ['activeForm' => $activeForm, 'form' => $form]),
    'active' => $active == 'purchase',
];
// $tabs[] = [
//     'label' => Yii::t('catalog', 'Recommended'),
//     'content' => $this->render('recommended', ['activeForm' => $activeForm, 'form' => $form]),
//     'active' => $active == 'recommended',
// ];
// if (Yii::$app->controller->module->storeEnabled) {
//     $tabs[] = [
//         'label' => Yii::t('catalog', 'Quantity'),
//         'content' => $this->render('quantity', ['activeForm' => $activeForm, 'form' => $form]),
//         'active' => $active == 'quantity',
//     ];
// }

//render
echo Tabs::widget(['items' => $tabs]);
