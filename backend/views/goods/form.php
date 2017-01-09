<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

use cms\catalog\backend\assets\GoodsFormAsset;
use cms\catalog\common\models\Category;
use dkhlystov\uploadimage\widgets\UploadImages;

GoodsFormAsset::register($this);

$width = 360;
$height = 270;

$imageSize = '<br><span class="label label-default">' . $width . '&times' . $height . '</span>';

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
	'options' => [
		'data-properties-url' => Url::toRoute(['properties', 'id' => $model->getId()]),
	],
]); ?>

	<?= $form->field($model, 'active')->checkbox() ?>

	<?= $form->field($model, 'images')->label($model->getAttributeLabel('images') . $imageSize)->widget(UploadImages::className(), [
		'id' => 'goods-images',
		'fileKey' => 'file',
		'thumbKey' => 'thumb',
		'thumbWidth' => $width,
		'thumbHeight' => $height,
		'data' => function($item) {
			return [
				'id' => $item['id'],
			];
		},
	]) ?>

	<?= $form->field($model, 'category_id')->dropDownList($categories) ?>

	<?= $form->field($model, 'title') ?>

	<?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

	<?= $form->field($model, 'price') ?>

	<div class="properties">
		<?= Html::hiddenInput($propertiesName, '') ?>
		<?php foreach ($model->properties as $property) {
			echo $form->field($property, 'value')->label($property->title)->widget('cms\catalog\backend\widgets\Property', [
				'name' => $propertiesName,
			]);
		} ?>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('news', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('news', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
