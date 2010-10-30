<?php

class UserDetails extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'w3_user_details':
     * @var integer $userId
     * @var string $passwordHint
     * @var string $isEmailConfirmed
     * @var string $emailConfirmationKey
     * @var string $isEmailVisible
     * @var string $isScreenNameEditable
     * @var integer $deactivationTime
     * @var string $firstName
     * @var string $middleName
     * @var string $lastName
     * @var string $initials
     * @var string $occupation
     * @var string $gender
     * @var string $birthDate
     * @var string $textStatus
     * @var integer $lastLoginTime
     * @var integer $lastVisitTime
     * @var integer $totalTimeLoggedIn
     * @var string $secretQuestion
     * @var string $secretAnswer
     * @var string $administratorNote
     * @var integer $updateTime
     */

    const EMAIL_IS_CONFIRMED='1';
    const EMAIL_IS_NOT_CONFIRMED='0';
    const EMAIL_IS_VISIBLE='1';
    const EMAIL_IS_NOT_VISIBLE='0';

    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name (without prefix)
     */
    protected function _tableName()
    {
        return 'user_details';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $retval=array();
        if(Yii::app()->user->checkAccess(User::ADMINISTRATOR))
        {
            // administratorNote is safe
            $retval[]=array('administratorNote', 'safe', 'on'=>'not sure');
            // deactivationTime has to be integer
            $retval[]=array('deactivationTime', 'numerical', 'integerOnly'=>true, 'on'=>'not sure');
            // deactivationTime has to be 10 characters length max
            $retval[]=array('deactivationTime', 'length', 'max'=>10, 'on'=>'not sure');
        }
        // emailConfirmationKey has to be 32 characters length max
        $retval[]=array('emailConfirmationKey', 'length', 'max'=>32, 'on'=>'not sure');
        // firstName has to be 128 characters length max
        $retval[]=array('firstName', 'length', 'max'=>128, 'on'=>'not sure');
        // initials has to be 16 characters length max on update
        $retval[]=array('initials', 'length', 'max'=>16, 'on'=>'update');
        // isEmailConfirmed is in range
        $retval[]=array('isEmailConfirmed', 'in', 'range'=>array(null,self::EMAIL_IS_CONFIRMED,self::EMAIL_IS_NOT_CONFIRMED), 'strict'=>true, 'allowEmpty'=>false, 'on'=>'not sure');
        // isEmailVisible is in range
        $retval[]=array('isEmailVisible', 'in', 'range'=>array(null,self::EMAIL_IS_VISIBLE,self::EMAIL_IS_NOT_VISIBLE), 'strict'=>true, 'allowEmpty'=>false, 'on'=>'not sure');
        if(Yii::app()->user->checkAccess(User::ADMINISTRATOR))
            // isScreenNameEditable needs to be a boolean
            $retval[]=array('isScreenNameEditable', 'boolean', 'on'=>'not sure');//in
        // lastLoginTime has to be 10 characters length max
        $retval[]=array('lastLoginTime', 'length', 'max'=>10, 'on'=>'not sure');
        // lastVisitTime has to be 10 characters length max
        $retval[]=array('lastVisitTime', 'length', 'max'=>10, 'on'=>'not sure');
        // lastName has to be 128 characters length max
        $retval[]=array('lastName', 'length', 'max'=>128, 'on'=>'not sure');
        // middleName has to be 128 characters length max
        $retval[]=array('middleName', 'length', 'max'=>128, 'on'=>'not sure');
        // occupation has to be 128 characters length max on update
        $retval[]=array('occupation', 'length', 'max'=>128, 'on'=>'update');
        // secretAnswer has to be 255 characters length max
        $retval[]=array('secretAnswer', 'length', 'max'=>255, 'on'=>'not sure');
        // totalTimeLoggedIn has to be integer
        $retval[]=array('totalTimeLoggedIn', 'numerical', 'integerOnly'=>true, 'on'=>'not sure');
        // totalTimeLoggedIn has to be 9 characters length max
        $retval[]=array('totalTimeLoggedIn', 'length', 'max'=>9, 'on'=>'not sure');
        return $retval;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // user details belongs to an 'user' record associated
            'user' => array(self::BELONGS_TO,'User','userId',
                'alias'=>'UserDetails_User'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'deactivationTime'=>Yii::t('t','Deactivation date'),
            'Deact'=>Yii::t('t','Deact.[Deactivation]'),
            'isEmailConfirmed'=>Yii::t('t','Email is confirmed'),
            'isEmailVisible'=>Yii::t('t','Email is visible'),
            'initials'=>Yii::t('t','Initials'),
            'occupation'=>Yii::t('t','Occupation'),
            'updateTime'=>Yii::t('t','Update date'),
            'userId'=>'User',
            'passwordHint'=>'Password Hint',
            'emailConfirmationKey'=>'Email Confirmation Key',
            'isScreenNameEditable'=>'Is Screen Name Editable',
            'firstName'=>'First Name',
            'middleName'=>'Middle Name',
            'lastName'=>'Last Name',
            'gender'=>'Gender',
            'birthDate'=>'Birth Date',
            'textStatus'=>'Text Status',
            'lastLoginTime'=>'Last login date',
            'lastVisitTime'=>'Last visit date',
            'totalTimeLoggedIn'=>'Total Time Logged In',
            'secretQuestion'=>'Secret Question',
            'secretAnswer'=>'Secret Answer',
            'administratorNote'=>'Administrator note',
        );
    }

    /**
     * Prepares attributes before performing validation.
     */
    protected function beforeValidate()
    {
        $scenario=$this->getScenario();
        if(isset($_POST[__CLASS__]['isEmailConfirmed']) && $this->isEmailConfirmed!==self::EMAIL_IS_CONFIRMED && $this->isEmailConfirmed!==self::EMAIL_IS_NOT_CONFIRMED)
            // enum('0','1') null
            $this->isEmailConfirmed=null;
        if(isset($_POST[__CLASS__]['isEmailVisible']) && $this->isEmailVisible!==self::EMAIL_IS_VISIBLE && $this->isEmailVisible!==self::EMAIL_IS_NOT_VISIBLE)
            // enum('0','1') null
            $this->isEmailVisible=null;
        // parent does all common work
        return parent::beforeValidate();
    }

    /**
     * Last model processing before save in db.
     */
    /*protected function beforeSave()
    {
        return true;
    }*/

    /**
     * Returns i18n (translated) representation of the attribute value for view.
     * @param string the attribute name
     * @return string the attribute value's translation
     */
    public function getAttributeView($attribute)
    {
        switch($attribute)
        {
            case 'isEmailConfirmed':
                switch($this->isEmailConfirmed)
                {
                    case self::EMAIL_IS_CONFIRMED:
                        return Yii::t('attr','Yes (Member has confirmed indicated email)');
                    case self::EMAIL_IS_NOT_CONFIRMED:
                        return Yii::t('attr','No (Member has not yet confirmed indicated email)');
                    default:
                        return $this->isEmailConfirmed;
                }
            case 'isEmailVisible':
                switch($this->isEmailVisible)
                {
                    case self::EMAIL_IS_VISIBLE:
                        return Yii::t('attr','Yes (Email is visible by all members)');
                    case self::EMAIL_IS_NOT_VISIBLE:
                        return Yii::t('attr','No (Email is not visible by other members)');
                    case null:
                        return Yii::t('attr','By default (Email is not visible by other members)');
                    default:
                        return $this->isEmailVisible;
                }
            default:
                return $this->$attribute;
        }
    }

    /**
     * Returns data array of the attribute for create/update.
     * @param string the attribute name
     * @return array the attribute's data
     */
    public function getAttributeData($attribute)
    {
        switch($attribute)
        {
            case 'isEmailConfirmed':
                return array(
                    self::EMAIL_IS_NOT_CONFIRMED=>Yii::t('attr','No (Member has not yet confirmed indicated email)'),
                    self::EMAIL_IS_CONFIRMED=>Yii::t('attr','Yes (Member has confirmed indicated email)'),
                );
            case 'isEmailVisible':
                return array(
                    null=>Yii::t('attr','By default (Email is not visible by other members)'),
                    self::EMAIL_IS_NOT_VISIBLE=>Yii::t('attr','No (Email is not visible by other members)'),
                    self::EMAIL_IS_VISIBLE=>Yii::t('attr','Yes (Email is visible by all members)'),
                );
            default:
                return $this->$attribute;
        }
    }

    /**
     * Generates the email confirmation key.
     * @return string key
     */
    public function generateConfirmationKey()
    {
        return md5(uniqid(rand(),true));
    }

    /**
     * Whether user email is visible by all members.
     * @return bool
     */
    public function isEmailVisible()
    {
        return $this->isEmailVisible===self::EMAIL_IS_VISIBLE;
    }
}