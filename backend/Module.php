<?php

namespace cms\catalog\backend;

use Yii;
use cms\components\BackendModule;
use cms\catalog\common\models\Category;

class Module extends BackendModule
{

    /**
     * @var integer;
     */
    public $vendorThumbWidth = 100;

    /**
     * @var integer;
     */
    public $vendorThumbHeight = 100;

    /**
     * @var integer;
     */
    public $productThumbWidth = 300;

    /**
     * @var integer;
     */
    public $productThumbHeight = 300;

    /**
     * @var boolean
     */
    public $vendorEnabled = true;

    /**
     * @var boolean
     */
    public $barcodeEnabled = true;

    /**
     * @var boolean
     */
    public $propertiesEnabled = true;

    /**
     * @var boolean
     */
    public $storeEnabled = true;

    /**
     * @var integer|null
     */
    public $maxCategoryDepth = null;

    /**
     * @inheritdoc
     */
    protected static function cmsDatabase()
    {
        parent::cmsDatabase();

        if (Category::find()->roots()->count() == 0) {
            $root = new Category(['title' => 'Root']);
            $root->makeRoot();
        }
    }

    /**
     * @inheritdoc
     */
    protected static function cmsSecurity()
    {
        parent::cmsSecurity();

        $auth = Yii::$app->getAuthManager();
        if ($auth->getRole('Catalog') === null) {
            //role
            $role = $auth->createRole('Catalog');
            $auth->add($role);
        }
    }

    /**
     * @inheritdoc
     */
    public function cmsMenu()
    {
        if (!Yii::$app->getUser()->can('Catalog')) {
            return [];
        }

        $items = [];
        $items[] = ['label' => Yii::t('catalog', 'Currencies'), 'url' => ['/catalog/currency/index']];
        if ($this->vendorEnabled) {
            $items[] = ['label' => Yii::t('catalog', 'Vendors'), 'url' => ['/catalog/vendor/index']];
        }
        if ($this->storeEnabled) {
            $items[] = ['label' => Yii::t('catalog', 'Stores'), 'url' => ['/catalog/store/index']];
        }

        $items[] = ['label' => Yii::t('catalog', 'Categories'), 'url' => ['/catalog/category/index']];
        $items[] = ['label' => Yii::t('catalog', 'Products/services'), 'url' => ['/catalog/product/index']];

        return [
            'label' => Yii::t('catalog', 'Catalog'),
            'items' => $items,
        ];
    }

}
