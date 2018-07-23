<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use dkhlystov\uploadimage\widgets\UploadImage;

//thumb size
$module = Yii::$app->controller->module;
$thumbWidth = ArrayHelper::getValue($module, 'vendorThumbWidth', 100);
$thumbHeight = ArrayHelper::getValue($module, 'vendorThumbHeight', 100);

//label suffix
$imageSize = '<br><span class="label label-default">' . $thumbWidth . '&times' . $thumbHeight . '</span>';

//widget size
$width = $thumbWidth;
if ($width < 20) $width = 20;
if ($width > 282) $width = 282;
$height = $thumbHeight / $thumbWidth * $width;
if ($height < 20) $height = 20;

?>
<?php $activeForm = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => false,
]); ?>

    <fieldset>
        <?= $activeForm->field($form, 'name') ?>
        <?= $activeForm->field($form, 'description')->textarea(['rows' => 5]) ?>
        <?= $activeForm->field($form, 'url') ?>
        <?= $activeForm->field($form, 'file')->label($form->getAttributeLabel('file') . $imageSize)->widget(UploadImage::className(), [
            'thumbAttribute' => 'thumb',
            'thumbWidth' => $thumbWidth,
            'thumbHeight' => $thumbHeight,
            'width' => $width,
            'height' => $height,
        ]) ?>
    </fieldset>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton(Yii::t('catalog', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('catalog', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
