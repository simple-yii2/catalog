<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use yii\helpers\Url;

use cms\catalog\backend\assets\OfferFormAsset;

OfferFormAsset::register($this);

?>
<?php $activeForm = ActiveForm::begin([
	'layout' => 'horizontal',
	'enableClientValidation' => false,
	'options' => [
		'data-properties-url' => Url::toRoute(['properties', 'id' => $formModel->getObject()->id]),
	],
]); ?>

	<?= Tabs::widget(['items' => [
		[
			'label' => Yii::t('catalog', 'General'),
			'content' => $this->render('form/general', ['activeForm' => $activeForm, 'formModel' => $formModel]),
			'active' => true,
		],
		[
			'label' => Yii::t('catalog', 'Properties'),
			'content' => $this->render('form/properties', ['activeForm' => $activeForm, 'formModel' => $formModel]),
			'active' => false,
		],
		[
			'label' => Yii::t('catalog', 'Purchase'),
			'content' => $this->render('form/purchase', ['activeForm' => $activeForm, 'formModel' => $formModel]),
			'active' => false,
		],
		[
			'label' => Yii::t('catalog', 'Dimensions'),
			'content' => $this->render('form/dimensions', ['activeForm' => $activeForm, 'formModel' => $formModel]),
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
