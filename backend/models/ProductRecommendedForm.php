<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Product;

/**
 * Product recommended form
 */
class ProductRecommendedForm extends Model
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
	 * @var Product
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Product|null $object 
	 */
	public function __construct(Product $object = null, $config = [])
	{
		if ($object === null)
			$object = new Product;

		$this->_object = $object;

		//attributes
		parent::__construct(array_replace([
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
	 * @return Product
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
	//  * @param Product $product 
	//  * @param boolean $runValidation 
	//  * @return boolean
	//  */
	// public function save(Product $product, $runValidation = true)
	// {
	// 	if ($runValidation && !$this->validate())
	// 		return false;

	// 	$object = $this->_object;

	// 	$object->product_id = $product->id;
	// 	$object->barcode = $this->barcode;

	// 	if (!$object->save(false))
	// 		return false;

	// 	return true;
	// }

}
