<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use cms\catalog\common\models\Product;
use cms\catalog\common\models\Store;
use cms\catalog\common\models\StoreProduct;

/**
 * Editing form
 */
class ProductQuantityForm extends Model
{

    /**
     * @var array
     */
    private $_stores = [];

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
            $object = new Product;
        }

        $this->_object = $object;

        //attributes
        parent::__construct(array_replace([
            'stores' => $object->stores,
        ], $config));
    }

    /**
     * Object getter
     * @return Currency
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * Stores getter
     * @return array
     */
    public function getStores()
    {
        return $this->_stores;
    }

    /**
     * Stores setter
     * @param array $value 
     * @return void
     */
    public function setStores($value)
    {
        //tempate
        $stores = [];
        foreach (Store::find()->all() as $item) {
            $stores[$item->id] = 0;
        }

        //quantity
        $a = [];
        foreach ($value as $k => $v) {
            if ($v instanceof StoreProduct) {
                if (array_key_exists($v->store_id, $stores)) {
                    $stores[$v->store_id] = $v->quantity;
                }
            } else {
                if (array_key_exists($k, $stores)) {
                    $stores[$k] = (int) $v;
                }
            }
        }

        $this->_stores = $stores;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['stores', 'safe'],
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

        $object = $this->_object;

        $success = false;
        $transaction = $object->db->beginTransaction();
        try {
            //old
            $old = [];
            foreach ($object->stores as $item) {
                $old[$item->store_id] = $item;
            }
            //insert, update
            foreach ($this->_stores as $key => $value) {
                if (array_key_exists($key, $old)) {
                    $item = $old[$key];
                    $item->quantity = $value;
                    $item->update(false, ['quantity']);
                    unset($old[$key]);
                } else {
                    $item = new StoreProduct(['product_id' => $object->id, 'store_id' => $key, 'quantity' => $value]);
                    $item->save(false);
                }
            }
            //delete
            foreach ($old as $item) {
                $item->delete();
            }
            //quantity
            $object->updateQuantity(array_sum($this->_stores));
            
            $transaction->commit();
            $success = true;
        } catch (\Exception $e) {
            $transaction->rollBack();
        }

        return $success;
    }

}
