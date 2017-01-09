<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\GoodsImage;

class GoodsImageForm extends Model
{

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var string
	 */
	public $thumb;

	/**
	 * @var GoodsImage
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param GoodsImage|null $object 
	 */
	public function __construct(GoodsImage $object = null, $config = [])
	{
		if ($object === null)
			$object = new GoodsImage;

		$this->_object = $object;

		//attributes
		$this->file = $object->file;
		$this->thumb = $object->thumb;

		Yii::$app->storage->cacheObject($object);

		parent::__construct($config);
	}

	/**
	 * Id getter
	 * @return integer
	 */
	public function getId()
	{
		return $this->_object->id;
	}

	/**
	 * Object getter
	 * @return GoodsImage
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
	 * Save object using model attributes
	 * @param cms\catalog\common\models\Goods $goods 
	 * @param boolean $runValidation 
	 * @return boolean
	 */
	public function save(\cms\catalog\common\models\Goods $goods, $runValidation = true)
	{
		if ($runValidation && !$this->validate())
			return false;

		$object = $this->_object;

		$object->goods_id = $goods->id;
		$object->file = $this->file;
		$object->thumb = $this->thumb;

		Yii::$app->storage->storeObject($object);

		if (!$object->save(false))
			return false;

		return true;
	}

}
