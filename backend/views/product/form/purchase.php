<?php

use cms\catalog\models\Currency;
use cms\catalog\models\Product;

//currencies
$currencies = ['' => ''];
foreach (Currency::find()->all() as $item) {
    $currencies[$item->id] = $item->name;
}

?>
<fieldset>
    <?= $form->field($model, 'currency_id')->dropDownList($currencies) ?>
    <?= $form->field($model, 'price') ?>
    <?= $form->field($model, 'oldPrice') ?>
    <?= $form->field($model, 'availability')->dropDownList(Product::getAvailabilityNames()) ?>
</fieldset>
