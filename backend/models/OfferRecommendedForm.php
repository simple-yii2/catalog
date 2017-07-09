<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Offer;

/**
 * Offer recommended form
 */
class OfferRecommendedForm extends Model
{

	/**
	 * @var integer 
	 */
	public $id;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $thumb;

	/**
	 * @var string
	 */
	private $_formName;

	/**
	 * @var Offer
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Offer|null $object 
	 */
	public function __construct(Offer $object = null, $config = [])
	{
		if ($object === null)
			$object = new Offer;

		$this->_object = $object;

		//attributes
		parent::__construct(array_merge([
			'id' => $object->id,
			'name' => $object->name,
			'thumb' => $object->thumb,
		], $config));
	}

	/**
	 * Form name setter
	 * @param string $value 
	 * @return void
	 */
	public function setFormName($value)
	{
		$this->_formName = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function formName()
	{
		if ($this->_formName === null)
			return parent::formName();

		return $this->_formName . '[' . $this->id . ']';
	}

	/**
	 * Object getter
	 * @return Offer
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['id', 'integer'],
			['id', 'required'],
		];
	}

	// /**
	//  * Save
	//  * @param Offer $offer 
	//  * @param boolean $runValidation 
	//  * @return boolean
	//  */
	// public function save(Offer $offer, $runValidation = true)
	// {
	// 	if ($runValidation && !$this->validate())
	// 		return false;

	// 	$object = $this->_object;

	// 	$object->offer_id = $offer->id;
	// 	$object->barcode = $this->barcode;

	// 	if (!$object->save(false))
	// 		return false;

	// 	return true;
	// }

}
