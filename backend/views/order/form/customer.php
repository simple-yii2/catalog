<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\widgets\MaskedInput;

$customer = $model->customer;

?>
<div class="row">
    <div class="col-md-6">
        <?= Html::activeHiddenInput($customer, 'user_id') ?>
        <?= $form->field($customer, 'user')->widget(AutoComplete::className(), [
            'options' => ['class' => 'form-control', 'data-value' => $customer->user],
            'clientOptions' => ['source' => Url::toRoute('customer')],
        ]) ?>
        <?= $form->field($customer, 'name') ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($customer, 'phone')->widget(MaskedInput::className(), ['mask' => Yii::t('catalog', '+1-999-999-9999')]) ?>
        <?= $form->field($customer, 'email') ?>
    </div>
</div>
