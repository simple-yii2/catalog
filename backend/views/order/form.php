<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use cms\catalog\backend\assets\OrderFormAsset;

OrderFormAsset::register($this);

?>
<?php $form = ActiveForm::begin([
    'id' => 'order-form',
    'layout' => 'horizontal',
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'label' => 'col-sm-4',
            'offset' => 'offset-sm-4',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
    'options' => ['data-url-calc' => Url::toRoute('calc')],
]); ?>

    <?= $this->render('form/general', ['form' => $form, 'model' => $model]) ?>
    <hr>
    <?= $this->render('form/customer', ['form' => $form, 'model' => $model]) ?>
    <hr>
    <?= $this->render('form/delivery', ['form' => $form, 'model' => $model]) ?>
    <?= $this->render('form/products', ['form' => $form, 'model' => $model]) ?>

    <div class="form-group">
        <div class="col-sm-12">
            <?= Html::submitButton(Yii::t('cms', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('cms', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
