<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferProperty;
use cms\catalog\common\models\Property;

class OfferPropertyForm extends Model
{

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
	 * @var Property
	 */
	private $_template;

	/**
	 * @var OfferProperty
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Property $template 
	 * @param OfferProperty|null $object 
	 */
	public function __construct(Property $template, OfferProperty $object = null, $config = [])
	{
		$this->_template = $template;

		if ($object === null)
			$object = new OfferProperty;

		$this->_object = $object;

		//attributes
		$this->name = $template->name;
		$this->values = $template->getValues();
		$this->value = $object->value;

		parent::__construct($config);
	}

	/**
	 * Property id getter
	 * @return integer
	 */
	public function getProperty_id()
	{
		return $this->_template->id;
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
	 * @return Property
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
