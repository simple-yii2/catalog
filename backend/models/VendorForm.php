<?php

namespace cms\catalog\backend\models;

use Yii;
use yii\base\Model;

use cms\catalog\common\models\Vendor;

/**
 * Editing form
 */
class VendorForm extends Model
{

	/**
	 * @var string Name
	 */
	public $name;

	/**
	 * @var string Description
	 */
	public $description;

	/**
	 * @var string Url
	 */
	public $url;

	/**
	 * @var string Image
	 */
	public $file;

	/**
	 * @var string Thumb
	 */
	public $thumb;

	/**
	 * @var Vendor
	 */
	private $_model;

	/**
	 * @inheritdoc
	 * @param Vendor|null $model 
	 */
	public function __construct(Vendor $model = null, $config = [])
	{
		if ($model === null)
			$model = new Vendor;

		$this->_model = $model;

		//attributes
		$this->name = $model->name;
		$this->description = $model->description;
		$this->url = $model->url;
		$this->file = $model->image;
		$this->thumb = $model->image;

		//file caching
		Yii::$app->storage->cacheObject($model);

		parent::__construct($config);
	}

	/**
	 * Model getter
	 * @return Vendor
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
			'description' => Yii::t('catalog', 'Description'),
			'url' => Yii::t('catalog', 'Url'),
			'file' => Yii::t('catalog', 'Image'),
		];
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['name', 'string', 'max' => 100],
			['description', 'string', 'max' => 3000],
			['url', 'string', 'max' => 200],
			[['file', 'thumb'], 'string'],
			['name', 'required'],
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
		$model->description = $this->description;
		$model->url = $this->url;
		$model->image = $this->thumb;

		//files
		Yii::$app->storage->storeObject($model);

		if (!$model->save(false))
			return false;

		return true;
	}

}
