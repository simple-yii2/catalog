<?php

use yii\helpers\Html;

use cms\catalog\common\models\Category;
use cms\catalog\backend\widgets\Property;
use cms\catalog\backend\widgets\assets\PropertyAsset;

PropertyAsset::register($this);

//categories
$categories = ['' => ''];
$query = Category::find()->orderBy(['lft' => SORT_ASC]);
foreach ($query->all() as $model) {
	if ($model->isLeaf() && $model->active)
		$categories[$model->id] = $model->path;
}

//properties
$propertiesName = Html::getInputName($formModel, 'properties');

?>
<fieldset>
	<?= $activeForm->field($formModel, 'category_id')->dropDownList($categories) ?>
	<div class="properties">
		<?= Html::hiddenInput($propertiesName, '') ?>
		<?php foreach ($formModel->properties as $property) {
			echo $activeForm->field($property, 'value')->label($property->name)->widget(Property::className(), [
				'name' => $propertiesName,
			]);
		} ?>
	</div>
	<?= $activeForm->field($formModel, 'length') ?>
	<?= $activeForm->field($formModel, 'width') ?>
	<?= $activeForm->field($formModel, 'height') ?>
	<?= $activeForm->field($formModel, 'weight') ?>
</fieldset>
