<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Property;
use cms\catalog\common\models\GoodsProperty;

class GoodsPropertyForm extends Model
{

	public $title;

	public $values;

	public $value;

	private $_template;

	private $_object;

	/**
	 * @inheritdoc
	 * @param Property $template 
	 * @param GoodsProperty|null $object 
	 */
	public function __construct(Property $template, GoodsProperty $object = null, $config = [])
	{
		$this->_template = $template;

		if ($object === null)
			$object = new GoodsProperty;

		$this->_object = $object;

		//attributes
		$this->title = $template->title;
		$this->values = $template->getValues();
		$this->value = $object->value;

		parent::__construct($config);
	}

	/**
	 * Property id getter
	 * @return integer
	 */
	public function getPropertyId()
	{
		return $this->_template->id;
	}

	/**
	 * Property type getter
	 * @return integer
	 */
	public function getType()
	{
		return $this->_template->type;
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['value', function() {
				if (!$this->_template->validateValue($this->value)) {
					$this->addError('value', Yii::t('catalog', 'The value is incorrect.'));
				}
			}],
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
		$object->property_id = $this->_template->id;
		$object->value = $this->_template->formatValue($this->value);

		if (!$object->save(false))
			return false;

		return true;
	}

}
