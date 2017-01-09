<?php

namespace cms\catalog\backend\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;

use cms\catalog\common\models;
use cms\catalog\backend\widgets\assets\PropertyAsset;

class Property extends InputWidget
{

	public $options = ['class' => 'form-control'];

	public $booleanButtonOptions = ['class' => 'btn btn-default'];

	public function init()
	{
		parent::init();

		PropertyAsset::register($this->view);
	}

	public function run()
	{
		switch ($this->model->getType()) {
			case models\Property::BOOLEAN:
				$this->renderBoolean();
				break;

			case models\Property::INTEGER:
				$this->renderInteger();
				break;

			case models\Property::FLOAT:
				$this->renderFloat();
				break;

			case models\Property::SELECT:
				$this->renderSelect();
				break;
		}
	}

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
		if ($value === true)
			Html::addCssClass($options, 'active');
		$checkboxTrue = Html::tag('label', $checkboxTrue . $formatter->booleanFormat[1], $options);

		$checkboxFalse = Html::checkbox($name, $value === false, ['value' => '0']);
		$options = $this->booleanButtonOptions;
		if ($value === false)
			Html::addCssClass($options, 'active');
		$checkboxFalse = Html::tag('label', $checkboxFalse . $formatter->booleanFormat[0], $options);

		echo $input . Html::tag('div', $checkboxTrue . $checkboxFalse, ['class' => 'btn-group property-boolean', 'data-toggle' => 'buttons']);
	}

	private function renderInteger()
	{
		echo Html::textInput($this->getInputName(), $this->model->value, $this->options);
	}

	private function renderFloat()
	{
		echo Html::textInput($this->getInputName(), $this->model->value, $this->options);
	}

	private function renderSelect()
	{
		$model = $this->model;

		$items = ['' => ''];
		foreach ($model->values as $value) {
			$items[$value] = $value;
		}

		echo Html::dropDownList($this->getInputName(), $model->value, $items, $this->options);
	}

	private function getInputName()
	{
		return $this->name . '[' . $this->model->getPropertyId() . ']';
	}

}