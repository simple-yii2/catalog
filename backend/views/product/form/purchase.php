<?php

use cms\catalog\common\models\Currency;

//currencies
$currencies = ['' => ''];
foreach (Currency::find()->all() as $item)
	$currencies[$item->id] = $item->name;

?>
<fieldset>
	<?= $form->field($model, 'currency_id')->dropDownList($currencies) ?>
	<?= $form->field($model, 'price')->textInput() ?>
	<?= $form->field($model, 'oldPrice')->textInput() ?>
</fieldset>
