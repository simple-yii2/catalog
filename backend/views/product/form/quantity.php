<?php

use yii\helpers\Html;

$storesName = Html::getInputName($model, 'stores');

?>
<fieldset>
	<?= Html::hiddenInput($storesName, '') ?>
	<?php foreach ($model->stores as $store) {
		echo $form->field($store, 'quantity')->label($store->name)->textInput([
			'name' => $storesName . '[' . $store->store_id . '][quantity]',
		]);
	} ?>
</fieldset>
