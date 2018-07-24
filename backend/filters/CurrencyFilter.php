<?php

namespace cms\catalog\backend\filters;

use Yii;
use yii\data\ActiveDataProvider;
use cms\catalog\common\models\Currency;

/**
 * Search model
 */
class CurrencyFilter extends Currency
{

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('catalog', 'Name'),
            'code' => Yii::t('catalog', 'Code'),
            'rate' => Yii::t('catalog', 'Rate'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['code', 'string'],
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
        $query->andFilterWhere(['like', 'code', $this->code]);

        $config['query'] = $query;
        return new ActiveDataProvider($config);
    }

}
