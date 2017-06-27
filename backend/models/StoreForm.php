<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Store;

/**
 * Editing form
 */
class StoreForm extends Model
{

	/**
	 * @var integer Type
	 */
	public $type;

	/**
	 * @var float Name
	 */
	public $name;

	/**
	 * @var Store
	 */
	private $_model;

	/**
	 * @inheritdoc
	 * @param Store|null $model 
	 */
	public function __construct(Store $model = null, $config = [])
	{
		if ($model === null)
			$model = new Store;

		$this->_model = $model;

		//attributes
		$this->type = $model->type;
		$this->name = $model->name;

		parent::__construct($config);
	}

	/**
	 * Model getter
	 * @return Store
	 */
	public function getModel()
	{
		return $this->_model;
	}

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
			['type', 'in', 'range' => Store::getTypes()],
			['name', 'string', 'max' => 100],
			[['type', 'name'], 'required'],
		];
	}

	/**
	 * Saving model using model attributes
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$model = $this->_model;

		$model->type = (integer) $this->type;
		$model->name = $this->name;

		if (!$model->save(false))
			return false;

		return true;
	}

}
