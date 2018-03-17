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
    public $offerThumbWidth = 360;

    /**
     * @var integer;
     */
    public $offerThumbHeight = 270;

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
    public static function moduleName()
    {
        return 'catalog';
    }

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
    public function cmsMenu($base)
    {
        if (!Yii::$app->getUser()->can('Catalog')) {
            return [];
        }

        $items = [];
        $items[] = ['label' => Yii::t('catalog', 'Settings'), 'url' => ["$base/catalog/settings/index"]];
        $items[] = ['label' => Yii::t('catalog', 'Currencies'), 'url' => ["$base/catalog/currency/index"]];
        if ($this->vendorEnabled) {
            $items[] = ['label' => Yii::t('catalog', 'Vendors'), 'url' => ["$base/catalog/vendor/index"]];
        }
        if ($this->storeEnabled) {
            $items[] = ['label' => Yii::t('catalog', 'Stores'), 'url' => ["$base/catalog/store/index"]];
        }
        $items[] = '<li role="separator" class="divider"></li>';
        $items[] = ['label' => Yii::t('catalog', 'Categories'), 'url' => ["$base/catalog/category/index"]];
        $items[] = ['label' => Yii::t('catalog', 'Goods/Services'), 'url' => ["$base/catalog/offer/index"]];

        return [
            ['label' => Yii::t('catalog', 'Catalog'), 'items' => $items],
        ];
    }

}
