<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

class CategoryForm extends Model
{

	/**
	 * @var boolean Active
	 */
	public $active;

	/**
	 * @var string Title
	 */
	public $title;

	/**
	 * @var cms\catalog\common\models\Category
	 */
	private $_object;

	/**
	 * @inheritdoc
	 * @param cms\catalog\common\models\Category $object 
	 */
	public function __construct(\cms\catalog\common\models\Category $object, $config = [])
	{
		$this->_object = $object;

		//attributes
		$this->active = $object->active == 0 ? '0' : '1';
		$this->title = $object->title;

		parent::__construct($config);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'active' => Yii::t('catalog', 'Active'),
			'title' => Yii::t('catalog', 'Title'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['active', 'boolean'],
			['title', 'string', 'max' => 100],
			['title', 'required'],
		];
	}

	/**
	 * Object id getter
	 * @return integer
	 */
	public function getObjectId()
	{
		return $this->_object->id;
	}

	/**
	 * Object title getter
	 * @return integer
	 */
	public function getObjectTitle()
	{
		return $this->_object->title;
	}

	/**
	 * Save object using model attributes
	 * @param cms\catalog\common\models\Category|null $object 
	 * @return boolean
	 */
	public function save(\cms\catalog\common\models\Category $parent = null)
	{
		if (!$this->validate())
			return false;

		$object = $this->_object;

		$object->active = $this->active == 1;
		$object->title = $this->title;

		if ($object->getIsNewRecord()) {
			if (!$object->appendTo($parent, false))
				return false;
		} else {
			if (!$object->save(false))
				return false;
		}

		return true;
	}

}
