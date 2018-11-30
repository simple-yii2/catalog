<?php

namespace cms\catalog\backend\filters;

use Yii;
use yii\data\ActiveDataProvider;
use cms\catalog\models\Store;

class StoreFilter extends Store
{

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('catalog', 'Type'),
            'name' => Yii::t('catalog', 'Name'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['type', 'in', 'range' => self::getTypes()],
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
        $query->andFilterWhere(['=', 'type', $this->type]);
        $query->andFilterWhere(['like', 'name', $this->name]);

        $config['query'] = $query;
        return new ActiveDataProvider($config);
    }

}
