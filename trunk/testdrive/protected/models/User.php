<?php

/**
 * This is the model class for table "{{user}}".
 */
class User extends CActiveRecord
{
	/**
	 * The followings are the available columns in table '{{user}}':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $email
	 * @var integer $isAdmin
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return '{{user}}';
	}

    /**
     * Checks if the given password is correct.
     * @param string the password to be validated
     * @return boolean whether the password is valid
     */
    public function validatePassword($password)
    {
        return $this->hashPassword($password) === $this->password;
    }

    /**
     * Generates the password hash.
     * @param string password
     * @return string hash
     */
    public function hashPassword($password)
    {
        return md5($password);
    }
}