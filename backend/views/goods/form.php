<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

use cms\catalog\common\models\Category;

$categories = ['' => ''];
$query = Category::find()->orderBy(['lft' => SORT_ASC]);
foreach ($query->all() as $object) {
	if ($object->isLeaf() && $object->active)
		$categories[$object->id] = $object->path;
}

$propertiesName = Html::getInputName($model, 'properties');

?>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
]); ?>

	<?= $form->field($model, 'active')->checkbox() ?>

	<?= $form->field($model, 'category_id')->dropDownList($categories) ?>

	<?= $form->field($model, 'title') ?>

	<?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

	<?php foreach ($model->properties as $property) {
		echo $form->field($property, 'value')->label($property->title)->widget('cms\catalog\backend\widgets\Property', [
			'name' => $propertiesName,
		]);
	} ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('news', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('news', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
