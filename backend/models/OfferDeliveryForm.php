<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferDelivery;
use cms\catalog\common\models\Delivery;

class OfferDeliveryForm extends Model
{

	/**
	 * @var integer
	 */
	public $delivery_id;

	/**
	 * @var string Name
	 */
	public $name;

	/**
	 * @var boolean Delivery is enabled for the offer
	 */
	public $active;

	/**
	 * @var boolean If false - use cost from this model
	 */
	public $defaultCost;

	/**
	 * @var float Cost
	 */
	public $cost;

	/**
	 * @var boolean If false - use days from this model
	 */
	public $defaultDays;

	/**
	 * @var integer Day count
	 */
	public $days;

	/**
	 * @var Delivery
	 */
	private $_template;

	/**
	 * @var OfferDelivery
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Delivery $template 
	 * @param OfferDelivery|null $object 
	 */
	public function __construct(Delivery $template, OfferDelivery $object = null, $config = [])
	{
		$this->_template = $template;

		if ($object === null)
			$object = new OfferDelivery;

		$this->_object = $object;

		//attributes
		parent::__construct(array_merge([
			'delivery_id' => $template->id,
			'name' => $template->name,
			'active' => $object->getIsNewRecord() ? '0' : '1',
			'defaultCost' => $object->cost === null ? '1' : '0',
			'cost' => $object->cost,
			'defaultDays' => $object->days === null ? '1' : '0',
			'days' => $object->days,
		], $config));
	}

	/**
	 * Template getter
	 * @return Delivery
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
	public function attributeLabels()
	{
		return [
			'name' => Yii::t('catalog', 'Name'),
			'defaultCost' => Yii::t('catalog', 'Use default'),
			'cost' => Yii::t('catalog', 'Cost'),
			'defaultDays' => Yii::t('catalog', 'Use default'),
			'days' => Yii::t('catalog', 'Days'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['active', 'defaultCost', 'defaultDays'], 'boolean'],
			['cost', 'double', 'min' => 0.01],
			['days', 'integer', 'min' => 0],
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
		$object->delivery_id = $this->_template->id;
		$object->cost = $this->defaultCost == 0 ? $this->cost : null;
		$object->days = $this->defaultDays == 0 ? $this->days : null;

		if (!$object->save(false))
			return false;

		return true;
	}

}
