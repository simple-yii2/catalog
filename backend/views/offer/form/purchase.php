<?php

use cms\catalog\common\models\Currency;

//currencies
$currencies = ['' => ''];
foreach (Currency::find()->all() as $item)
	$currencies[$item->id] = $item->code;

//purchase
$options = [];
if (empty($model->currency_id))
	$options['disabled'] = true;

?>
<fieldset>
	<?= $form->field($model, 'currency_id')->dropDownList($currencies) ?>
	<?= $form->field($model, 'price')->textInput($options) ?>
	<?= $form->field($model, 'oldPrice')->textInput($options) ?>
	<?= $form->field($model, 'storeAvailable')->checkbox() ?>
	<?= $form->field($model, 'pickupAvailable')->checkbox() ?>
	<?= $form->field($model, 'deliveryAvailable')->checkbox() ?>
</fieldset>
