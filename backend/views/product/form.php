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
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => false,
    'options' => [
        'data-properties-url' => Url::toRoute(['properties', 'id' => $model->id]),
    ],
]); ?>

    <?= $this->render('form/tabs', ['form' => $form, 'model' => $model]) ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton(Yii::t('cms', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('cms', 'Cancel'), $cancelUrl, ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
