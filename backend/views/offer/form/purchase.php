<fieldset>
	<?= $activeForm->field($formModel, 'storeAvailable')->checkbox() ?>
	<?= $activeForm->field($formModel, 'pickupAvailable')->checkbox() ?>
	<?= $activeForm->field($formModel, 'deliveryAvailable')->checkbox() ?>
	<?= $activeForm->field($formModel, 'price') ?>
	<?= $activeForm->field($formModel, 'oldPrice') ?>
</fieldset>
