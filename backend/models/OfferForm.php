<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Delivery;
use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferBarcode;
use cms\catalog\common\models\OfferImage;
use cms\catalog\common\models\OfferProperty;
use cms\catalog\common\models\OfferDelivery;
use cms\catalog\common\models\OfferRecommended;
use cms\catalog\common\models\Store;
use cms\catalog\common\models\StoreOffer;
use cms\catalog\common\models\Vendor;

/**
 * Editing form
 */
class OfferForm extends Model
{

	use ArrayAttributeTrait;

	/**
	 * @var integer Category id
	 */
	public $category_id;

	/**
	 * @var integer Vendor id
	 */
	public $vendor_id;

	/**
	 * @var integer Currency id
	 */
	public $currency_id;

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
	 * @var boolean Use common delivery
	 */
	public $defaultDelivery;

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
	 * @var integer Weight in kg
	 */
	public $weight;

	/**
	 * @var OfferBarcodeForm[] Barcodes
	 */
	private $_barcodes = [];

	/**
	 * @var OfferImageForm[] Images
	 */
	private $_images = [];

	/**
	 * @var OfferPropertyForm[] Properties
	 */
	private $_properties = [];

	/**
	 * @var OfferDeliveryForm[] Delivery types
	 */
	private $_delivery = [];

	/**
	 * @var OfferStoreForm[] Stores
	 */
	private $_stores = [];

	/**
	 * @var OfferRecommendedForm[] Recommended
	 */
	private $_recommended = [];

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
		parent::__construct(array_merge([
			'category_id' => $object->category_id,
			'active' => $object->active == 0 ? '0' : '1',
			'name' => $object->name,
			'model' => $object->model,
			'description' => $object->description,
			'currency_id' => $object->currency_id,
			'price' => $object->price,
			'oldPrice' => $object->oldPrice,
			'storeAvailable' => $object->storeAvailable == 0 ? '0' : '1',
			'pickupAvailable' => $object->pickupAvailable == 0 ? '0' : '1',
			'deliveryAvailable' => $object->deliveryAvailable == 0 ? '0' : '1',
			'defaultDelivery' => $object->defaultDelivery == 0 ? '0' : '1',
			'vendor_id' => $object->vendor_id,
			'countryOfOrigin' => $object->countryOfOrigin,
			'length' => $object->length,
			'width' => $object->width,
			'height' => $object->height,
			'weight' => $object->weight,
			'barcodes' => $object->barcodes,
			'images' => $object->images,
			'properties' => $object->properties,
			'delivery' => $object->delivery,
			'stores' => $object->stores,
			'recommended' => $object->recommended,
		], $config));
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
	 * Barcodes getter
	 * @return OfferBarcodeForm[]
	 */
	public function getBarcodes()
	{
		return $this->_barcodes;
	}

	/**
	 * Barcodes setter
	 * @param OfferBarcode[]|array[] $value 
	 * @return void
	 */
	public function setBarcodes($value)
	{
		$this->setArrayAttribute('_barcodes', OfferBarcode::className(), OfferBarcodeForm::className(), $value);
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
		$this->setArrayAttribute('_images', OfferImage::className(), OfferImageForm::className(), $value);
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
		$templates = [];
		$category = Category::findOne($this->category_id);
		if ($category !== null)
			$templates = array_merge($category->getParentProperties(), $category->properties);

		$this->SetArrayAttributeWithTemplate('_properties', OfferProperty::className(), OfferPropertyForm::className(), $value, $templates, 'property_id');
	}

	/**
	 * Delivery getter
	 * @return OfferDeliveryForm[]
	 */
	public function getDelivery()
	{
		return $this->_delivery;
	}

	/**
	 * Delivery setter
	 * @param OfferDelivery[]|array[] $value Delivery
	 * @return void
	 */
	public function setDelivery($value)
	{
		$templates = Delivery::find()->all();

		$this->SetArrayAttributeWithTemplate('_delivery', OfferDelivery::className(), OfferDeliveryForm::className(), $value, $templates, 'delivery_id');
		if ($this->defaultDelivery != 0) {
			foreach ($this->_delivery as $model)
				$model->active = 1;
		}
	}

	/**
	 * Stores getter
	 * @return OfferStoreForm[]
	 */
	public function getStores()
	{
		return $this->_stores;
	}

	/**
	 * Stores setter
	 * @param StoreOffer[]|array[] $value 
	 * @return void
	 */
	public function setStores($value)
	{
		$templates = Store::find()->all();

		$this->SetArrayAttributeWithTemplate('_stores', StoreOffer::className(), OfferStoreForm::className(), $value, $templates, 'store_id');
	}

	/**
	 * Recommended getter
	 * @return OfferRecommendedForm[]
	 */
	public function getRecommended()
	{
		return $this->_recommended;
	}

	/**
	 * Recommended setter
	 * @param OfferRecommended[]|array[] $value 
	 * @return void
	 */
	public function setRecommended($value)
	{
		//if there are arrays to set, preload objects
		$items = [];
		$ids = [];
		foreach ($value as $item) {
			$id = ArrayHelper::getValue($item, 'id');
			if ($id !== null) {
				$items[$id] = $item;
				if (is_array($item))
					$ids[] = $id;
			}
		}
		if (!empty($ids)) {
			foreach (Offer::findAll($ids) as $object)
				$items[$object->id] = $object;
		}

		$this->setArrayAttribute('_recommended', Offer::className(), OfferRecommendedForm::className(), $items);
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
			'currency_id' => Yii::t('catalog', 'Currency'),
			'price' => Yii::t('catalog', 'Price'),
			'oldPrice' => Yii::t('catalog', 'Old price'),
			'storeAvailable' => Yii::t('catalog', 'Can buy in the sales area'),
			'pickupAvailable' => Yii::t('catalog', 'Can buy with self-delivery'),
			'deliveryAvailable' => Yii::t('catalog', 'Can buy with delivery'),
			'defaultDelivery' => Yii::t('catalog', 'Use default delivery'),
			'vendor_id' => Yii::t('catalog', 'Vendor'),
			'countryOfOrigin' => Yii::t('catalog', 'Country of origin'),
			'length' => Yii::t('catalog', 'Length'),
			'width' => Yii::t('catalog', 'Width'),
			'height' => Yii::t('catalog', 'Height'),
			'weight' => Yii::t('catalog', 'Weight'),
			'barcodes' => Yii::t('catalog', 'Barcodes'),
			'images' => Yii::t('catalog', 'Images'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['category_id', 'vendor_id', 'currency_id'], 'integer'],
			[['active', 'storeAvailable', 'pickupAvailable', 'deliveryAvailable', 'defaultDelivery'], 'boolean'],
			[['name', 'model', 'countryOfOrigin'], 'string', 'max' => 100],
			['description', 'string', 'max' => 1000],
			[['price', 'oldPrice'], 'double'],
			[['length', 'width', 'height'], 'integer', 'min' => 1],
			['weight', 'double', 'min' => 0.001],
			[['category_id', 'name'], 'required'],
			[['barcodes', 'images', 'properties', 'delivery', 'stores', 'recommended'], function($attribute, $params) {
				$hasError = false;
				foreach ($this->$attribute as $model) {
					if (!$model->validate())
						$hasError = true;
				}

				if ($hasError)
					$this->addError($attribute . '[]', 'Items validation error.');
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

		$currency = Currency::findOne($this->currency_id);

		$vendor = Vendor::findOne($this->vendor_id);

		$object = $this->_object;

		$object->category_id = $category->id;
		$object->category_lft = $category->lft;
		$object->category_rgt = $category->rgt;
		$object->active = $this->active == 0 ? false : true;
		$object->name = $this->name;
		$object->model = $this->model;
		$object->currency_id = $currency === null ? null : $currency->id;
		$object->description = $this->description;
		$object->price = empty($this->price) ? null : (float) $this->price;
		$object->oldPrice = empty($this->oldPrice) ? null : (float) $this->oldPrice;
		$object->storeAvailable = $this->storeAvailable == 0 ? false : true;
		$object->pickupAvailable = $this->pickupAvailable == 0 ? false : true;
		$object->deliveryAvailable = $this->deliveryAvailable == 0 ? false : true;
		$object->defaultDelivery = $this->defaultDelivery == 0 ? false : true;
		$object->vendor_id = $vendor === null ? null : $vendor->id;
		$object->vendor = $vendor === null ? null : $vendor->name;
		$object->countryOfOrigin = $this->countryOfOrigin;
		$object->length = empty($this->length) ? null : (int) $this->length;
		$object->width = empty($this->width) ? null : (int) $this->width;
		$object->height = empty($this->height) ? null : (int) $this->height;
		$object->weight = empty($this->weight) ? null : (float) $this->weight;
		$object->modifyDate = gmdate('Y-m-d H:i:s');
		$object->thumb = null;
		$object->imageCount = sizeof($this->_images);
		$object->quantity = array_sum(array_map(function($v) {
			return (integer) $v->quantity;
		}, $this->getStores()));

		if (!$object->save(false))
			return false;

		if ($object->alias === null) {
			$object->makeAlias();
			$object->update(false, ['alias']);
		}

		//relations
		$this->saveBarcodes();
		$this->saveImages();
		$this->saveProperties();
		$this->saveDelivery();
		$this->saveStores();
		$this->saveRecommended();

		return true;
	}

	/**
	 * Save barcodes
	 * @return void
	 */
	private function saveBarcodes()
	{
		$object = $this->_object;

		$old = [];
		foreach ($object->barcodes as $item)
			$old[$item->id] = $item;

		//insert/update
		foreach ($this->_barcodes as $model) {
			$model->save($object, false);
			unset($old[$model->getObject()->id]);
		}

		//delete
		foreach ($old as $item)
			$item->delete();
	}

	/**
	 * Save images
	 * @return void
	 */
	private function saveImages()
	{
		$object = $this->_object;

		$old = [];
		foreach ($object->images as $item)
			$old[$item->id] = $item;

		//insert/update
		foreach ($this->_images as $model) {
			$model->save($object, false);
			unset($old[$model->getObject()->id]);
		}

		//delete
		foreach ($old as $item) {
			Yii::$app->storage->removeObject($item);
			$item->delete();
		}

		//object thumb
		if (!empty($this->_images)) {
			$object->thumb = $this->_images[0]->getObject()->thumb;
			$object->update(false, ['thumb']);
		}
	}

	/**
	 * Save properties
	 * @return void
	 */
	private function saveProperties()
	{
		$object = $this->_object;

		$old = [];
		foreach ($object->properties as $item)
			$old[$item->property_id] = $item;

		//insert/update
		foreach ($this->_properties as $item) {
			if ($item->value !== '' && $item->value !== null) {
				$item->save($object, false);
				unset($old[$item->property_id]);
			}
		}

		//delete
		foreach ($old as $item)
			$item->delete();
	}

	/**
	 * Save delivery
	 * @return void
	 */
	private function saveDelivery()
	{
		$object = $this->_object;

		$old = [];
		foreach ($object->delivery as $item)
			$old[$item->delivery_id] = $item;

		//insert/update
		if (!$object->defaultDelivery) {
			foreach ($this->_delivery as $model) {
				if ($model->active != 0) {
					$model->save($object, false);
					unset($old[$model->getTemplate()->id]);
				}
			}
		}

		//delete
		foreach ($old as $item)
			$item->delete();
	}

	/**
	 * Save stores
	 * @return void
	 */
	private function saveStores()
	{
		$object = $this->_object;

		$old = [];
		foreach ($object->stores as $item)
			$old[$item->store_id] = $item;

		//insert/update
		foreach ($this->_stores as $model) {
			if (!empty($model->quantity)) {
				$model->save($object, false);
				unset($old[$model->getTemplate()->id]);
			}
		}

		//delete
		foreach ($old as $item)
			$item->delete();
	}

	/**
	 * Save recommended
	 * @return void
	 */
	private function saveRecommended()
	{
		$object = $this->_object;

		$old = [];
		foreach ($object->recommended as $item)
			$old[$item->id] = $item;

		//insert/update
		foreach ($this->_recommended as $model) {
			$item = $model->getObject();
			if (array_key_exists($item->id, $old)) {
				unset($old[$item->id]);
			} else {
				$object->link('recommended', $item);
			}
		}

		//delete
		foreach ($old as $item)
			$object->unlink('recommended', $item, true);
	}

}
