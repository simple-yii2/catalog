<?php

namespace cms\catalog\models;

use Yii;
use yii\helpers\ArrayHelper;
use helpers\Translit;
use dkhlystov\db\ActiveRecord;
use dkhlystov\storage\components\StoredInterface;
use cms\catalog\helpers\CurrencyHelper;

class Product extends ActiveRecord implements StoredInterface
{

    // Availability
    const INSTOCK = 0;
    const UNDERTHEORDER = 1;
    const NOTAVAILABLE = 2;

    /**
     * @var array
     */
    private static $availabilityNames = [
        self::INSTOCK => 'In stock',
        self::UNDERTHEORDER => 'Under the order',
        self::NOTAVAILABLE => 'Not available',
    ];

    /**
     * @var array Availability names with translation
     */
    private static $_availabilityNames;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_product';
    }

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        parent::__construct(array_replace([
            'active' => true,
            'imageCount' => 0,
            'currency_id' => CurrencyHelper::getApplicationCurrencyId(),
        ], $config));
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert) === false) {
            return false;
        }

        // Price value
        if ($this->isAttributeChanged('price') || $this->isAttributeChanged('currency_id')) {
            $value = $this->price;
            if ($this->currency !== null) {
                $value *= $this->currency->rate;
            }

            $this->priceValue = $value;
        }

        // Modify date
        $this->modifyDate = gmdate('Y-m-d H:i:s');

        return true;
    }

    /**
     * Availability values
     * @return array
     */
    public static function getAvailabilityValues()
    {
        return array_keys(self::$availabilityNames);
    }

    /**
     * Availability names with translation
     * @return array
     */
    public static function getAvailabilityNames()
    {
        if (self::$_availabilityNames !== null) {
            return self::$_availabilityNames;
        }

        $names = [];
        foreach (self::$availabilityNames as $key => $name) {
            $names[$key] = Yii::t('catalog', $name);
        }

        return self::$_availabilityNames = $names;
    }

    /**
     * Availability name for model
     * @return string
     */
    public function getAvailabilityName()
    {
        return ArrayHelper::getValue(self::getAvailabilityNames(), $this->availability, '');
    }

    /**
     * Category relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Currency relation
     * @return yii\db\ActiveQueryInterface;
     */
    public function getCurrency()
    {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

    /**
     * Barcodes relation
     * @return yii\db\ActiveQueryInterface
     */
    public function getBarcodes()
    {
        return $this->hasMany(ProductBarcode::className(), ['product_id' => 'id']);
    }

    /**
     * Properties relation
     * @return yii\db\ActiveQueryInterface
     */
    public function getProperties()
    {
        return $this->hasMany(ProductProperty::className(), ['product_id' => 'id']);
    }

    /**
     * Images relation
     * @return yii\db\ActiveQueryInterface
     */
    public function getImages()
    {
        return $this->hasMany(ProductImage::className(), ['product_id' => 'id']);
    }

    // /**
    //  * Recommended relation
    //  * @return yii\db\ActiveQueryInterface
    //  */
    // public function getRecommended()
    // {
    //     return $this->hasMany(Product::className(), ['id' => 'recommended_id'])->viaTable('catalog_product_recommended', ['product_id' => 'id']);
    // }

    /**
     * Stores quantity relation
     * @return yii\db\ActiveQueryInterface
     */
    public function getStores()
    {
        return $this->hasMany(StoreProduct::className(), ['product_id' => 'id']);
    }

    /**
     * Find by alias
     * @param sring $alias alias or id
     * @return static
     */
    public static function findByAlias($alias)
    {
        $model = static::findOne(['alias' => $alias]);
        if ($model === null) {
            $model = static::findOne(['id' => $alias]);
        }

        return $model;
    }

    // public static function search($text)
    // {
    //     $query = static::find();

    //     // Search by name and model
    //     $keywords = preg_split('/[\s,]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    //     foreach ($keywords as $word) {
    //         $query->andWhere(['or',
    //             ['like', 'name', $word],
    //             ['like', 'model', $word],
    //         ]);
    //     }

    //     // Search by SKU
    //     $query->orWhere(['like', 'sku', $text]);

    //     return $query;
    // }

    /**
     * Making alias from name and id
     * @return void
     */
    public function makeAlias()
    {
        $this->alias = Translit::t($this->name . '-' . $this->id);
    }

    // /**
    //  * Calc price to destination currency
    //  * @param Currency|null $destCurrency destination curency. Application currency used if not set.
    //  * @return float
    //  */
    // public function calcPrice($destCurrency = null)
    // {
    //     return CurrencyHelper::calc($this->price, $this->currency, $destCurrency);
    // }

    /**
     * Update product quantity with availability correction
     * @param integer $value 
     * @return boolean
     */
    public function updateQuantity($value)
    {
        $this->quantity = $value;
        $this->availability = $value ? self::INSTOCK : self::NOTAVAILABLE;
        return $this->update(false, ['quantity', 'availability']);
    }

    /**
     * Parsing html for files in <img> and <a>.
     * @param string $content 
     * @return string[]
     */
    protected function getFilesFromContent($content)
    {
        if (preg_match_all('/(?:src|href)="([^"]+)"/i', $content, $matches)) {
            return $matches[1];
        }

        return [];      
    }

    /**
     * @inheritdoc
     */
    public function getOldFiles()
    {
        return $this->getFilesFromContent($this->getOldAttribute('description'));
    }

    /**
     * @inheritdoc
     */
    public function getFiles()
    {
        return $this->getFilesFromContent($this->description);
    }

    /**
     * @inheritdoc
     */
    public function setFiles($files)
    {
        $description = $this->description;
        foreach ($files as $from => $to) {
            $description = str_replace($from, $to, $description);
        }

        $this->description = $description;
    }

}
