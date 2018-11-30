<?php

use dkhlystov\widgets\Datepicker;
use cms\catalog\common\models\Order;

?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'number') ?>
        <?= $form->field($model, 'issueDate')->widget(Datepicker::className()) ?>
        <?= $form->field($model, 'discount', ['inputTemplate' => '<div class="input-group">{input}<span class="input-group-addon">%</span></div>']) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'paymentTerm')->widget(Datepicker::className()) ?>
        <?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>
    </div>
</div>
