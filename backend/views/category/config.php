<?php

//columns for properties widget

use yii\helpers\Html;
use cms\catalog\backend\forms\CategoryPropertyForm;
use cms\catalog\models\CategoryProperty;

$typeNames = CategoryProperty::getTypeNames();
$typesWithValues = CategoryProperty::getTypesWithValues();

return [
    'itemClass' => CategoryPropertyForm::className(),
    'columns' => [
        'name',
        ['attribute' => 'type', 'items' => $typeNames, 'inputOptions' => ['class' => 'form-control property-type']],
        ['attribute' => 'values', 'content' => function($model, $key, $index, $column) use ($typesWithValues) {
            if ($model->getReadOnly()) {
                return '';
            }

            $id = Html::hiddenInput($column->getInputName($model, $index, 'id'), $model->id);

            $name = $column->getInputName($model, $index, 'values');
            $values = Html::hiddenInput($name, '', ['class' => 'property-values']);
            foreach ($model->values as $value) {
                $values .= Html::hiddenInput($name . '[]', $value);
            }

            $options = ['class' => 'btn btn-default property-values'];
            if (!in_array($model->type, $typesWithValues)) {
                $options['disabled'] = true;
            }
            $button = Html::button($model->getAttributeLabel('values'), $options);

            return $id . $values . $button;
        }],
        ['attribute' => 'unit', 'inputOptions' => function($model, $key, $index, $column) {
            $options = ['class' => 'form-control'];
            if ($model->type == CategoryProperty::TYPE_BOOLEAN) {
                Html::addCssClass($options, 'hidden');
            }

            return $options;
        }],
        ['attribute' => 'search', 'class' => 'dkhlystov\grid\BooleanInputColumn'],
    ],
    'addLabel' => Yii::t('cms', 'Add'),
    'removeLabel' => Yii::t('cms', 'Remove'),
    'readOnlyAttribute' => 'readOnly',
    'canMove' => true,
    'options' => [
        'class' => 'category-properties',
        'data-types-with-values' => $typesWithValues,
        'data-modal-title' => Yii::t('catalog', 'Values'),
        'data-modal-add' => Yii::t('cms', 'Add'),
        'data-modal-ok' => Yii::t('cms', 'OK'),
        'data-modal-cancel' => Yii::t('cms', 'Cancel'),
    ],
];
