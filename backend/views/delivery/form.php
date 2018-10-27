<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use cms\catalog\common\models\Currency;

//currencies
$currencies = ['' => ''];
foreach (Currency::find()->all() as $item) {
    $currencies[$item->id] = $item->name;
}

?>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => false,
]); ?>

    <fieldset>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'currency_id')->dropDownList($currencies) ?>
        <?= $form->field($model, 'price') ?>
        <?= $form->field($model, 'days') ?>
    </fieldset>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton(Yii::t('cms', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('cms', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
