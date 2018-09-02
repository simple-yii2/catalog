<?php

namespace cms\catalog\common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use helpers\Translit;
use creocoder\nestedsets\NestedSetsBehavior;
use creocoder\nestedsets\NestedSetsQueryBehavior;

class Category extends ActiveRecord
{

    const ALIAS_SEPARATOR = '/';
    const PATH_SEPARATOR = ' » ';

    /**
     * @var Category[]
     */
    private $_parents;

    /**
     * @var array
     */
    private $_activeChildren;

    /**
     * @var integer
     */
    private $_totalProductCount;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_category';
    }

    /**
     * @inheritdoc
     * Default values
     */
    public function __construct($config = [])
    {
        parent::__construct(array_replace([
            'active' => true,
            'productCount' => 0,
        ], $config));
    }

    /**
     * Properties relation
     * @return ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(CategoryProperty::className(), ['category_id' => 'id']);
    }

    /**
     * Products relation
     * @return ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id'])->inverseOf('category');
    }

    /**
     * Parents getter
     * @return Category[]
     */
    public function getParents()
    {
        if ($this->_parents !== null)
            return $this->_parents;

        return $this->_parents = $this->parents()->all();
    }

    /**
     * Parent properties
     * @return CategoryProperty[]
     */
    public function getParentProperties()
    {
        $items = [];
        foreach ($this->getParents() as $parent) {
            $items = array_merge($items, $parent->properties);
        }

        array_walk($items, function(&$item) {
            $item->readOnly = true;
        });

        return $items;
    }

    /**
     * Find by alias
     * @param sring $alias Alias or id
     * @return static
     */
    public static function findByAlias($alias) {
        $model = static::findOne(['alias' => $alias]);
        if ($model === null)
            $model = static::findOne(['id' => $alias]);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    /**
     * Making alias and path from title and parent alias and path
     * @param Category|null $parent 
     * @return void
     */
    public function makeAliasAndPath(Category $parent = null)
    {
        if ($this->isRoot()) {
            $this->alias = null;
            $this->path = null;
            return;
        }

        if ($parent === null)
            $parent = $this->parents(1)->one();

        $alias = '';
        $path = '';

        if ($parent !== null && !$parent->isRoot()) {
            $alias = $parent->alias;
            $path = $parent->path;
        }

        if (!empty($alias))
            $alias .= self::ALIAS_SEPARATOR;
        if (!empty($path))
            $path .= self::PATH_SEPARATOR;

        $this->alias = $alias . Translit::t($this->title);
        $this->path = $path . $this->title;
    }

    /**
     * Update alias and path with children
     * @param Category|null $parent 
     * @return void
     */
    public function updateAliasAndPath(Category $parent = null)
    {
        $this->makeAliasAndPath($parent);
        $this->update(false, ['alias', 'path']);

        if ($this->isLeaf())
            return;

        foreach ($this->children(1)->all() as $object)
            $object->updateAliasAndPath($this);
    }

    /**
     * Update product count
     * @return void
     */
    public function updateProductCount()
    {
        $count = $this->getProducts()->count();
        if ($count != $this->productCount) {
            $this->productCount = $count;
            $this->update(false, ['productCount']);
        }
    }

    /**
     * Active sub categories with products
     * @return array
     */
    public function getActiveChildren()
    {
        if ($this->_activeChildren !== null) {
            return $this->_activeChildren;
        }

        $children = [];
        $item = null;
        $skip = 0;
        foreach ($this->children()->all() as $value) {
            if ($value->depth == $this->depth + 1) {
                if ($item !== null && $item->active && $item->productCount > 0) {
                    $children[] = $item;
                }
                $item = $value;
            }
            if (!$value->active) {
                $skip = $value->rgt;
            }
            if ($value->rgt <= $skip) {
                continue;
            }
            $item->productCount += $value->productCount;
        }
        if ($item !== null && $item->active && $item->productCount > 0) {
            $children[] = $item;
        }

        return $this->_activeChildren = $children;
    }

}

class CategoryQuery extends ActiveQuery
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }

}
