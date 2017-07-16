<?php

namespace cms\catalog\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use cms\catalog\common\models\CategoryProperty;
use cms\catalog\frontend\helpers\FilterHelper;
use cms\catalog\frontend\widgets\assets\OfferFilterAsset;

class OfferFilter extends Widget
{

	/**
	 * @var cms\catalog\frontend\models\OfferFilter filter model
	 */
	public $model;

	/**
	 * @var array
	 */
	public $options = ['class' => 'offer-filter'];

	/**
	 * @var string
	 */
	public $formName = '';

	/**
	 * @var string
	 */
	public $buttonText = 'Apply';

	/**
	 * @var array
	 */
	public $buttonOptions = ['class' => 'btn btn-primary'];

	/**
	 * @var string
	 */
	public $trueText = 'Yes';

	/**
	 * @var string
	 */
	public $falseText = 'No';

	/**
	 * @var CategoryProperty[]
	 */
	private $_items;

	/**
	 * @var array
	 */
	private $_queryParams;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
		$this->prepareItems();
		$this->registerScripts();

		$this->_queryParams = Yii::$app->getRequest()->getQueryParams();
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		//price
		$items = $this->renderPrice();

		//vendor
		$items .= $this->renderVendor();

		//properties
		foreach ($this->_items as $item)
			$items .= $this->renderProperty($item);

		//do not render filter if there are no items
		if (empty($items))
			return '';

		//render
		// var_dump($this->_queryParams); die();
		ActiveForm::begin([
			'action' => array_replace([''], $this->_queryParams),
			'enableClientValidation' => false,
			'method' => 'get',
			'options' => $this->options,
		]);
		echo $items;
		echo $this->renderSubmitButton();
		ActiveForm::end();
	}

	/**
	 * Register client scripts
	 * @return void
	 */
	public function registerScripts()
	{
		OfferFilterAsset::register($this->getView());
	}

	/**
	 * Prepare category properties
	 * @return void
	 */
	private function prepareItems()
	{
		$this->_items = $this->model->getProperties();
	}

	/**
	 * Generate form name from property name
	 * @param string $name 
	 * @return string
	 */
	protected function generateFormName($name)
	{
		if (empty($this->formName))
			return $name;

		return $this->formName . "[$name]";
	}

	/**
	 * Render property title
	 * @param string $title 
	 * @param boolean $expanded 
	 * @return string
	 */
	protected function renderPropertyTitle($title)
	{
		return Html::a('<span class="glyphicon glyphicon-menu-down"></span> ' . Html::encode($title), '#', ['class' => 'filter-title']);
	}

	/**
	 * Render price range
	 * @return string
	 */
	protected function renderPrice()
	{
		$model = $this->model;
		list($min, $max) = $model->getPriceRange();
		return $this->renderRange($model->getAttributeLabel('price'), 'price', $model->price, $min, $max, true);
	}

	/**
	 * Render vendor select
	 * @return string
	 */
	protected function renderVendor()
	{
		$model = $this->model;

		return $this->renderSelect($model->getAttributeLabel('vendor'), 'vendor', $model->vendor, $model->getVendorItems(), true);
	}

	/**
	 * Render property field
	 * @param CategoryProperty $property 
	 * @return string
	 */
	protected function renderProperty($property)
	{
		$model = $this->model;

		switch ($property->type) {
			case CategoryProperty::TYPE_BOOLEAN:
				return $this->renderBoolean($property->name, $property->alias, $model->getPropertyValue($property), $model->getPropertyItems($property));

			case CategoryProperty::TYPE_INTEGER:
			case CategoryProperty::TYPE_FLOAT:
				list($min, $max) = $model->getPropertyRange($property);
				return $this->renderRange($property->name, $property->alias, $model->getPropertyValue($property), $min, $max);

			case CategoryProperty::TYPE_SELECT:
				return $this->renderSelect($property->name, $property->alias, $model->getPropertyValue($property), $model->getPropertyItems($property));
		}

		return '';
	}

	/**
	 * Render range filter field
	 * @param string $title 
	 * @param string $name 
	 * @param string $value 
	 * @param float $min 
	 * @param float $max 
	 * @param boolean|null $expanded 
	 * @return string
	 */
	protected function renderRange($title, $name, $value, $min, $max, $expanded = null)
	{
		$formName = $this->generateFormName($name);
		unset($this->_queryParams[$formName]);

		if (empty($value) && $min >= $max)
			return '';

		//title
		if ($expanded === null)
			$expanded = $value != '';
		$titleBlock = $this->renderPropertyTitle($title, $expanded);

		//input
		$options = [];
		if ($value == '')
			$options['disabled'] = true;
		$input = Html::hiddenInput($formName, $value, $options);

		//controls
		list($from, $to) = FilterHelper::rangeItems($value);
		$inputFrom = Html::textInput('', $from, ['class' => 'form-control', 'placeholder' => $min]);
		$inputTo = Html::textInput('', $to, ['class' => 'form-control', 'placeholder' => $max]);
		$controls = Html::tag('div', $inputFrom . $inputTo, ['class' => 'filter-controls']);

		$options = ['class' => 'filter-item filter-range'];
		if ($expanded)
			Html::addCssClass($options, 'expanded');

		return Html::tag('div', $titleBlock . $input . $controls, $options);
	}

	/**
	 * Render selection filter field
	 * @param string $title 
	 * @param string $name 
	 * @param string $value 
	 * @param array $items title, value, count keys. If value is not set, title uses as value.
	 * @param boolean|null $expanded 
	 * @return string
	 */
	protected function renderSelect($title, $name, $value, $items, $expanded = null)
	{
		$formName = $this->generateFormName($name);
		unset($this->_queryParams[$formName]);

		if (empty($items))
			return '';

		//title
		if ($expanded === null)
			$expanded = $value != '';
		$titleBlock = $this->renderPropertyTitle($title, $expanded);

		//input
		$options = [];
		if ($value == '')
			$options['disabled'] = true;
		$input = Html::hiddenInput($formName, $value, $options);

		//controls
		$selected = FilterHelper::selectItems($value);
		$listItems = [];
		$selectedItems = [];
		foreach ($items as $key => $item) {
			if (is_string($item)) {
				$listItems[$key] = $item;
				continue;
			}

			$t = ArrayHelper::getValue($item, 'title');
			if (empty($t))
				continue;

			$v = ArrayHelper::getValue($item, 'value', $t);

			$t = Html::encode($t);
			$c = ArrayHelper::getValue($item, 'count');
			if (!empty($c))
				$t .= ' ' . Html::tag('span', '(' . $c . ')', ['class' => 'text-muted']);

			if (in_array($v, $selected)) {
				$selectedItems[$v] = $t;
			} else {
				$listItems[$v] = $t;
			}
		}
		$inputList = Html::checkboxList('', $selected, array_replace($selectedItems, $listItems), [
			'class' => 'filter-select-items',
			'item' => function($index, $label, $name, $checked, $value) {
				$s = Html::checkbox('', $checked, ['value' => $value]);
				$s = Html::tag('label', $s . ' ' . $label);
				return Html::tag('div', $s);
			},
		]);
		$controls = Html::tag('div', $inputList, ['class' => 'filter-controls']);

		$options = ['class' => 'filter-item filter-select'];
		if ($expanded)
			Html::addCssClass($options, 'expanded');

		return Html::tag('div', $titleBlock . $input . $controls, $options);
	}

	/**
	 * Render boolean filter field
	 * @param string $title 
	 * @param string $name 
	 * @param string $value 
	 * @param array $items title, value, count keys. If value is not set, title uses as value.
	 * @param boolean|null $expanded 
	 * @return string
	 */
	protected function renderBoolean($title, $name, $value, $items, $expanded = null)
	{
		$formName = $this->generateFormName($name);
		unset($this->_queryParams[$formName]);

		//title
		if ($expanded === null)
			$expanded = $value != '';
		$titleBlock = $this->renderPropertyTitle($title, $expanded);

		//input
		$options = [];
		if ($value == '')
			$options['disabled'] = true;
		$input = Html::hiddenInput($formName, $value, $options);

		//controls
		$selected = FilterHelper::selectItems($value);
		$listItems = [];
		foreach ($items as $key => $item) {
			if (is_string($item)) {
				$listItems[$key] = $item;
				continue;
			}

			$v = ArrayHelper::getValue($item, 'title');
			if ($v === null)
				continue;

			$t = $v == 1 ? $this->trueText : $this->falseText;

			$t = Html::encode($t);
			$c = ArrayHelper::getValue($item, 'count');
			if (!empty($c))
				$t .= ' ' . Html::tag('span', '(' . $c . ')', ['class' => 'text-muted']);

			$listItems[$v] = $t;
		}
		if (sizeof($listItems) != 2)
			return '';
		krsort($listItems);
		$inputList = Html::checkboxList('', $selected, $listItems, [
			'class' => 'filter-select-items',
			'item' => function($index, $label, $name, $checked, $value) {
				$s = Html::checkbox('', $checked, ['value' => $value]);
				$s = Html::tag('label', $s . ' ' . $label);
				return Html::tag('div', $s);
			},
		]);
		$controls = Html::tag('div', $inputList, ['class' => 'filter-controls']);

		$options = ['class' => 'filter-item filter-select'];
		if ($expanded)
			Html::addCssClass($options, 'expanded');

		return Html::tag('div', $titleBlock . $input . $controls, $options);
	}

	/**
	 * Render submit button
	 * @return string
	 */
	protected function renderSubmitButton()
	{
		$button = Html::submitButton($this->buttonText, $this->buttonOptions);

		return Html::tag('div', $button, ['class' => 'filter-submit']);
	}

}
