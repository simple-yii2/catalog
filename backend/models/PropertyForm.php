<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use helpers\Translit;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Property;

/**
 * Category property form
 */
class PropertyForm extends Model
{

	/**
	 * @var string Title
	 */
	public $name;

	/**
	 * @var integer Type
	 */
	public $type;

	/**
	 * @var string[] Values
	 */
	public $values = [];

	/**
	 * @var string Measure unit
	 */
	public $unit;

	/**
	 * @var Property
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Property|null $object 
	 */
	public function __construct(Property $object = null, $config = [])
	{
		if ($object === null)
			$object = new Property;

		$this->_object = $object;

		//attributes
		$this->name = $object->name;
		$this->type = $object->type;
		$this->values = $object->values;
		$this->unit = $object->unit;

		parent::__construct($config);
	}

	/**
	 * Id getter
	 * @return integer|null
	 */
	public function getId()
	{
		return $this->_object->id;
	}

	/**
	 * Read-only getter
	 * @return boolean
	 */
	public function getReadOnly()
	{
		return $this->_object->readOnly;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'name' => Yii::t('catalog', 'Title'),
			'type' => Yii::t('catalog', 'Type'),
			'values' => Yii::t('catalog', 'Values'),
			'unit' => Yii::t('catalog', 'Unit'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['name', 'string', 'max' => 50],
			['type', 'in', 'range' => Property::getTypes()],
			['values', 'each', 'rule' => ['string', 'max' => 30]],
			['unit', 'string', 'max' => 10],
		];
	}

	/**
	 * Save
	 * @param Category $category 
	 * @param boolean $runValidation 
	 * @return boolean
	 */
	public function save(Category $category, $runValidation = true)
	{
		if ($this->_object->readOnly)
			return false;

		if ($runValidation && !$this->validate())
			return false;

		$object = $this->_object;

		$object->category_id = $category->id;
		$object->name = $this->name;
		$object->type = $this->type;
		$object->values = $this->values;
		$object->unit = $this->unit;

		$object->makeAlias();

		if (!$object->save(false))
			return false;

		return true;
	}

}
