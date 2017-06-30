<?php

use yii\helpers\Html;

$storesName = Html::getInputName($formModel, 'stores');

?>
<fieldset>
	<?= Html::hiddenInput($storesName, '') ?>
	<?php foreach ($formModel->stores as $store) {
		echo $activeForm->field($store, 'quantity')->label($store->name)->textInput([
			'name' => $storesName . '[' . $store->store_id . '][quantity]',
		]);
	} ?>
</fieldset>
