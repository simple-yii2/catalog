<?php

namespace cms\catalog\backend\filters;

use Yii;
use yii\data\ActiveDataProvider;
use cms\catalog\common\models\Vendor;

class VendorFilter extends Vendor
{

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'url' => Yii::t('catalog', 'Url'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string'],
        ];
    }

    /**
     * Search function
     * @param array $config Data provider config
     * @return ActiveDataProvider
     */
    public function getDataProvider($config = [])
    {
        $query = self::find();
        $query->andFilterWhere(['like', 'name', $this->name]);

        $config['query'] = $query;
        return new ActiveDataProvider($config);
    }

}
