<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;
use cms\catalog\common\models\Product;
use cms\catalog\common\models\ProductBarcode;

/**
 * Product barcode form
 */
class ProductBarcodeForm extends Model
{

    /**
     * @var string Barcode
     */
    public $barcode;

    /**
     * @var ProductBarcode
     */
    private $_object;

    /**
     * @inheritdoc
     * @param ProductBarcode|null $object 
     */
    public function __construct(ProductBarcode $object = null, $config = [])
    {
        if ($object === null) {
            $object = new ProductBarcode;
        }

        $this->_object = $object;

        //attributes
        parent::__construct(array_replace([
            'barcode' => $object->barcode,
        ], $config));
    }

    /**
     * Object getter
     * @return ProductBarcode
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'barcode' => Yii::t('catalog', 'Value'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['barcode', 'string', 'max' => 50],
            ['barcode', 'required'],
        ];
    }

    /**
     * Save
     * @param Product $product 
     * @param boolean $runValidation 
     * @return boolean
     */
    public function save(Product $product, $runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            return false;
        }

        $object = $this->_object;

        $object->product_id = $product->id;
        $object->barcode = $this->barcode;

        if (!$object->save(false)) {
            return false;
        }

        return true;
    }

}
