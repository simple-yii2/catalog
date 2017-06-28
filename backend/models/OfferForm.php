<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use cms\catalog\common\models\Category;
use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferImage;
use cms\catalog\common\models\OfferProperty;
use cms\catalog\common\models\Vendor;

/**
 * Editing form
 */
class OfferForm extends Model
{

	/**
	 * @var integer Category id
	 */
	public $category_id;

	/**
	 * @var integer Vendor id
	 */
	public $vendor_id;

	/**
	 * @var boolean Active
	 */
	public $active;

	/**
	 * @var string Name
	 */
	public $name;

	/**
	 * @var string Model
	 */
	public $model;

	/**
	 * @var string Description
	 */
	public $description;

	/**
	 * @var float Price
	 */
	public $price;

	/**
	 * @var float Old price
	 */
	public $oldPrice;

	/**
	 * @var boolean Can buy in sales area
	 */
	public $storeAvailable;

	/**
	 * @var boolean Can buy with pickup
	 */
	public $pickupAvailable;

	/**
	 * @var boolean Can buy with delivery
	 */
	public $deliveryAvailable;

	/**
	 * @var string Country of origin
	 */
	public $countryOfOrigin;

	/**
	 * @var integer Length in mm
	 */
	public $length;

	/**
	 * @var integer Width in mm
	 */
	public $width;

	/**
	 * @var integer Height in mm
	 */
	public $height;

	/**
	 * @var integer Weight in g
	 */
	public $weight;

	/**
	 * @var OfferImageForm[] Images
	 */
	private $_images = [];

	/**
	 * @var OfferPropertyForm[] Properties
	 */
	private $_properties = [];

	/**
	 * @var Offer
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Offer|null $object 
	 */
	public function __construct(Offer $object = null, $config = [])
	{
		if ($object === null)
			$object = new Offer;

		$this->_object = $object;

		//attributes
		$this->category_id = $object->category_id;
		$this->active = $object->active == 0 ? '0' : '1';
		$this->name = $object->name;
		$this->model = $object->model;
		$this->description = $object->description;
		$this->price = $object->price;
		$this->oldPrice = $object->oldPrice;
		$this->storeAvailable = $object->storeAvailable == 0 ? '0' : '1';
		$this->pickupAvailable = $object->pickupAvailable == 0 ? '0' : '1';
		$this->deliveryAvailable = $object->deliveryAvailable == 0 ? '0' : '1';
		$this->vendor_id = $object->vendor_id;
		$this->countryOfOrigin = $object->countryOfOrigin;
		$this->length = $object->length;
		$this->width = $object->width;
		$this->height = $object->height;
		$this->weight = $object->weight;
		$this->images = $object->images;
		$this->properties = $object->properties;

		parent::__construct($config);
	}

	/**
	 * Model getter
	 * @return Offer
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * Images getter
	 * @return OfferImageForm[]
	 */
	public function getImages()
	{
		return $this->_images;
	}

	/**
	 * Images setter
	 * @param OfferImage[]|array[] $value 
	 * @return void
	 */
	public function setImages($value)
	{
		$old = [];
		foreach ($this->_images as $item) {
			if ($id = $item->id)
				$old[$id] = $item;
		}

		$this->_images = [];

		if (!is_array($value))
			return;

		foreach ($value as $item) {
			if ($item instanceof OfferImage) {
				$model = $item;
				$id = $item->id;
				$attributes = $item->getAttributes();
			} else {
				$model = null;
				$id = ArrayHelper::getValue($item, 'id');
				$attributes = $item;
			}

			$form = array_key_exists($id, $old) ? $old[$id] : new OfferImageForm($model);

			$form->setAttributes($attributes);
			$this->_images[] = $form;
		}

	}

	/**
	 * Properties getter
	 * @return OfferPropertyForm[]
	 */
	public function getProperties()
	{
		return $this->_properties;
	}

	/**
	 * Properties setter
	 * @param OfferProperty[]|array[] $value Properties
	 * @return void
	 */
	public function setProperties($value)
	{
		$items = [];
		if (is_array($value)) {
			foreach ($value as $property_id => $item) {
				if ($item instanceof OfferProperty) {
					$items[$item->property_id] = $item;
				} else {
					$items[$property_id] = ['value' => $item]; //?
				}
			}
		}

		$old = [];
		foreach ($this->_properties as $item) {
			if ($property_id = $item->property_id)
				$old[$property_id] = $item;
		}

		$category = Category::findOne($this->category_id);

		$this->_properties = [];
		if ($category !== null) {
			foreach (array_merge($category->getParentProperties(), $category->properties) as $item) {
				if (isset($old[$item->id])) {
					$model = $old[$item->id];
				} else {
					$model = null;
					if (isset($items[$item->id]) && ($items[$item->id] instanceof OfferProperty))
						$model = $items[$item->id];

					$model = new OfferPropertyForm($item, $model);
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
			'name' => Yii::t('catalog', 'Name'),
			'model' => Yii::t('catalog', 'Model'),
			'description' => Yii::t('catalog', 'Description'),
			'price' => Yii::t('catalog', 'Price'),
			'oldPrice' => Yii::t('catalog', 'Old price'),
			'storeAvailable' => Yii::t('catalog', 'Can buy in the sales area'),
			'pickupAvailable' => Yii::t('catalog', 'Can buy with self-delivery'),
			'deliveryAvailable' => Yii::t('catalog', 'Can buy with delivery'),
			'vendor_id' => Yii::t('catalog', 'Vendor'),
			'countryOfOrigin' => Yii::t('catalog', 'Country of origin'),
			'length' => Yii::t('catalog', 'Length'),
			'width' => Yii::t('catalog', 'Width'),
			'height' => Yii::t('catalog', 'Height'),
			'weight' => Yii::t('catalog', 'Weight'),
			'images' => Yii::t('catalog', 'Images'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['category_id', 'vendor_id'], 'integer'],
			[['active', 'storeAvailable', 'pickupAvailable', 'deliveryAvailable'], 'boolean'],
			[['name', 'model', 'countryOfOrigin'], 'string', 'max' => 100],
			['description', 'string', 'max' => 1000],
			[['price', 'oldPrice'], 'double'],
			[['length', 'width', 'height', 'weight'], 'integer', 'min' => 1],
			[['category_id', 'name'], 'required'],
			['images', function($attribute, $params) {
				$hasError = false;
				foreach ($this->_images as $formModel) {
					if (!$formModel->validate())
						$hasError = true;
				}

				if ($hasError)
					$this->addError($attribute . '[]', 'Images validation error.');
			}],
			['properties', function($attribute, $params) {
				$hasError = false;
				foreach ($this->_properties as $formModel) {
					if (!$formModel->validate())
						$hasError = true;
				}

				if ($hasError)
					$this->addError($attribute . '[]', 'Properties validation error.');
			}],
		];
	}

	/**
	 * Save
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$category = Category::findOne($this->category_id);
		if ($category === null)
			return false;

		$vendor = Vendor::findOne($this->vendor_id);

		$object = $this->_object;

		$object->category_id = $category->id;
		$object->category_lft = $category->lft;
		$object->category_rgt = $category->rgt;
		$object->active = $this->active == 0 ? false : true;
		$object->name = $this->name;
		$object->model = $this->model;
		$object->description = $this->description;
		$object->price = empty($this->price) ? null : (float) $this->price;
		$object->oldPrice = empty($this->oldPrice) ? null : (float) $this->oldPrice;
		$object->storeAvailable = $this->storeAvailable == 0 ? false : true;
		$object->pickupAvailable = $this->pickupAvailable == 0 ? false : true;
		$object->deliveryAvailable = $this->deliveryAvailable == 0 ? false : true;
		$object->vendor_id = $vendor === null ? null : $vendor->id;
		$object->vendor = $vendor === null ? null : $vendor->name;
		$object->countryOfOrigin = $this->countryOfOrigin;
		$object->length = empty($this->length) ? null : (int) $this->length;
		$object->width = empty($this->width) ? null : (int) $this->width;
		$object->height = empty($this->height) ? null : (int) $this->height;
		$object->weight = empty($this->weight) ? null : (int) $this->weight;
		$object->modifyDate = gmdate('Y-m-d H:i:s');
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
			unset($old[$item->id]);
		}
		//delete
		foreach ($old as $item) {
			Yii::$app->storage->removeObject($item);
			$item->delete();
		}


		if (!empty($this->_images)) {
			$object->thumb = $this->_images[0]->getModel()->thumb;
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
				unset($old[$item->property_id]);
			}
		}
		//delete
		foreach ($old as $item) {
			$item->delete();
		}


		return true;
	}

}
