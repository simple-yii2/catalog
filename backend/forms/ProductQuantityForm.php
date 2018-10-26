<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use cms\catalog\common\models\Product;
use cms\catalog\common\models\Store;
use cms\catalog\common\models\StoreProduct;

class ProductQuantityForm extends Model
{

    /**
     * @var array
     */
    private $_quantity = [];

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

        //stores
        $this->_stores = [];
        foreach (Store::find()->all() as $item) {
            $this->_stores[$item->id] = $item;
            $this->_quantity[$item->id] = 0;
        }

        //quantity
        foreach ($object->stores as $item) {
            if (array_key_exists($item->store_id, $this->_quantity)) {
                $this->_quantity[$item->store_id] = $item->quantity;
            }
        }

        //attributes
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (!preg_match('/quantity(\d+)/', $name, $m)) {
            return parent::__get($name);
        }

        return ArrayHelper::getValue($this->_quantity, $m[1]);
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (!preg_match('/quantity(\d+)/', $name, $m)) {
            return parent::__get($name);
        }

        $key = $m[1];
        if (array_key_exists($key, $this->_quantity)) {
            $this->_quantity[$key] = $value;
        }
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
     * @inheritdoc
     */
    public function attributes()
    {
        return array_map(function($v) {return 'quantity' . $v->id;}, $this->_stores);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $result = [];
        foreach ($this->_stores as $item) {
            $result['quantity' . $item->id] = $item->name;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $attributes = [];
        foreach ($this->_stores as $item) {
            $attributes[] = 'quantity' . $item->id;
        }
        return [
            [$attributes, 'integer', 'min' => 0],
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
            foreach ($this->_quantity as $key => $value) {
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
            $object->updateQuantity(array_sum($this->_quantity));
            
            $transaction->commit();
            $success = true;
        } catch (\Exception $e) {
            $transaction->rollBack();
        }

        return $success;
    }

}
