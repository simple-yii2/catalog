<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use dkhlystov\forms\Form;
use cms\catalog\helpers\CurrencyHelper;
use cms\catalog\models\Category;
use cms\catalog\models\Product;
use cms\catalog\models\Vendor;

class ProductForm extends Form
{

    /**
     * @var integer
     */
    public $id;

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
     * @var string sku
     */
    public $sku;

    /**
     * @var string Name
     */
    public $name;

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
     * @inheritdoc
     */
    public function forms()
    {
        return [
            ['barcodes', Form::HAS_MANY, ProductBarcodeForm::className()],
            ['images', Form::HAS_MANY, ProductImageForm::className()],
            ['properties', Form::HAS_MANY, ProductPropertyForm::className()],
            // ['related', Form::HAS_MANY, ProductRecommendedForm::className()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => Yii::t('catalog', 'Category'),
            'active' => Yii::t('catalog', 'Active'),
            'sku' => Yii::t('catalog', 'SKU'),
            'name' => Yii::t('catalog', 'Name'),
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
        return array_merge(parent::rules(), [
            [['category_id', 'vendor_id', 'currency_id'], 'integer'],
            ['active', 'boolean'],
            ['sku', 'string', 'max' => 50],
            ['sku', 'unique', 'targetClass' => Product::className(), 'when' => function ($model, $attribute) {
                $object = Product::findOne($this->id);
                return $object === null || $object->sku != $this->sku;
            }],
            ['name', 'string', 'max' => 200],
            ['countryOfOrigin', 'string', 'max' => 100],
            ['description', 'string', 'max' => 65535],
            [['price', 'oldPrice'], 'double', 'min' => 0],
            ['oldPrice', 'compare', 'compareAttribute' => 'price', 'operator' => '>', 'type' => 'number'],
            [['length', 'width', 'height'], 'integer'],
            ['weight', 'double'],
            [['length', 'width', 'height', 'weight'], 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
            ['availability', 'in', 'range' => Product::getAvailabilityValues()],
            [['category_id', 'sku', 'name'], 'required'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        Yii::$app->storage->cacheObject($object);

        $this->id = $object->id;
        $this->category_id = $object->category_id;
        $this->active = $object->active == 0 ? '0' : '1';
        $this->sku = $object->sku;
        $this->name = $object->name;
        $this->description = $object->description;
        $this->currency_id = $object->currency_id;
        $this->price = $object->price;
        $this->oldPrice = $object->oldPrice;
        $this->vendor_id = $object->vendor_id;
        $this->countryOfOrigin = $object->countryOfOrigin;
        $this->length = $object->length;
        $this->width = $object->width;
        $this->height = $object->height;
        $this->weight = $object->weight;
        $this->availability = $object->availability;
        $this->barcodes = $object->barcodes;
        $this->images = $object->images;
        $this->properties = $object->properties;
        // $this->related = $object->related;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $category = Category::findOne($this->category_id);
        $currency = CurrencyHelper::getCurrency($this->currency_id);
        $vendor = Vendor::findOne($this->vendor_id);

        $object->category_id = ArrayHelper::getValue($category, 'id');
        $object->category_lft = ArrayHelper::getValue($category, 'lft');
        $object->category_rgt = ArrayHelper::getValue($category, 'rgt');
        $object->active = $this->active == 0 ? false : true;
        $object->sku = $this->sku;
        $object->name = $this->name;
        $object->currency_id = ArrayHelper::getValue($currency, 'id');
        $object->description = HtmlPurifier::process($this->description, function($config) {
            $config->set('Attr.EnableID', true);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(?:http:)?//(?:www.youtube.com/embed/|player.vimeo.com/video/)%');
        });
        $object->price = empty($this->price) ? null : (float) $this->price;
        $object->oldPrice = empty($this->oldPrice) ? null : (float) $this->oldPrice;
        $object->vendor_id = ArrayHelper::getValue($vendor, 'id');
        $object->vendor = ArrayHelper::getValue($vendor, 'name');
        $object->countryOfOrigin = $this->countryOfOrigin;
        $object->length = empty($this->length) ? null : (int) $this->length;
        $object->width = empty($this->width) ? null : (int) $this->width;
        $object->height = empty($this->height) ? null : (int) $this->height;
        $object->weight = empty($this->weight) ? null : (float) $this->weight;
        $object->availability = (integer) $this->availability;
        $object->barcodes = $this->barcodes;
        $object->images = $this->images;
        $object->properties = $this->properties;
        // $object->related = $this->related;
        
        $object->thumb = ArrayHelper::getValue($object, ['images', 0, 'thumb']);
        $object->imageCount = sizeof($object->images);

        Yii::$app->storage->storeObject($object);
    }

    // /**
    //  * Save
    //  * @param boolean $runValidation 
    //  * @return boolean
    //  */
    // public function save($runValidation = true)
    // {
    //     if ($runValidation && !$this->validate()) {
    //         return false;
    //     }

    //     $category = Category::findOne($this->category_id);
    //     if ($category === null) {
    //         return false;
    //     }

    //     $currency = Currency::findOne($this->currency_id);

    //     $vendor = Vendor::findOne($this->vendor_id);

    //     $object = $this->_object;

    //     $object->category_id = $category->id;
    //     $object->category_lft = $category->lft;
    //     $object->category_rgt = $category->rgt;
    //     $object->active = $this->active == 0 ? false : true;
    //     $object->sku = $this->sku;
    //     $object->name = $this->name;
    //     $object->model = $this->model;
    //     $object->currency_id = $currency === null ? null : $currency->id;
    //     $object->description = HtmlPurifier::process($this->description, function($config) {
    //         $config->set('Attr.EnableID', true);
    //         $config->set('HTML.SafeIframe', true);
    //         $config->set('URI.SafeIframeRegexp', '%^(?:http:)?//(?:www.youtube.com/embed/|player.vimeo.com/video/)%');
    //     });
    //     $object->price = empty($this->price) ? null : (float) $this->price;
    //     $object->oldPrice = empty($this->oldPrice) ? null : (float) $this->oldPrice;
    //     $object->vendor_id = $vendor === null ? null : $vendor->id;
    //     $object->vendor = $vendor === null ? null : $vendor->name;
    //     $object->countryOfOrigin = $this->countryOfOrigin;
    //     $object->length = empty($this->length) ? null : (int) $this->length;
    //     $object->width = empty($this->width) ? null : (int) $this->width;
    //     $object->height = empty($this->height) ? null : (int) $this->height;
    //     $object->weight = empty($this->weight) ? null : (float) $this->weight;
    //     $object->availability = (integer) $this->availability;
    //     $object->modifyDate = gmdate('Y-m-d H:i:s');
    //     $object->thumb = null;
    //     $object->imageCount = sizeof($this->_images);

    //     Yii::$app->storage->storeObject($object);

    //     if (!$object->save(false)) {
    //         return false;
    //     }

    //     if ($object->alias === null) {
    //         $object->makeAlias();
    //         $object->update(false, ['alias']);
    //     }

    //     //relations
    //     $this->saveBarcodes();
    //     $this->saveImages();
    //     $this->saveProperties();
    //     // $this->saveRecommended();

    //     return true;
    // }

    // /**
    //  * Save barcodes
    //  * @return void
    //  */
    // private function saveBarcodes()
    // {
    //     $object = $this->_object;

    //     $old = [];
    //     foreach ($object->barcodes as $item) {
    //         $old[$item->id] = $item;
    //     }

    //     //insert/update
    //     foreach ($this->_barcodes as $model) {
    //         $model->save($object, false);
    //         unset($old[$model->getObject()->id]);
    //     }

    //     //delete
    //     foreach ($old as $item) {
    //         $item->delete();
    //     }
    // }

    // /**
    //  * Save images
    //  * @return void
    //  */
    // private function saveImages()
    // {
    //     $object = $this->_object;

    //     $old = [];
    //     foreach ($object->images as $item) {
    //         $old[$item->id] = $item;
    //     }

    //     //insert/update
    //     foreach ($this->_images as $model) {
    //         $model->save($object, false);
    //         unset($old[$model->getObject()->id]);
    //     }

    //     //delete
    //     foreach ($old as $item) {
    //         Yii::$app->storage->removeObject($item);
    //         $item->delete();
    //     }

    //     //object thumb
    //     if (!empty($this->_images)) {
    //         $object->thumb = $this->_images[0]->getObject()->thumb;
    //         $object->update(false, ['thumb']);
    //     }
    // }

    // /**
    //  * Save properties
    //  * @return void
    //  */
    // private function saveProperties()
    // {
    //     $object = $this->_object;

    //     $old = [];
    //     foreach ($object->properties as $item) {
    //         $old[$item->property_id] = $item;
    //     }

    //     //insert/update
    //     foreach ($this->_properties as $item) {
    //         if ($item->value !== '' && $item->value !== null) {
    //             $item->save($object, false);
    //             unset($old[$item->property_id]);
    //         }
    //     }

    //     //delete
    //     foreach ($old as $item) {
    //         $item->delete();
    //     }
    // }

    // /**
    //  * Save recommended
    //  * @return void
    //  */
    // private function saveRecommended()
    // {
    //     $object = $this->_object;

    //     $old = [];
    //     foreach ($object->recommended as $item) {
    //         $old[$item->id] = $item;
    //     }

    //     //insert/update
    //     foreach ($this->_recommended as $model) {
    //         $item = $model->getObject();
    //         if (array_key_exists($item->id, $old)) {
    //             unset($old[$item->id]);
    //         } else {
    //             $object->link('recommended', $item);
    //         }
    //     }

    //     //delete
    //     foreach ($old as $item) {
    //         $object->unlink('recommended', $item, true);
    //     }
    // }

}
