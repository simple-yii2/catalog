<?php

use yii\helpers\Html;

use cms\catalog\common\models\Category;
use cms\catalog\backend\widgets\Property;
use cms\catalog\backend\widgets\assets\PropertyAsset;

PropertyAsset::register($this);

//categories
$categories = ['' => ''];
$query = Category::find()->orderBy(['lft' => SORT_ASC]);
foreach ($query->all() as $item) {
	if ($item->isLeaf() && $item->active)
		$categories[$item->id] = $item->path;
}

//properties
$propertiesName = Html::getInputName($model, 'properties');

?>
<fieldset>
	<?= $form->field($model, 'category_id')->dropDownList($categories) ?>
	<div class="properties">
		<?= Html::hiddenInput($propertiesName, '') ?>
		<?php foreach ($model->properties as $property) {
			echo $form->field($property, 'value')->label($property->name)->widget(Property::className(), [
				'name' => $propertiesName,
			]);
		} ?>
	</div>
	<?= $form->field($model, 'length') ?>
	<?= $form->field($model, 'width') ?>
	<?= $form->field($model, 'height') ?>
	<?= $form->field($model, 'weight') ?>
</fieldset>
