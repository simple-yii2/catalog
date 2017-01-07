<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\CategoryProperty;

class CategoryForm extends Model
{

	/**
	 * @var boolean Active
	 */
	public $active;

	/**
	 * @var string Title
	 */
	public $title;

	/**
	 * @var CategoryPropertyForm[] Properties
	 */
	private $_properties = [];

	/**
	 * @var cms\catalog\common\models\Category
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param cms\catalog\common\models\Category $object 
	 */
	public function __construct(\cms\catalog\common\models\Category $object, $config = [])
	{
		$this->_object = $object;

		//attributes
		$this->active = $object->active == 0 ? '0' : '1';
		$this->title = $object->title;

		$this->properties = array_merge($object->getParentProperties(), $object->properties);

		parent::__construct($config);
	}

	/**
	 * Properies getter
	 * @return CategoryPropertyForm[]
	 */
	public function getProperties()
	{
		return $this->_properties;
	}

	/**
	 * Properies setter
	 * @param CategoryProperty[]|array[] $value Properies
	 * @return void
	 */
	public function setProperties($value)
	{
		$old = [];
		foreach ($this->_properties as $item) {
			if ($id = $item->getId())
				$old[$id] = $item;
		}

		$this->_properties = [];

		if (!is_array($value))
			return;

		foreach ($value as $item) {
			if ($item instanceof CategoryProperty) {
				$this->_properties[] = new CategoryPropertyForm($item);
			} else {
				if (isset($item['id']) && isset($old[$item['id']])) {
					$model = $old[$item['id']];
				} else {
					$model = new CategoryPropertyForm;
				}
				$model->setAttributes($item);
				$this->_properties[] = $model;
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function attributes()
	{
		return array_merge(parent::attributes(), ['properties']);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'active' => Yii::t('catalog', 'Active'),
			'title' => Yii::t('catalog', 'Title'),
			'properties' => Yii::t('catalog', 'Properties'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['active', 'boolean'],
			['title', 'string', 'max' => 100],
			['title', 'required'],
			['properties', function($attribute, $params) {
				$hasError = false;
				foreach ($this->_properties as $model) {
					if (!$model->validate())
						$hasError = true;
				}

				if ($hasError)
					$this->addError($attribute . '[]', 'Properties validation error.');
			}],
		];
	}

	/**
	 * Object id getter
	 * @return integer
	 */
	public function getObjectId()
	{
		return $this->_object->id;
	}

	/**
	 * Object title getter
	 * @return integer
	 */
	public function getObjectTitle()
	{
		return $this->_object->title;
	}

	/**
	 * Save object using model attributes
	 * @param cms\catalog\common\models\Category|null $parent 
	 * @return boolean
	 */
	public function save(\cms\catalog\common\models\Category $parent = null)
	{
		if (!$this->validate())
			return false;

		$object = $this->_object;

		$object->active = $this->active == 1;
		$object->title = $this->title;

		if ($object->getIsNewRecord()) {
			if (!$object->appendTo($parent, false))
				return false;
		} else {
			if (!$object->save(false))
				return false;
		}

		//update relations
		$old = [];
		foreach ($object->properties as $item) {
			$old[$item->id] = $item;
		};
		//insert/update
		foreach ($this->_properties as $item) {
			$item->save($object, false);
			unset($old[$item->getId()]);
		}
		//delete
		foreach ($old as $item) {
			$item->delete();
		}

		return true;
	}

}
