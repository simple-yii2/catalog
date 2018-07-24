<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use cms\catalog\backend\assets\ProductFormAsset;

ProductFormAsset::register($this);

$cancelUrl = ['index'];
if ($category_id = Yii::$app->getRequest()->get('category_id')) {
    $cancelUrl['category_id'] = $category_id;
}

?>
<?php $activeForm = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => false,
    'options' => [
        'data-properties-url' => Url::toRoute(['properties', 'id' => $form->getObject()->id]),
    ],
]); ?>

    <?= $this->render('form/tabs', ['activeForm' => $activeForm, 'form' => $form]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('catalog', 'Cancel'), $cancelUrl, ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
