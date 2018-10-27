<?php

namespace cms\catalog\backend;

use Yii;
use cms\components\BackendModule;
use cms\catalog\common\helpers\CurrencyHelper;
use cms\catalog\common\models\Category;
use cms\catalog\common\models\Delivery;

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
     * @var boolean
     */
    public $purchaseEnabled = true;

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

        //Categories root
        if (Category::find()->roots()->count() == 0) {
            $root = new Category(['title' => 'Root']);
            $root->makeRoot();
        }

        //Default delivery methods
        if (Delivery::find()->count() == 0) {
            $currency_id = CurrencyHelper::getApplicationCurrencyId();

            //pickup
            $item = new Delivery([
                'currency_id' => $currency_id,
                'name' => Yii::t('catalog', 'Pickup'),
                'price' => 0,
                'days' => 0,
                'availableFields' => ['store_id', 'comment'],
            ]);
            $item->save(false);

            //courier
            $item = new Delivery([
                'currency_id' => $currency_id,
                'name' => Yii::t('catalog', 'Courier'),
                'price' => 0,
                'days' => 1,
                'availableFields' => ['street', 'house', 'apartment', 'entrance', 'entryphone', 'floor', 'recipient', 'phone', 'comment'],
            ]);
            $item->save(false);

            //delivery service
            $item = new Delivery([
                'currency_id' => $currency_id,
                'name' => Yii::t('catalog', 'Delivery service'),
                'price' => 0,
                'days' => 1,
                'availableFields' => ['serviceName', 'city', 'street', 'house', 'apartment', 'recipient', 'phone', 'comment'],
            ]);
            $item->save(false);
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
        $items[] = ['label' => Yii::t('catalog', 'Categories'), 'url' => ['/catalog/category/index']];
        $items[] = ['label' => Yii::t('catalog', 'Products'), 'url' => ['/catalog/product/index']];
        if ($this->storeEnabled) {
            $items[] = ['label' => Yii::t('catalog', 'Stores'), 'url' => ['/catalog/store/index']];
            $items[] = ['label' => Yii::t('catalog', 'Product quantity'), 'url' => ['/catalog/quantity/index']];
        }
        if ($this->purchaseEnabled) {
            $items[] = ['label' => Yii::t('catalog', 'Delivery methods'), 'url' => ['/catalog/delivery/index']];
        }

        return [
            'label' => Yii::t('catalog', 'Catalog'),
            'items' => $items,
        ];
    }

}
