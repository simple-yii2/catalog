<?php

use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Product;

//currencies
$currencies = ['' => ''];
foreach (Currency::find()->all() as $item) {
    $currencies[$item->id] = $item->name;
}

?>
<fieldset>
    <?= $activeForm->field($model, 'currency_id')->dropDownList($currencies) ?>
    <?= $activeForm->field($model, 'price') ?>
    <?= $activeForm->field($model, 'oldPrice') ?>
    <?= $activeForm->field($model, 'availability')->dropDownList(Product::getAvailabilityNames()) ?>
</fieldset>
