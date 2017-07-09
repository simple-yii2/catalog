<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;

use cms\catalog\backend\assets\OfferFormAsset;

OfferFormAsset::register($this);

?>
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
	'options' => [
		'data-properties-url' => Url::toRoute(['properties', 'id' => $model->getObject()->id]),
	],
]); ?>

	<?= Tabs::widget(['items' => [
		[
			'label' => Yii::t('catalog', 'General'),
			'content' => $this->render('form/general', ['form' => $form, 'model' => $model]),
			'active' => true,
		],
		[
			'label' => Yii::t('catalog', 'Properties'),
			'content' => $this->render('form/properties', ['form' => $form, 'model' => $model]),
			'active' => false,
		],
		[
			'label' => Yii::t('catalog', 'Purchase'),
			'content' => $this->render('form/purchase', ['form' => $form, 'model' => $model]),
			'active' => false,
		],
		[
			'label' => Yii::t('catalog', 'Delivery'),
			'content' => $this->render('form/delivery', ['form' => $form, 'model' => $model]),
			'active' => false,
		],
		[
			'label' => Yii::t('catalog', 'Recommended'),
			'content' => $this->render('form/recommended', ['form' => $form, 'model' => $model]),
			'active' => false,
		],
		[
			'label' => Yii::t('catalog', 'Quantity'),
			'content' => $this->render('form/quantity', ['form' => $form, 'model' => $model]),
			'active' => false,
		],
	]]) ?>

	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-6">
			<?= Html::submitButton(Yii::t('news', 'Save'), ['class' => 'btn btn-primary']) ?>
			<?= Html::a(Yii::t('news', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
		</div>
	</div>

<?php ActiveForm::end(); ?>
