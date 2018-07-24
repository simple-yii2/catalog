<?php

use cms\catalog\common\models\Currency;

//currencies
$currencies = ['' => ''];
foreach (Currency::find()->all() as $item)
	$currencies[$item->id] = $item->name;

?>
<fieldset>
	<?= $activeForm->field($form, 'currency_id')->dropDownList($currencies) ?>
	<?= $activeForm->field($form, 'price')->textInput() ?>
	<?= $activeForm->field($form, 'oldPrice')->textInput() ?>
</fieldset>
