<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Delivery;

/**
 * Editing form
 */
class DeliveryForm extends Model
{

	/**
	 * @var string Name
	 */
	public $name;

	/**
	 * @var float Cost
	 */
	public $cost;

	/**
	 * @var integer Day count
	 */
	public $days;

	/**
	 * @var Delivery
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Delivery|null $object 
	 */
	public function __construct(Delivery $object = null, $config = [])
	{
		if ($object === null)
			$object = new Delivery;

		$this->_object = $object;

		//attributes
		parent::__construct(array_merge([
			'name' => $object->name,
			'cost' => $object->cost,
			'days' => $object->days,
		], $config));
	}

	/**
	 * Object getter
	 * @return Delivery
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
			'name' => Yii::t('catalog', 'Name'),
			'cost' => Yii::t('catalog', 'Cost'),
			'days' => Yii::t('catalog', 'Days'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['name', 'string', 'max' => 100],
			['cost', 'double', 'min' => 0],
			['days', 'integer', 'min' => 0],
			[['name', 'cost', 'days'], 'required'],
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

		$object->name = $this->name;
		$object->cost = (float) $this->cost;
		$object->days = (integer) $this->days;

		if (!$object->save(false))
			return false;

		return true;
	}

}
