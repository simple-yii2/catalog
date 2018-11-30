<?php

namespace cms\catalog\backend\forms;

use Yii;
use yii\helpers\ArrayHelper;
use dkhlystov\forms\Form;
use cms\user\common\models\User;

class OrderCustomerForm extends Form
{

    /**
     * @var integer
     */
    public $user_id;

    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $phone;

    /**
     * @var string
     */
    public $email;

    /**
     * @inheritdoc
     */
    public function assign($object)
    {
        $this->user_id = $object->user_id;
        $this->user = ArrayHelper::getValue($object->user, 'username', '');
        $this->name = $object->name;
        $this->phone = $object->phone;
        $this->email = $object->email;
    }

    /**
     * @inheritdoc
     */
    public function assignTo($object)
    {
        $user = User::findOne($this->user_id);

        $object->user_id = $user === null ? null : (integer) $user->id;
        $object->name = $this->name;
        $object->phone = $this->phone;
        $object->email = $this->email;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user' => Yii::t('catalog', 'Customer'),
            'name' => Yii::t('catalog', 'Name'),
            'phone' => Yii::t('catalog', 'Phone'),
            'email' => Yii::t('catalog', 'E-mail'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['user_id', 'integer'],
            [['name', 'email'], 'string', 'max' => 100],
            ['phone', 'string', 'max' => 20],
            ['email', 'email'],
            ['phone', 'match', 'pattern' => '/\+1\-\d{3}\-\d{3}\-\d{4}/'],
            [['name', 'phone'], 'required'],
        ]);
    }

}
