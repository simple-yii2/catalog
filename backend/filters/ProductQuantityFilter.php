<?php

namespace cms\catalog\backend\filters;

use Yii;
use yii\data\ActiveDataProvider;
use cms\catalog\common\models\Product;

class ProductQuantityFilter extends Product
{

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sku' => Yii::t('catalog', 'SKU'),
            'name' => Yii::t('catalog', 'Name'),
            'price' => Yii::t('catalog', 'Price'),
            'quantity' => Yii::t('catalog', 'Quantity'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sku', 'name'], 'string'],
        ];
    }

    /**
     * Search function
     * @param array|null $config Data provider config
     * @return ActiveDataProvider
     */
    public function getDataProvider($config = [])
    {
        $query = self::find();
        $query->andFilterWhere(['sku' => $this->sku]);
        $query->andFilterWhere(['or',
            ['like', 'name', $this->name],
            ['like', 'model', $this->name],
        ]);

        $query->orderBy(['category_lft' => SORT_ASC]);

        $config['query'] = $query;
        return new ActiveDataProvider($config);
    }

}
