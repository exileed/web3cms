<?php

class UserDetails extends CActiveRecord
{
    /**
     * The followings are the available columns in table 'UserDetails':
     * @var integer $userId
     * @var string $passwordHint
     * @var string $isEmailConfirmed
     * @var string $emailConfirmationKey
     * @var string $isEmailVisible
     * @var string $isScreenNameEditable
     * @var string $firstName
     * @var string $middleName
     * @var string $lastName
     * @var string $gender
     * @var string $birthDate
     * @var string $textStatus
     * @var string $lastLoginOn
     * @var string $lastLoginGmtOn
     * @var string $lastSeenOn
     * @var string $lastSeenGmtOn
     * @var integer $totalTimeLoggedIn
     * @var string $secretQuestion
     * @var string $secretAnswer
     * @var string $adminComment
     * @var string $oldPassword
     * @var string $updatedOn
     * @var string $updatedGmtOn
     */
    // userId is not in safeAttributes
    //public $userId;
    public $emailConfirmationKey;

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
        return 'UserDetails';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('emailConfirmationKey','length','max'=>32),
            array('firstName','length','max'=>128),
            array('middleName','length','max'=>128),
            array('lastName','length','max'=>128),
            array('secretAnswer','length','max'=>255),
            array('oldPassword','length','max'=>128),
            array('totalTimeLoggedIn', 'numerical', 'integerOnly'=>true),
        );
    }

    /**
     * @return array attributes that can be massively assigned
     * using something like $model->attributes=$_POST['Model'];
     */
    public function safeAttributes()
    {
        return array(
            'emailConfirmationKey',
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'userId'=>'User',
            'passwordHint'=>'Password Hint',
            'isEmailConfirmed'=>'Is Email Confirmed',
            'emailConfirmationKey'=>'Email Confirmation Key',
            'isEmailVisible'=>'Is Email Visible',
            'isScreenNameEditable'=>'Is Screen Name Editable',
            'firstName'=>'First Name',
            'middleName'=>'Middle Name',
            'lastName'=>'Last Name',
            'gender'=>'Gender',
            'birthDate'=>'Birth Date',
            'textStatus'=>'Text Status',
            'lastLoginOn'=>'Last Login On',
            'lastLoginGmtOn'=>'Last Login Gmt On',
            'lastSeenOn'=>'Last Seen On',
            'lastSeenGmtOn'=>'Last Seen Gmt On',
            'totalTimeLoggedIn'=>'Total Time Logged In',
            'secretQuestion'=>'Secret Question',
            'secretAnswer'=>'Secret Answer',
            'adminComment'=>'Admin Comment',
            'oldPassword'=>'Old Password',
            'updatedOn'=>'Updated On',
            'updatedGmtOn'=>'Updated Gmt On',
        );
    }
}