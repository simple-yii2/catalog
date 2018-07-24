<?php

use yii\helpers\Html;
use cms\catalog\backend\widgets\Property;
use cms\catalog\backend\widgets\assets\PropertyAsset;

PropertyAsset::register($this);

//properties
$propertiesName = Html::getInputName($form, 'properties');

//input templates
$templateLength = '<div class="input-group">{input}<span class="input-group-addon">' . Html::encode(Yii::t('catalog', 'mm')) . '</span></div>';
$templateWeight = '<div class="input-group">{input}<span class="input-group-addon">' . Html::encode(Yii::t('catalog', 'kg')) . '</span></div>';

?>
<fieldset>
    <div class="properties">
        <?= Html::hiddenInput($propertiesName, '') ?>
        <?php foreach ($form->properties as $property) {
            echo $activeForm->field($property, 'value')->label($property->name)->widget(Property::className(), [
                'name' => $propertiesName,
            ]);
        } ?>
    </div>
    <?= $activeForm->field($form, 'length', ['inputTemplate' => $templateLength]) ?>
    <?= $activeForm->field($form, 'width', ['inputTemplate' => $templateLength]) ?>
    <?= $activeForm->field($form, 'height', ['inputTemplate' => $templateLength]) ?>
    <?= $activeForm->field($form, 'weight', ['inputTemplate' => $templateWeight]) ?>
</fieldset>
