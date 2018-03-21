<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Product;
use cms\catalog\common\models\ProductImage;

class ProductImageForm extends Model
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
	 * @var ProductImage
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param ProductImage|null $object 
	 */
	public function __construct(ProductImage $object = null, $config = [])
	{
		if ($object === null)
			$object = new ProductImage;

		$this->_object = $object;

		//file caching
		Yii::$app->storage->cacheObject($object);

		//attributes
		parent::__construct(array_replace([
			'id' => $object->id,
			'file' => $object->file,
			'thumb' => $object->thumb,
		], $config));
	}

	/**
	 * Object getter
	 * @return ProductImage
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
	 * @param Product $product 
	 * @param boolean $runValidation 
	 * @return boolean
	 */
	public function save(Product $product, $runValidation = true)
	{
		if ($runValidation && !$this->validate())
			return false;

		$object = $this->_object;

		$object->product_id = $product->id;
		$object->file = $this->file;
		$object->thumb = $this->thumb;

		Yii::$app->storage->storeObject($object);

		if (!$object->save(false))
			return false;

		return true;
	}

}
