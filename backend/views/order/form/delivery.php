<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use cms\catalog\common\helpers\DeliveryHelper;
use cms\catalog\models\Order;
use cms\catalog\common\models\Store;
use cms\catalog\delivery\Delivery;

$order = new Order;
$model->assignTo($order);
$delivery = $model->delivery;

// Delivery items
$deliveryItems = ['' => ''];
foreach (DeliveryHelper::getDeliveryClasses($order) as $key => $class) {
    $deliveryItems[$key] = $class::getName();
}

// Delivery data
$deliveryData = DeliveryHelper::getDeliveryData($order);

// Store items
$storeItems = ['' => ''];
foreach (Store::find()->all() as $item) {
    $storeItems[$item->id] = $item->name;
}

// Fields
$fields = Delivery::getFields();

// Field options
$config = [];
foreach ($fields as $field) {
    $config[$field] = ['options' => ['class' => 'form-group hidden']];
}
$class = DeliveryHelper::getDeliveryClass($delivery->delivery);
$availableFields = $class === null ? [] : $class::getAvailableFields();
foreach ($availableFields as $field) {
    Html::removeCssClass($config[$field]['options'], 'hidden');
}

?>
<?= Html::beginTag('div', ['id' => 'delivery-block', 'data-fields' => $fields, 'data-delivery' => $deliveryData, 'class' => 'row']) ?>
    <div class="col-md-6">
        <?= $form->field($delivery, 'delivery')->dropDownList($deliveryItems) ?>
        <?= $form->field($delivery, 'price') ?>
        <?= $form->field($delivery, 'days') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($delivery, 'store_id', $config['store_id'])->dropDownList($storeItems) ?>
        <?= $form->field($delivery, 'serviceName', $config['serviceName']) ?>
        <?= $form->field($delivery, 'city', $config['city']) ?>
        <?= $form->field($delivery, 'street', $config['street']) ?>
        <?= $form->field($delivery, 'house', $config['house']) ?>
        <?= $form->field($delivery, 'apartment', $config['apartment']) ?>
        <?= $form->field($delivery, 'entrance', $config['entrance']) ?>
        <?= $form->field($delivery, 'entryphone', $config['entryphone']) ?>
        <?= $form->field($delivery, 'floor', $config['floor']) ?>
        <?= $form->field($delivery, 'recipient', $config['recipient']) ?>
        <?= $form->field($delivery, 'phone', $config['phone'])->widget(MaskedInput::className(), ['mask' => Yii::t('catalog', '+1-999-999-9999')]) ?>
        <?= $form->field($delivery, 'trackingCode', $config['trackingCode']) ?>
    </div>
<?= Html::endTag('div') ?>
