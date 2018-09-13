<?php

namespace cms\catalog\common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class ProductProperty extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_product_property';
    }

    /**
     * Category property relation
     * @return ActiveQueryInterface
     */
    public function getCategoryProperty()
    {
        return $this->hasOne(CategoryProperty::className(), ['id' => 'property_id']);
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new ProductPropertyQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->numericValue = (float) $this->value;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->categoryProperty->type == CategoryProperty::TYPE_MULTIPLE) {
            ProductProperty::deleteAll(['product_id' => $this->product_id, 'property_id' => $this->property_id]);

            $values = $this->value;
            if (!is_array($values)) {
                $values = [$values];
            }

            $b = true;
            foreach ($values as $value) {
                $item = new ProductProperty([
                    'product_id' => $this->product_id,
                    'property_id' => $this->property_id,
                    'value' => $value,
                ]);
                if ($item->insert($runValidation) !== false) {
                    $b = false;
                }
                unset($item);
            }

            return $b;
        } else {
            return parent::save($runValidation, $attributeNames);
        }
    }

}

class ProductPropertyQuery extends ActiveQuery
{

    /**
     * @inheritdoc
     */
    public function findFor($name, $model)
    {
        $items = [];
        foreach (parent::findFor($name, $model) as $item) {
            $id = $item->property_id;
            if (array_key_exists($id, $items)) {
                $v = $items[$id]->value;
                if (!is_array($v)) {
                    $v = [$v];
                }
                $v[] = $item->value;
                $items[$id]->value = $v;
            } else {
                $items[$id] = $item;
            }
        }

        return array_values($items);
    }

}
