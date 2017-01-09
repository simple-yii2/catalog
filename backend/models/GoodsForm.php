<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Category;
use cms\catalog\common\models\Goods;
use cms\catalog\common\models\GoodsImage;
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
	 * @var GoodsImageForm[] Images
	 */
	private $_images = [];

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
		$this->images = $object->images;
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
	 * Images getter
	 * @return GoodsImageForm[]
	 */
	public function getImages()
	{
		return $this->_images;
	}

	/**
	 * Images setter
	 * @param GoodsImage[]|array[] $value 
	 * @return void
	 */
	public function setImages($value)
	{
		$old = [];
		foreach ($this->_images as $item) {
			$old[$item->getId()] = $item;
		}

		$this->_images = [];
		if (is_array($value)) {
			foreach ($value as $item) {
				if ($item instanceof GoodsImage) {
					$object = $item;
					$id = $item->id;
					$attributes = $item->getAttributes();
				} else {
					$object = new GoodsImage;
					$id = isset($item['id']) ? $item['id'] : null;
					$attributes = $item;
				}
				if (isset($old[$id])) {
					$model = $old[$id];
				} else {
					$model = new GoodsImageForm($object);
				}
				$model->setAttributes($attributes);
				$this->_images[] = $model;
			}
		}
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
			'images' => Yii::t('catalog', 'Images'),
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
			['images', function($attribute, $params) {
				$hasError = false;
				foreach ($this->_images as $model) {
					if (!$model->validate())
						$hasError = true;
				}

				if ($hasError)
					$this->addError($attribute . '[]', 'Images validation error.');
			}],
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
		$object->thumb = null;
		$object->imageCount = sizeof($this->_images);

		if (!$object->save(false))
			return false;

		if ($object->alias === null) {
			$object->makeAlias();
			$object->update(false, ['alias']);
		}


		//update images
		$old = [];
		foreach ($object->images as $item) {
			$old[$item->id] = $item;
		};
		//insert/update
		foreach ($this->_images as $item) {
			$item->save($object, false);
			unset($old[$item->getId()]);
		}
		//delete
		foreach ($old as $item) {
			Yii::$app->storage->removeObject($item);
			$item->delete();
		}


		if (!empty($this->_images)) {
			$object->thumb = $this->_images[0]->getObject()->thumb;
			$object->update(false, ['thumb']);
		}


		//update properties
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
