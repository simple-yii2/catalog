<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Category;
use cms\catalog\common\models\Goods;

/**
 * Editing form
 */
class GoodsForm extends Model
{

	/**
	 * @var integer Category id
	 */
	public $category_id;

	/**
	 * @var boolean Active
	 */
	public $active;

	/**
	 * @var string Title
	 */
	public $title;

	/**
	 * @var string Description
	 */
	public $description;

	/**
	 * @var Goods
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param Goods|null $object 
	 */
	public function __construct(Goods $object = null, $config = [])
	{
		if ($object === null)
			$object = new Goods;

		$this->_object = $object;

		//attributes
		$this->category_id = $object->category_id;
		$this->active = $object->active == 0 ? '0' : '1';
		$this->title = $object->title;
		$this->description = $object->description;

		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'category_id' => Yii::t('catalog', 'Category'),
			'active' => Yii::t('catalog', 'Active'),
			'title' => Yii::t('catalog', 'Title'),
			'description' => Yii::t('catalog', 'Description'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['category_id', 'integer'],
			['active', 'boolean'],
			['title', 'string', 'max' => 100],
			['description', 'string', 'max' => 1000],
			[['category_id', 'title'], 'required'],
		];
	}

	/**
	 * Saving object using model attributes
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$category = Category::findOne($this->category_id);
		if ($category === null)
			return false;

		$object = $this->_object;

		$object->category_id = $category->id;
		$object->category_lft = $category->lft;
		$object->category_rgt = $category->rgt;
		$object->active = $this->active == 1;
		$object->title = $this->title;
		$object->description = $this->description;

		if (!$object->save(false))
			return false;

		if ($object->alias === null) {
			$object->makeAlias();
			$object->update(false, ['alias']);
		}

		return true;
	}

}
