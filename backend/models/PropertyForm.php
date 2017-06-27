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
	 * @var Property
	 */
	private $_model;

	/**
	 * @inheritdoc
	 * @param Property|null $model 
	 */
	public function __construct(Property $model = null, $config = [])
	{
		if ($model === null)
			$model = new Property;

		$this->_model = $model;

		//attributes
		$this->name = $model->name;
		$this->type = $model->type;

		$this->values = $model->getValues();

		parent::__construct($config);
	}

	/**
	 * Id getter
	 * @return integer|null
	 */
	public function getId()
	{
		return $this->_model->id;
	}

	/**
	 * Read-only getter
	 * @return boolean
	 */
	public function getReadOnly()
	{
		return $this->_model->readOnly;
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
		if ($this->_model->readOnly)
			return false;

		if ($runValidation && !$this->validate())
			return false;

		$model = $this->_model;

		$model->category_id = $category->id;
		$model->name = $this->name;
		$model->type = $this->type;

		$model->setValues($this->values);

		$model->makeAlias();

		if (!$model->save(false))
			return false;

		return true;
	}

}
