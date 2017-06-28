<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Offer;
use cms\catalog\common\models\OfferImage;

class OfferImageForm extends Model
{

	/**
	 * @var string
	 */
	public $file;

	/**
	 * @var string
	 */
	public $thumb;

	/**
	 * @var OfferImage
	 */
	private $_model;

	/**
	 * @inheritdoc
	 * @param OfferImage|null $model 
	 */
	public function __construct(OfferImage $model = null, $config = [])
	{
		if ($model === null)
			$model = new OfferImage;

		$this->_model = $model;

		//attributes
		$this->file = $model->file;
		$this->thumb = $model->thumb;

		Yii::$app->storage->cacheObject($model);

		parent::__construct($config);
	}

	/**
	 * Id getter
	 * @return integer
	 */
	public function getId()
	{
		return $this->_model->id;
	}

	/**
	 * Model getter
	 * @return OfferImage
	 */
	public function getModel()
	{
		return $this->_model;
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['file', 'thumb'], 'string', 'max' => 200],
		];
	}

	/**
	 * Save
	 * @param Offer $offer 
	 * @param boolean $runValidation 
	 * @return boolean
	 */
	public function save(Offer $offer, $runValidation = true)
	{
		if ($runValidation && !$this->validate())
			return false;

		$model = $this->_model;

		$model->offer_id = $offer->id;
		$model->file = $this->file;
		$model->thumb = $this->thumb;

		Yii::$app->storage->storeObject($model);

		if (!$model->save(false))
			return false;

		return true;
	}

}
