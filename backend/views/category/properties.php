<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use cms\catalog\backend\assets\CategoryFormAsset;
use dkhlystov\widgets\ArrayInput;

CategoryFormAsset::register($this);

$title = Yii::t('catalog', 'Common properties');

$this->title = $title . ' | ' . Yii::$app->name;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('catalog', 'Categories'), 'url' => ['index']],
    $title,
];

?>
<h1><?= Html::encode($title) ?></h1>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableClientValidation' => false,
    'fieldConfig' => ['template' => "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}"],
]); ?>

    <fieldset>
        <div class="form-group">
            <div class="col-sm-9">
                <?= ArrayInput::widget(array_merge(['model' => $model, 'attribute' => 'properties'], require(__DIR__ . '/_propertiesConfig.php'))) ?>
            </div>
        </div>
    </fieldset>

    <div class="form-group">
        <div class="col-sm-6">
            <?= Html::submitButton(Yii::t('cms', 'Save'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('cms', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
