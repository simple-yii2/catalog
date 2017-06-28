<?php

use cms\catalog\common\models\Currency;

$currencies = ['' => ''];
foreach (Currency::find()->all() as $item)
	$currencies[$item->id] = $item->code;

$options = [];
if (empty($formModel->currency_id))
	$options['disabled'] = true;

?>
<fieldset>
	<?= $activeForm->field($formModel, 'storeAvailable')->checkbox() ?>
	<?= $activeForm->field($formModel, 'pickupAvailable')->checkbox() ?>
	<?= $activeForm->field($formModel, 'deliveryAvailable')->checkbox() ?>
	<?= $activeForm->field($formModel, 'currency_id')->dropDownList($currencies) ?>
	<?= $activeForm->field($formModel, 'price')->textInput($options) ?>
	<?= $activeForm->field($formModel, 'oldPrice')->textInput($options) ?>
</fieldset>
