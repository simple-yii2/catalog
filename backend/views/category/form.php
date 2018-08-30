<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use cms\catalog\backend\assets\CategoryFormAsset;

CategoryFormAsset::register($this);

?>
<?php $activeForm = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => false,
]); ?>

    <fieldset>
        <?= $activeForm->field($form, 'active')->checkbox() ?>
        <?= $activeForm->field($form, 'title') ?>
        <?php if (Yii::$app->controller->module->propertiesEnabled) echo $activeForm->field($form, 'properties')->widget('dkhlystov\widgets\ArrayInput', require(__DIR__ . '/_propertiesConfig.php')) ?>
    </fieldset>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('catalog', 'Cancel'), ['index', 'id' => $id], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
