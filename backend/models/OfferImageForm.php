<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferImage;

class OfferImageForm extends Model
{

	/**
	 * @var integer|null
	 */
	public $id;

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var string
	 */
	public $thumb;

	/**
	 * @var OfferImage
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param OfferImage|null $object 
	 */
	public function __construct(OfferImage $object = null, $config = [])
	{
		if ($object === null)
			$object = new OfferImage;

		$this->_object = $object;

		//file caching
		Yii::$app->storage->cacheObject($object);

		//attributes
		parent::__construct(array_merge([
			'id' => $object->id,
			'file' => $object->file,
			'thumb' => $object->thumb,
		], $config));
	}

	/**
	 * Object getter
	 * @return OfferImage
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
			[['file', 'thumb'], 'string', 'max' => 200],
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
		$object->file = $this->file;
		$object->thumb = $this->thumb;

		Yii::$app->storage->storeObject($object);

		if (!$object->save(false))
			return false;

		return true;
	}

}
