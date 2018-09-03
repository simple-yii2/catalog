<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use cms\catalog\common\helpers\CurrencyHelper;
use cms\catalog\common\models\Currency;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Delivery;
use cms\catalog\common\models\Product;
use cms\catalog\common\models\ProductBarcode;
use cms\catalog\common\models\ProductImage;
use cms\catalog\common\models\ProductProperty;
use cms\catalog\common\models\ProductRecommended;
use cms\catalog\common\models\Store;
use cms\catalog\common\models\StoreProduct;
use cms\catalog\common\models\Vendor;

/**
 * Editing form
 */
class ProductForm extends Model
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
     * @var integer Product availability (in stock, under the order, out of stock)
     */
    public $availability;

    /**
     * @var ProductBarcodeForm[] Barcodes
     */
    private $_barcodes = [];

    /**
     * @var ProductImageForm[] Images
     */
    private $_images = [];

    /**
     * @var ProductPropertyForm[] Properties
     */
    private $_properties = [];

    /**
     * @var ProductStoreForm[] Stores
     */
    private $_stores = [];

    /**
     * @var ProductRecommendedForm[] Recommended
     */
    private $_recommended = [];

    /**
     * @var Product
     */
    private $_object;

    /**
     * @inheritdoc
     * @param Product|null $object 
     */
    public function __construct(Product $object = null, $config = [])
    {
        if ($object === null) {
            $object = new Product([
                'active' => true,
                'imageCount' => 0,
                'currency_id' => CurrencyHelper::getApplicationCurrencyId(),
            ]);
        }

        $this->_object = $object;

        //file caching
        Yii::$app->storage->cacheObject($object);

        //attributes
        parent::__construct(array_replace([
            'category_id' => $object->category_id,
            'active' => $object->active == 0 ? '0' : '1',
            'name' => $object->name,
            'model' => $object->model,
            'description' => $object->description,
            'currency_id' => $object->currency_id,
            'price' => $object->price,
            'oldPrice' => $object->oldPrice,
            'vendor_id' => $object->vendor_id,
            'countryOfOrigin' => $object->countryOfOrigin,
            'length' => $object->length,
            'width' => $object->width,
            'height' => $object->height,
            'weight' => $object->weight,
            'availability' => $object->availability,
            'barcodes' => $object->barcodes,
            'images' => $object->images,
            'properties' => $object->properties,
            // 'stores' => $object->stores,
            'recommended' => $object->recommended,
        ], $config));
    }

    /**
     * Model getter
     * @return Product
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * Barcodes getter
     * @return ProductBarcodeForm[]
     */
    public function getBarcodes()
    {
        return $this->_barcodes;
    }

    /**
     * Barcodes setter
     * @param ProductBarcode[]|array[] $value 
     * @return void
     */
    public function setBarcodes($value)
    {
        $this->setArrayAttribute('_barcodes', ProductBarcode::className(), ProductBarcodeForm::className(), $value);
    }

    /**
     * Images getter
     * @return ProductImageForm[]
     */
    public function getImages()
    {
        return $this->_images;
    }

    /**
     * Images setter
     * @param ProductImage[]|array[] $value 
     * @return void
     */
    public function setImages($value)
    {
        $this->setArrayAttribute('_images', ProductImage::className(), ProductImageForm::className(), $value);
    }

    /**
     * Properties getter
     * @return ProductPropertyForm[]
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Properties setter
     * @param ProductProperty[]|array[] $value Properties
     * @return void
     */
    public function setProperties($value)
    {
        $templates = [];
        $category = Category::findOne($this->category_id);
        if ($category !== null) {
            $templates = array_merge($category->getParentProperties(), $category->properties);
        }

        $this->SetArrayAttributeWithTemplate('_properties', ProductProperty::className(), ProductPropertyForm::className(), $value, $templates, 'property_id');
    }

    /**
     * Stores getter
     * @return ProductStoreForm[]
     */
    public function getStores()
    {
        return $this->_stores;
    }

    /**
     * Stores setter
     * @param StoreProduct[]|array[] $value 
     * @return void
     */
    public function setStores($value)
    {
        $templates = Store::find()->all();

        $this->SetArrayAttributeWithTemplate('_stores', StoreProduct::className(), ProductStoreForm::className(), $value, $templates, 'store_id');
    }

    /**
     * Recommended getter
     * @return ProductRecommendedForm[]
     */
    public function getRecommended()
    {
        return $this->_recommended;
    }

    /**
     * Recommended setter
     * @param ProductRecommended[]|array[] $value 
     * @return void
     */
    public function setRecommended($value)
    {
        //if there are arrays to set, preload objects
        $items = [];
        $ids = [];
        if (is_array($value)) {
            foreach ($value as $item) {
                $id = ArrayHelper::getValue($item, 'id');
                if ($id !== null) {
                    $items[$id] = $item;
                    if (is_array($item)) {
                        $ids[] = $id;
                    }
                }
            }
        }
        if (!empty($ids)) {
            foreach (Product::findAll($ids) as $object) {
                $items[$object->id] = $object;
            }
        }

        $this->setArrayAttribute('_recommended', Product::className(), ProductRecommendedForm::className(), $items);
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
            'vendor_id' => Yii::t('catalog', 'Vendor'),
            'countryOfOrigin' => Yii::t('catalog', 'Country of origin'),
            'length' => Yii::t('catalog', 'Length'),
            'width' => Yii::t('catalog', 'Width'),
            'height' => Yii::t('catalog', 'Height'),
            'weight' => Yii::t('catalog', 'Weight'),
            'availability' => Yii::t('catalog', 'Availability'),
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
            ['active', 'boolean'],
            [['name', 'model', 'countryOfOrigin'], 'string', 'max' => 100],
            ['description', 'string', 'max' => 65535],
            [['price', 'oldPrice'], 'double'],
            ['oldPrice', 'compare', 'compareAttribute' => 'price', 'operator' => '>', 'type' => 'number'],
            [['length', 'width', 'height'], 'integer', 'min' => 1],
            ['weight', 'double', 'min' => 0.001],
            ['availability', 'in', 'range' => Product::getAvailabilityValues()],
            [['category_id', 'name', 'price'], 'required'],
            [['barcodes', 'images', 'properties', 'stores', 'recommended'], function($attribute, $params) {
                $hasError = false;
                foreach ($this->$attribute as $model) {
                    if (!$model->validate()) {
                        $hasError = true;
                    }
                }

                if ($hasError) {
                    $this->addError($attribute . '[]', 'Items validation error.');
                }
            }],
        ];
    }

    /**
     * Save
     * @param boolean $runValidation 
     * @return boolean
     */
    public function save($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }

        $category = Category::findOne($this->category_id);
        if ($category === null) {
            return false;
        }

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
        $object->description = HtmlPurifier::process($this->description, function($config) {
            $config->set('Attr.EnableID', true);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(?:http:)?//(?:www.youtube.com/embed/|player.vimeo.com/video/)%');
        });
        $object->price = empty($this->price) ? null : (float) $this->price;
        $object->oldPrice = empty($this->oldPrice) ? null : (float) $this->oldPrice;
        $object->vendor_id = $vendor === null ? null : $vendor->id;
        $object->vendor = $vendor === null ? null : $vendor->name;
        $object->countryOfOrigin = $this->countryOfOrigin;
        $object->length = empty($this->length) ? null : (int) $this->length;
        $object->width = empty($this->width) ? null : (int) $this->width;
        $object->height = empty($this->height) ? null : (int) $this->height;
        $object->weight = empty($this->weight) ? null : (float) $this->weight;
        $object->availability = (integer) $this->availability;
        $object->modifyDate = gmdate('Y-m-d H:i:s');
        $object->thumb = null;
        $object->imageCount = sizeof($this->_images);
        $object->quantity = array_sum(array_map(function($v) {
            return (integer) $v->quantity;
        }, $this->getStores()));

        Yii::$app->storage->storeObject($object);

        if (!$object->save(false)) {
            return false;
        }

        if ($object->alias === null) {
            $object->makeAlias();
            $object->update(false, ['alias']);
        }

        //relations
        $this->saveBarcodes();
        $this->saveImages();
        $this->saveProperties();
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
        foreach ($object->barcodes as $item) {
            $old[$item->id] = $item;
        }

        //insert/update
        foreach ($this->_barcodes as $model) {
            $model->save($object, false);
            unset($old[$model->getObject()->id]);
        }

        //delete
        foreach ($old as $item) {
            $item->delete();
        }
    }

    /**
     * Save images
     * @return void
     */
    private function saveImages()
    {
        $object = $this->_object;

        $old = [];
        foreach ($object->images as $item) {
            $old[$item->id] = $item;
        }

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
        foreach ($object->properties as $item) {
            $old[$item->property_id] = $item;
        }

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
    }

    /**
     * Save stores
     * @return void
     */
    private function saveStores()
    {
        $object = $this->_object;

        $old = [];
        foreach ($object->stores as $item) {
            $old[$item->store_id] = $item;
        }

        //insert/update
        foreach ($this->_stores as $model) {
            if (!empty($model->quantity)) {
                $model->save($object, false);
                unset($old[$model->getTemplate()->id]);
            }
        }

        //delete
        foreach ($old as $item) {
            $item->delete();
        }
    }

    /**
     * Save recommended
     * @return void
     */
    private function saveRecommended()
    {
        $object = $this->_object;

        $old = [];
        foreach ($object->recommended as $item) {
            $old[$item->id] = $item;
        }

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
        foreach ($old as $item) {
            $object->unlink('recommended', $item, true);
        }
    }

}
