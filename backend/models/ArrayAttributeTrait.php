<?php

namespace cms\catalog\backend\models;

use yii\helpers\ArrayHelper;

trait ArrayAttributeTrait
{

	/**
	 * Sets array attribute to form when every item is the form too
	 * @param string $attribute Attribute name
	 * @param string $objectClass Bussiness logic object class
	 * @param string $modelClass Form model class
	 * @param array $value Value from relation or post
	 * @param array||null $templates Templates of items in attribute
	 * @return void
	 */
	protected function SetArrayAttribute($attribute, $objectClass, $modelClass, $value)
	{
		//make array of old values, every value if form model
		$old = [];
		foreach ($this->$attribute as $model) {
			if ($id = $model->getObject()->id)
				$old[$id] = $model;
		}

		//default
		$this->$attribute = [];

		//if not array
		if (!is_array($value))
			return;

		//set array values
		foreach ($value as $item) {
			//determine object, id and attributes
			if (is_array($item)) {
				$object = null;
				$id = ArrayHelper::getValue($item, 'id');
				$attributes = $item;
			} elseif (get_class($item) == $objectClass) {
				$object = $item;
				$id = $item->id;
				$attributes = $item->getAttributes();
			} else {
				continue;
			}

			//determine model
			$model = array_key_exists($id, $old) ? $old[$id] : new $modelClass($object);

			//attributes
			$model->setAttributes($attributes);

			//set array item
			$this->{$attribute}[] = $model;
		}
	}

	protected function SetArrayAttributeWithTemplate($attribute, $objectClass, $modelClass, $value, $templates, $key)
	{
		//check value and make $id=>$item array
		//make shure that keys of $value array corresponds $key when render form
		$items = [];
		if (is_array($value)) {
			foreach ($value as $id => $item) {
				if (is_array($item)) {
					$items[$id] = $item;
				} elseif (get_class($item) == $objectClass) {
					$items[$item->$key] = $item;
				}
			}
		}

		//old items
		$old = [];
		foreach ($this->$attribute as $model) {
			if ($id = $model->getTemplate()->id)
				$old[$id] = $model;
		}

		//default
		$this->$attribute = [];

		if (empty($templates))
			return;

		//assign items
		foreach ($templates as $template) {
			$id = $template->id;

			//determine object and attributes
			$object = null;
			$attributes = [];

			$item = ArrayHelper::getValue($items, $id);
			if (is_array($item)) {
				$attributes = $item;
			} elseif (get_class($item) == $objectClass) {
				$object = $item;
				$attributes = $item->getAttributes();
			}

			//determine model
			$model = array_key_exists($id, $old) ? $old[$id] : new $modelClass($template, $object);

			//attributes
			$model->setAttributes($attributes);

			//set array item
			$this->{$attribute}[] = $model;
		}
	}

}
