<?php

namespace cms\catalog\backend\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;

use cms\catalog\common\models\CategoryProperty;
use cms\catalog\backend\widgets\assets\PropertyAsset;

class Property extends InputWidget
{

    /**
     * @inheritdoc
     */
    public $options = ['class' => 'form-control'];

    /**
     * @var array The HTML attributes for the boolean button tag.
     */
    public $booleanButtonOptions = ['class' => 'btn btn-default'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        PropertyAsset::register($this->view);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        switch ($this->model->type) {
            case CategoryProperty::TYPE_BOOLEAN:
                echo $this->renderBoolean();
                break;

            case CategoryProperty::TYPE_INTEGER:
                echo $this->renderInteger();
                break;

            case CategoryProperty::TYPE_FLOAT:
                echo $this->renderFloat();
                break;

            case CategoryProperty::TYPE_SELECT:
                echo $this->renderSelect();
                break;
        }
    }

    /**
     * Render boolean property
     * @return string
     */
    private function renderBoolean()
    {
        $value = $this->model->value;
        if ($value === null || $value === '') {
            $value = null;
        } else {
            $value = $value == 0 ? false : true;
        }

        $name = $this->getInputName();
        $formatter = Yii::$app->formatter;

        $input = Html::hiddenInput($name, '');

        $checkboxTrue = Html::checkbox($name, $value === true, ['value' => '1']);
        $options = $this->booleanButtonOptions;
        if ($value === true) {
            Html::addCssClass($options, 'active');
        }
        $checkboxTrue = Html::tag('label', $checkboxTrue . $formatter->booleanFormat[1], $options);

        $checkboxFalse = Html::checkbox($name, $value === false, ['value' => '0']);
        $options = $this->booleanButtonOptions;
        if ($value === false) {
            Html::addCssClass($options, 'active');
        }
        $checkboxFalse = Html::tag('label', $checkboxFalse . $formatter->booleanFormat[0], $options);

        return $input . Html::tag('div', $checkboxTrue . $checkboxFalse, ['class' => 'btn-group property-boolean', 'data-toggle' => 'buttons']);
    }

    /**
     * Render integer property
     * @return string
     */
    private function renderInteger()
    {
        $model = $this->model;
        $unit = $model->getTemplate()->unit;

        $control = Html::textInput($this->getInputName(), $model->value, $this->options);

        if (!empty($unit)) {
            $control = $this->renderUnit($control, $unit);
        }

        return $control;
    }

    /**
     * Render float property
     * @return string
     */
    private function renderFloat()
    {
        $model = $this->model;
        $unit = $model->getTemplate()->unit;

        $control = Html::textInput($this->getInputName(), $model->value, $this->options);

        if (!empty($unit)) {
            $control = $this->renderUnit($control, $unit);
        }

        return $control;
    }

    /**
     * Render select property
     * @return string
     */
    private function renderSelect()
    {
        $model = $this->model;
        $unit = $model->getTemplate()->unit;

        $items = ['' => ''];
        foreach ($model->values as $value) {
            $items[$value] = $value;
        }

        $control = Html::dropDownList($this->getInputName(), $model->value, $items, $this->options);

        if (!empty($unit)) {
            $control = $this->renderUnit($control, $unit);
        }

        return $control;
    }

    /**
     * Generate name for property input
     * @return string
     */
    private function getInputName()
    {
        return $this->name . '[' . $this->model->property_id . '][value]';
    }

    /**
     * Render measure unit
     * @param string $control 
     * @param string $unit 
     * @return string
     */
    private function renderUnit($control, $unit)
    {
        $unit = Html::tag('span', Html::encode($unit), ['class' => 'input-group-addon product-property-unit']);
        return Html::tag('div', $control . $unit, ['class' => 'input-group']);
    }

}
