<?php

class User extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'User':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $email
	 * @var string $displayName
	 * @var string $language
	 * @var string $theme
	 * @var string $accessType
	 * @var integer $accessLevel
	 * @var string $isActive
	 * @var string $createdOn
	 * @var string $createdGmtOn
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'User';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('username','length','max'=>128),
			array('password','length','max'=>128),
			array('email','length','max'=>255),
			array('displayName','length','max'=>128),
			array('language','length','max'=>16),
			array('theme','length','max'=>32),
			array('accessType','length','max'=>32),
			array('accessLevel', 'numerical', 'integerOnly'=>true),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'details' => array(self::HAS_ONE, 'UserDetails', 'userId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>'Id',
			'username'=>'Username',
			'password'=>'Password',
			'email'=>'Email',
			'displayName'=>'Display Name',
			'language'=>'Language',
			'theme'=>'Theme',
			'accessType'=>'Access Type',
			'accessLevel'=>'Access Level',
			'isActive'=>'Is Active',
			'createdOn'=>'Created On',
			'createdGmtOn'=>'Created Gmt On',
		);
	}
}