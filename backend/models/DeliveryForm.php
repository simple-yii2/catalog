<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Delivery;

/**
 * Editing form
 */
class DeliveryForm extends Model
{

	/**
	 * @var string Name
	 */
	public $name;

	/**
	 * @var float Cost
	 */
	public $cost;

	/**
	 * @var integer Day count
	 */
	public $days;

	/**
	 * @var Delivery
	 */
	private $_model;

	/**
	 * @inheritdoc
	 * @param Delivery|null $model 
	 */
	public function __construct(Delivery $model = null, $config = [])
	{
		if ($model === null)
			$model = new Delivery;

		$this->_model = $model;

		//attributes
		$this->name = $model->name;
		$this->cost = $model->cost;
		$this->days = $model->days;

		parent::__construct($config);
	}

	/**
	 * Model getter
	 * @return Delivery
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
			'name' => Yii::t('catalog', 'Name'),
			'cost' => Yii::t('catalog', 'Cost'),
			'days' => Yii::t('catalog', 'Days'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['name', 'string', 'max' => 100],
			['cost', 'double', 'min' => 0],
			['days', 'integer', 'min' => 0],
			[['name', 'cost', 'days'], 'required'],
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

		$model->name = $this->name;
		$model->cost = (float) $this->cost;
		$model->days = (integer) $this->days;

		if (!$model->save(false))
			return false;

		return true;
	}

}
