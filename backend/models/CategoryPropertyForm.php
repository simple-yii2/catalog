<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use helpers\Translit;
use cms\catalog\common\models\CategoryProperty;

/**
 * Category property form
 */
class CategoryPropertyForm extends Model
{

	/**
	 * @var string Title
	 */
	public $title;

	/**
	 * @var integer Type
	 */
	public $type;

	/**
	 * @var string[] Values
	 */
	public $values = [];

	/**
	 * @var CategoryProperty
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param CategoryProperty|null $object 
	 */
	public function __construct(CategoryProperty $object = null, $config = [])
	{
		if ($object === null)
			$object = new CategoryProperty;

		$this->_object = $object;

		//attributes
		$this->title = $object->title;
		$this->type = $object->type;

		$this->values = $object->getValues();

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
			'title' => Yii::t('catalog', 'Title'),
			'type' => Yii::t('catalog', 'Type'),
			'values' => Yii::t('catalog', 'Values'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['title', 'string', 'max' => 50],
			['type', 'in', 'range' => array_keys(CategoryProperty::getTypeNames())],
			['values', 'each', 'rule' => ['string', 'max' => 30]],
		];
	}

	/**
	 * Save object using model attributes
	 * @param cms\catalog\common\models\Category $category 
	 * @param boolean $runValidation 
	 * @return boolean
	 */
	public function save(\cms\catalog\common\models\Category $category, $runValidation = true)
	{
		if ($this->_object->readOnly)
			return false;

		if ($runValidation && !$this->validate())
			return false;

		$object = $this->_object;

		$object->category_id = $category->id;
		$object->title = $this->title;
		$object->type = $this->type;

		$object->setValues($this->values);

		if (!$object->save(false))
			return false;

		return true;
	}

}
