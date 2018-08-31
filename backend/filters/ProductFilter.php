<?php

namespace cms\catalog\backend\filters;

use Yii;
use yii\data\ActiveDataProvider;
use cms\catalog\common\models\Product;

/**
 * Search model
 */
class ProductFilter extends Product {

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category_id' => Yii::t('catalog', 'Category'),
            'name' => Yii::t('catalog', 'Name'),
            'price' => Yii::t('catalog', 'Price'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['category_id', 'integer'],
            ['name', 'string'],
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
        $query->andFilterWhere(['category_id' => $this->category_id]);
        $query->andFilterWhere(['or',
            ['like', 'name', $this->name],
            ['like', 'model', $this->name],
        ]);

        $query->orderBy(['category_lft' => SORT_ASC]);

        $config['query'] = $query;
        return new ActiveDataProvider($config);
    }

}
