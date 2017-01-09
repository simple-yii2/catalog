<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Category;
use cms\catalog\common\models\Goods;
use cms\catalog\common\models\GoodsProperty;

/**
 * Editing form
 */
class GoodsForm extends Model
{

	/**
	 * @var integer Category id
	 */
	public $category_id;

	/**
	 * @var boolean Active
	 */
	public $active;

	/**
	 * @var string Title
	 */
	public $title;

	/**
	 * @var string Description
	 */
	public $description;

	/**
	 * @var float Price
	 */
	public $price;

	/**
	 * @var GoodsPropertyForm[] Properties
	 */
	private $_properties = [];

	/**
	 * @var Goods
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Goods|null $object 
	 */
	public function __construct(Goods $object = null, $config = [])
	{
		if ($object === null)
			$object = new Goods;

		$this->_object = $object;

		//attributes
		$this->category_id = $object->category_id;
		$this->active = $object->active == 0 ? '0' : '1';
		$this->title = $object->title;
		$this->description = $object->description;
		$this->price = $object->price;

		$this->properties = $object->properties;

		parent::__construct($config);
	}

	/**
	 * Object id getter
	 * @return integer
	 */
	public function getId()
	{
		return $this->_object->id;
	}

	/**
	 * Object title getter
	 * @return string
	 */
	public function getObjectTitle()
	{
		return $this->_object->title;
	}

	/**
	 * Properties getter
	 * @return GoodsPropertyForm[]
	 */
	public function getProperties()
	{
		return $this->_properties;
	}

	/**
	 * Properties setter
	 * @param GoodsProperty[]|array[] $value Properties
	 * @return void
	 */
	public function setProperties($value)
	{
		$items = [];
		if (is_array($value)) {
			foreach ($value as $property_id => $item) {
				if ($item instanceof GoodsProperty) {
					$items[$item->property_id] = $item;
				} else {
					$items[$property_id] = ['value' => $item];
				}
			}
		}

		$old = [];
		foreach ($this->_properties as $item) {
			$old[$item->getPropertyId()] = $item;
		}

		$category = Category::findOne($this->category_id);

		$this->_properties = [];
		if ($category !== null) {
			foreach (array_merge($category->getParentProperties(), $category->properties) as $item) {
				if (isset($old[$item->id])) {
					$model = $old[$item->id];
				} else {
					$object = null;
					if (isset($items[$item->id]) && ($items[$item->id] instanceof GoodsProperty))
						$object = $items[$item->id];

					$model = new GoodsPropertyForm($item, $object);
				}
				
				if (isset($items[$item->id]) && is_array($items[$item->id]))
					$model->setAttributes($items[$item->id]);

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
			'category_id' => Yii::t('catalog', 'Category'),
			'active' => Yii::t('catalog', 'Active'),
			'title' => Yii::t('catalog', 'Title'),
			'description' => Yii::t('catalog', 'Description'),
			'price' => Yii::t('catalog', 'Price'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['category_id', 'integer'],
			['active', 'boolean'],
			['title', 'string', 'max' => 100],
			['description', 'string', 'max' => 1000],
			['price', 'double'],
			[['category_id', 'title'], 'required'],
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
	 * Saving object using model attributes
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$category = Category::findOne($this->category_id);
		if ($category === null)
			return false;

		$object = $this->_object;

		$object->category_id = $category->id;
		$object->category_lft = $category->lft;
		$object->category_rgt = $category->rgt;
		$object->active = $this->active == 0 ? false : true;
		$object->title = $this->title;
		$object->description = $this->description;
		$object->price = empty($this->price) ? null : (float) $this->price;

		if (!$object->save(false))
			return false;

		if ($object->alias === null) {
			$object->makeAlias();
			$object->update(false, ['alias']);
		}

		//update relations
		$old = [];
		foreach ($object->properties as $item) {
			$old[$item->property_id] = $item;
		};
		//insert/update
		foreach ($this->_properties as $item) {
			if ($item->value !== '' && $item->value !== null) {
				$item->save($object, false);
				unset($old[$item->getPropertyId()]);
			}
		}
		//delete
		foreach ($old as $item) {
			$item->delete();
		}

		return true;
	}

}
