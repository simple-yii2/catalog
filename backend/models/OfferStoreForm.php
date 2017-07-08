<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Offer;
use cms\catalog\common\models\StoreOffer;
use cms\catalog\common\models\Store;

class OfferStoreForm extends Model
{

	/**
	 * @var integer
	 */
	public $store_id;

	/**
	 * @var string Name
	 */
	public $name;

	/**
	 * @var integer Quantity
	 */
	public $quantity;

	/**
	 * @var Store
	 */
	private $_template;

	/**
	 * @var StoreOffer
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Store $template 
	 * @param StoreOffer|null $object 
	 */
	public function __construct(Store $template, StoreOffer $object = null, $config = [])
	{
		$this->_template = $template;

		if ($object === null)
			$object = new StoreOffer;

		$this->_object = $object;

		//attributes
		parent::__construct(array_merge([
			'store_id' => $template->id,
			'name' => $template->name,
			'quantity' => $object->quantity,
		], $config));
	}

	/**
	 * Template getter
	 * @return Store
	 */
	public function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['quantity', 'integer', 'min' => 0],
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
		$object->store_id = $this->_template->id;
		$object->quantity = (integer) $this->quantity;

		if (!$object->save(false))
			return false;

		return true;
	}

}
