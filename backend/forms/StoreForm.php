<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Store;

/**
 * Editing form
 */
class StoreForm extends Model
{

	/**
	 * @var integer Type
	 */
	public $type;

	/**
	 * @var float Name
	 */
	public $name;

	/**
	 * @var Store
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Store|null $object 
	 */
	public function __construct(Store $object = null, $config = [])
	{
		if ($object === null)
			$object = new Store;

		$this->_object = $object;

		//attributes
		parent::__construct(array_merge([
			'type' => $object->type,
			'name' => $object->name,
		], $config));
	}

	/**
	 * Object getter
	 * @return Store
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
			'type' => Yii::t('catalog', 'Type'),
			'name' => Yii::t('catalog', 'Name'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['type', 'in', 'range' => Store::getTypes()],
			['name', 'string', 'max' => 100],
			[['type', 'name'], 'required'],
		];
	}

	/**
	 * Saving object using object attributes
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$object = $this->_object;

		$object->type = (integer) $this->type;
		$object->name = $this->name;

		if (!$object->save(false))
			return false;

		return true;
	}

}
