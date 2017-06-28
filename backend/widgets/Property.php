<?php

namespace cms\catalog\backend\widgets;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;

use cms\catalog\common\models;
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
			case models\Property::TYPE_BOOLEAN:
				echo $this->renderBoolean();
				break;

			case models\Property::TYPE_INTEGER:
				echo $this->renderInteger();
				break;

			case models\Property::TYPE_FLOAT:
				echo $this->renderFloat();
				break;

			case models\Property::TYPE_SELECT:
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
		if ($value === true)
			Html::addCssClass($options, 'active');
		$checkboxTrue = Html::tag('label', $checkboxTrue . $formatter->booleanFormat[1], $options);

		$checkboxFalse = Html::checkbox($name, $value === false, ['value' => '0']);
		$options = $this->booleanButtonOptions;
		if ($value === false)
			Html::addCssClass($options, 'active');
		$checkboxFalse = Html::tag('label', $checkboxFalse . $formatter->booleanFormat[0], $options);

		return $input . Html::tag('div', $checkboxTrue . $checkboxFalse, ['class' => 'btn-group property-boolean', 'data-toggle' => 'buttons']);
	}

	/**
	 * Render integer property
	 * @return string
	 */
	private function renderInteger()
	{
		return Html::textInput($this->getInputName(), $this->model->value, $this->options);
	}

	/**
	 * Render float property
	 * @return string
	 */
	private function renderFloat()
	{
		return Html::textInput($this->getInputName(), $this->model->value, $this->options);
	}

	/**
	 * Render select property
	 * @return string
	 */
	private function renderSelect()
	{
		$model = $this->model;

		$items = ['' => ''];
		foreach ($model->values as $value) {
			$items[$value] = $value;
		}

		return Html::dropDownList($this->getInputName(), $model->value, $items, $this->options);
	}

	/**
	 * Generate name for property input
	 * @return string
	 */
	private function getInputName()
	{
		return $this->name . '[' . $this->model->property_id . ']';
	}

}
