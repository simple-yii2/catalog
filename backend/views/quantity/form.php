<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use cms\catalog\common\models\Store;

//store names
$storeNames = [];
foreach (Store::find()->all() as $item) {
    $storeNames[$item->id] = $item->name;
}

//input name
$name = Html::getInputName($model, 'stores');

?>
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => false,
]); ?>

    <fieldset>
        <?php foreach ($model->stores as $key => $value) echo $form->field($model, 'stories')->label($storeNames[$key])->textInput([
            'name' => $name . '[' . $key . ']',
            'value' => $value,
        ]) ?>
    </fieldset>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
            <?= Html::submitButton(Yii::t('cms', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('cms', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
