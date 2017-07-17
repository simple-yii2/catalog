<?php

use cms\catalog\common\models\Currency;

//currencies
$currencies = ['' => ''];
foreach (Currency::find()->all() as $item)
	$currencies[$item->id] = $item->name;

//module
$module = Yii::$app->controller->module;

?>
<fieldset>
	<?= $form->field($model, 'currency_id')->dropDownList($currencies) ?>
	<?= $form->field($model, 'price')->textInput() ?>
	<?= $form->field($model, 'oldPrice')->textInput() ?>
	<?= $form->field($model, 'storeAvailable')->checkbox() ?>
	<?= $form->field($model, 'pickupAvailable')->checkbox() ?>
	<?php if ($module->deliveryEnabled) echo $form->field($model, 'deliveryAvailable')->checkbox() ?>
</fieldset>
