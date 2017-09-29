<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferBarcode;

/**
 * Offer barcode form
 */
class OfferBarcodeForm extends Model
{

	/**
	 * @var string Barcode
	 */
	public $barcode;

	/**
	 * @var OfferBarcode
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param OfferBarcode|null $object 
	 */
	public function __construct(OfferBarcode $object = null, $config = [])
	{
		if ($object === null)
			$object = new OfferBarcode;

		$this->_object = $object;

		//attributes
		parent::__construct(array_replace([
			'barcode' => $object->barcode,
		], $config));
	}

	/**
	 * Object getter
	 * @return OfferBarcode
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'barcode' => Yii::t('catalog', 'Value'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['barcode', 'string', 'max' => 50],
			['barcode', 'required'],
		];
	}

	/**
	 * Save
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
		$object->barcode = $this->barcode;

		if (!$object->save(false))
			return false;

		return true;
	}

}
