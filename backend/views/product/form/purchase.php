<?php

use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Product;

//currencies
$currencies = ['' => ''];
foreach (Currency::find()->all() as $item)
	$currencies[$item->id] = $item->name;

?>
<fieldset>
	<?= $activeForm->field($form, 'currency_id')->dropDownList($currencies) ?>
	<?= $activeForm->field($form, 'price') ?>
    <?= $activeForm->field($form, 'oldPrice') ?>
	<?= $activeForm->field($form, 'availability')->dropDownList(Product::getAvailabilityNames()) ?>
</fieldset>
