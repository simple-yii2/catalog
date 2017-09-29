<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferProperty;
use cms\catalog\common\models\CategoryProperty;

class OfferPropertyForm extends Model
{

	/**
	 * @var integer;
	 */
	public $property_id;

	/**
	 * @var string Name
	 */
	public $name;

	/**
	 * @var string[] Enum values
	 */
	public $values;

	/**
	 * @var string Value
	 */
	public $value;

	/**
	 * @var CategoryProperty
	 */
	private $_template;

	/**
	 * @var OfferProperty
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param CategoryProperty $template 
	 * @param OfferProperty|null $object 
	 */
	public function __construct(CategoryProperty $template, OfferProperty $object = null, $config = [])
	{
		$this->_template = $template;

		if ($object === null)
			$object = new OfferProperty;

		$this->_object = $object;

		//attributes
		parent::__construct(array_replace([
			'property_id' => $template->id,
			'name' => $template->name,
			'values' => $template->getValues(),
			'value' => $object->value,
		], $config));
	}

	/**
	 * Property type getter
	 * @return integer
	 */
	public function getType()
	{
		return $this->_template->type;
	}

	/**
	 * Template getter
	 * @return CategoryProperty
	 */
	public function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * @inheritdoc
	 */
	public function formName()
	{
		return parent::formName() . '[' . $this->_template->id . ']';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['value', function() {
				if (!$this->_template->validateValue($this->value)) {
					$this->addError('value', Yii::t('catalog', 'The value is incorrect.'));
				}
			}],
		];
	}

	/**
	 * Save object using model attributes
	 * @param Offer $offer 
	 * @param boolean $runValidation 
	 * @return boolean
	 */
	public function save(Offer $offer, $runValidation = true)
	{
		if ($runValidation && !$this->validate())
			return false;

		$object = $this->_object;

		$object->offer_id = $offer->id;
		$object->property_id = $this->_template->id;
		$object->value = $this->_template->formatValue($this->value);

		if (!$object->save(false))
			return false;

		return true;
	}

}
