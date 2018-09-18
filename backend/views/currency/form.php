<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => false,
]); ?>

    <fieldset>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'code') ?>
        <?= $form->field($model, 'rate') ?>
        <?= $form->field($model, 'precision')->dropDownList([0 => '1', 1 => '0.1', 2 => '0.01']) ?>
        <?= $form->field($model, 'prefix') ?>
        <?= $form->field($model, 'suffix') ?>
        <?= $form->field($model, 'default')->checkbox() ?>
    </fieldset>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton(Yii::t('cms', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('cms', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
