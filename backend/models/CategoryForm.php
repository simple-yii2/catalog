<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use cms\catalog\common\models\Category;
use cms\catalog\common\models\Property;

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
	 * @var CategoryPropertyForm[] Properties
	 */
	private $_properties = [];

	/**
	 * @var Category
	 */
	private $_model;

	/**
	 * @inheritdoc
	 * @param Category $model 
	 */
	public function __construct(Category $model = null, $config = [])
	{
		if ($model === null)
			$model = new Category;

		$this->_model = $model;

		//attributes
		$this->active = $model->active == 0 ? '0' : '1';
		$this->title = $model->title;

		$this->properties = array_merge($model->getParentProperties(), $model->properties);

		parent::__construct($config);
	}

	/**
	 * Properies getter
	 * @return CategoryPropertyForm[]
	 */
	public function getProperties()
	{
		return $this->_properties;
	}

	/**
	 * Properies setter
	 * @param Property[]|array[] $value Properies
	 * @return void
	 */
	public function setProperties($value)
	{
		$old = [];
		foreach ($this->_properties as $item) {
			if ($id = $item->id)
				$old[$id] = $item;
		}

		$this->_properties = [];

		if (!is_array($value))
			return;

		foreach ($value as $item) {
			if ($item instanceof Property) {
				$model = $item;
				$id = $item->id;
				$attributes = $item->getAttributes();
			} else {
				$model = null;
				$id = ArrayHelper::getValue($item, 'id');
				$attributes = $item;
			}

			$form = array_key_exists($id, $old) ? $old[$id] : new PropertyForm($model);

			$form->setAttributes($attributes);
			$this->_properties[] = $form;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function attributes()
	{
		return array_merge(parent::attributes(), ['properties']);
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'active' => Yii::t('catalog', 'Active'),
			'title' => Yii::t('catalog', 'Title'),
			'properties' => Yii::t('catalog', 'Properties'),
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
			['properties', function($attribute, $params) {
				$hasError = false;
				foreach ($this->_properties as $model) {
					if (!$model->validate())
						$hasError = true;
				}

				if ($hasError)
					$this->addError($attribute . '[]', 'Properties validation error.');
			}],
		];
	}

	/**
	 * Model getter
	 * @return Category
	 */
	public function getModel()
	{
		return $this->_model;
	}

	/**
	 * Save
	 * @param Category|null $parent 
	 * @return boolean
	 */
	public function save(Category $parent = null)
	{
		if (!$this->validate())
			return false;

		$model = $this->_model;

		$model->active = $this->active == 1;
		$model->title = $this->title;

		if ($model->getIsNewRecord()) {
			if (!$model->appendTo($parent, false))
				return false;
		} else {
			if (!$model->save(false))
				return false;
		}

		if (empty($model->alias)) {
			$model->makeAlias();
			$model->update(false, ['alias']);
		}

		$model->updatePath($parent);

		//update relations
		$old = [];
		foreach ($model->properties as $item) {
			$old[$item->id] = $item;
		};
		//insert/update
		foreach ($this->_properties as $item) {
			$item->save($model, false);
			unset($old[$item->id]);
		}
		//delete
		foreach ($old as $item) {
			$item->delete();
		}

		return true;
	}

}
