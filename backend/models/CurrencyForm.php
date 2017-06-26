<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Currency;

/**
 * Editing form
 */
class CurrencyForm extends Model
{

	/**
	 * @var string Code
	 */
	public $code;

	/**
	 * @var float Rate
	 */
	public $rate;

	/**
	 * @var Currency
	 */
	private $_model;

	/**
	 * @inheritdoc
	 * @param Currency|null $model 
	 */
	public function __construct(Currency $model = null, $config = [])
	{
		if ($model === null)
			$model = new Currency;

		$this->_model = $model;

		//attributes
		$this->code = $model->code;
		$this->rate = $model->rate;

		parent::__construct($config);
	}

	/**
	 * Model getter
	 * @return Currency
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
			'code' => Yii::t('catalog', 'Code'),
			'rate' => Yii::t('catalog', 'Rate'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['code', 'string', 'max' => 10],
			['rate', 'double', 'min' => 0.01],
			[['code', 'rate'], 'required'],
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

		$model->code = $this->code;
		$model->rate = empty($this->rate) ? null : (float) $this->rate;

		if (!$model->save(false))
			return false;

		return true;
	}

}
