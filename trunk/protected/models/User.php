<?php

class User extends CActiveRecord
{
    /**
     * The followings are the available columns in table 'User':
     * @var integer $id
     * @var string $username
     * @var string $password
     * @var string $email
     * @var string $email2
     * @var string $screenName
     * @var string $language
     * @var string $cssTheme
     * @var string $accessType
     * @var integer $accessLevel
     * @var string $isActive
     * @var string $createdOn
     * @var string $createdGmtOn
     */
    // id is not in safeAttributes
    //public $id;
    //public $accessLevel;
    //public $accessType;
    //public $createdOn;
    //public $createdGmtOn;
    public $cssTheme;
    public $email;
    public $email2;
    public $emailConfirmationKey;
    //public $isActive;
    public $language;
    public $password;
    public $password2;
    public $screenName;
    public $screenNameSame;
    public $username;
    public $verifyCode;
    //protected static $_isAction;

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
        $retval=array(
            // accessLevel has to be integer
            array('accessLevel', 'numerical', 'integerOnly'=>true),
            // set max length
            array('accessType', 'length', 'max'=>32),
            array('cssTheme', 'length', 'max'=>64),
            array('email', 'length', 'max'=>255),
            array('language', 'length', 'max'=>24),
            // on confirmEmail
            // email and emailConfirmationKey are required
            array('email, emailConfirmationKey', 'required', 'on'=>'confirmEmail'),
            // verifyCode needs to be entered correctly
            array('verifyCode', 'captcha', 'on'=>'confirmEmail', 'allowEmpty'=>!extension_loaded('gd')),
            // on confirmEmailUrl
            // email and emailConfirmationKey are required
            array('email, emailConfirmationKey', 'required', 'on'=>'confirmEmailUrl'),
            // on register
            // email, password, screenName and username are required
            array('email, password, screenName, username', 'required', 'on'=>'register'),
            // email, screenName and username have to be unique
            array('email, screenName, username', 'unique', 'on'=>'register'),
            // email has to be a valid email address
            array('email', 'email', 'on'=>'register'),
            // password should be compared with password2
            array('password', 'compare', 'compareAttribute'=>'password2', 'on'=>'register'),
            // set min/max length
            array('password', 'length', 'min'=>4, 'max'=>64, 'on'=>'register'),
            array('screenName', 'length', 'min'=>3, 'max'=>32, 'on'=>'register'),
            array('username', 'length', 'min'=>3, 'max'=>32, 'on'=>'register'),
            // verifyCode needs to be entered correctly
            array('verifyCode', 'captcha', 'on'=>'register', 'allowEmpty'=>!extension_loaded('gd')),
            // on update
            // screenName is required
            array('screenName', 'required', 'on'=>'update'),
            // screenName has to be unique
            array('screenName', 'unique', 'on'=>'update'),
            // set min/max length
            array('screenName', 'length', 'min'=>3, 'max'=>32, 'on'=>'update'),
        );
        // email should be compared with email2
        $this->hasVirtualAttribute('email2') && ($retval[]=array('email', 'compare', 'compareAttribute'=>'email2', 'on'=>'register'));
        return $retval;
    }

    /**
     * @return array attributes that can be massively assigned
     * using something like $model->attributes=$_POST['Model'];
     */
    public function safeAttributes()
    {
        return array(
            'cssTheme',
            'email',
            'email2',
            'emailConfirmationKey',
            'language',
            'password',
            'password2',
            'screenName',
            'screenNameSame',
            'username',
            'verifyCode',
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // user has a 'details' record associated
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
            'cssTheme'=>Yii::t('t','CSS Theme'),
            'email'=>Yii::t('t','Email'),
            'email2'=>Yii::t('t','Repeat email'),
            'emailConfirmationKey'=>Yii::t('t','Confirmation key'),
            'language'=>Yii::t('t','Language'),
            'password'=>Yii::t('t','Password'),
            'password2'=>Yii::t('t','Repeat password'),
            'screenName'=>Yii::t('t','Screen name'),
            'screenNameSame'=>Yii::t('t','Same as username'),
            'username'=>Yii::t('t','Username'),
            'verifyCode'=>Yii::t('t','Verification code'),
            'accessType'=>Yii::t('t','Access type'),
            'accessLevel'=>Yii::t('t','Access level'),
            'isActive'=>Yii::t('t','Is active'),
            'createdOn'=>Yii::t('t','Joined'),
            'createdGmtOn'=>'Created gmt on',
        );
    }

    /**
     * Prepares attributes before performing validation.
     */
    protected function beforeValidate($on)
    {
        if($on==='register')
        {
            if($this->screenNameSame)
                // make screenName same as username
                $this->screenName=$this->username;
        }
        return true;
    }

    /**
     * Last model processing before save in db.
     */
    protected function beforeSave()
    {
        if($this->isNewRecord)
        {
            $this->password=md5($this->password);
            $this->createdOn=date('Y-m-d H:i:s');
            $this->createdGmtOn=gmdate('Y-m-d H:i:s');
        }
        return true;
    }

    /**
     * @param string attribute name
     * @return boolean whether this AR has the named attribute (table column)
     * and it is allowed in config params/MParams.
     */
    public function hasVirtualAttribute($name)
    {
        return property_exists(get_class($this),$name) && MParams::getModelAttributes(get_class($this),$name)!==false;
    }

    /**
     * Returns i18n (translated) representation of the attribute value for view.
     * @param string the attribute name
     * @return string the attribute value's translation
     */
    public function getAttributeView($attribute)
    {
        switch($attribute)
        {
            case 'accessType':
                switch($this->accessType)
                {
                    case 'member':
                        return Yii::t('t','Member');
                        break;
                    case 'customer':
                        return Yii::t('t','Customer');
                        break;
                    case 'facilitator':
                        return Yii::t('t','Facilitator');
                        break;
                    case 'moderator':
                        return Yii::t('t','Moderator');
                        break;
                    case 'admin':
                        return Yii::t('t','Admin');
                        break;
                    default:
                        return $this->accessType;
                        break;
                }
            case 'cssTheme':
                $availableCssThemes=MParams::getAvailableCssThemes();
                if((is_string($this->cssTheme) || is_int($this->cssTheme)) && array_key_exists($this->cssTheme,$availableCssThemes))
                    return Yii::t('cssTheme',$availableCssThemes[$this->cssTheme]);
                return $this->cssTheme;
            case 'isActive':
                switch($this->isActive)
                {
                    case '1':
                        return Yii::t('t','Yes (Member account is On)');
                        break;
                    case '0':
                        return Yii::t('t','No (Member account is Off)');
                        break;
                    case null:
                        return Yii::t('t','By default (Member account is On)');
                        break;
                    default:
                        return $this->isActive;
                        break;
                }
            case 'language':
                $availableLanguages=MParams::getAvailableLanguages();
                if((is_string($this->language) || is_int($this->language)) && array_key_exists($this->language,$availableLanguages))
                    return Yii::t('t',$availableLanguages[$this->language],array(0));
                return $this->language;
            default:
                return $this->$attribute;
        }
    }

    /**
     * Set whether is action $action.
     * 
     * @param string $action
     * @param bool $value
     */
    /*public static function setIsAction($action,$value)
    {
        self::$_isAction[$action]=(bool)$value;
    }*/

    /**
     * Get whether is action $action.
     * 
     * @param string $action
     * @return bool
     */
    /*public static function getIsAction($action)
    {
        if(is_null(self::$_isAction[$action]))
        {
            return Yii::app()->controller->getId()==='user' && Yii::app()->controller->getAction()->getId()===$action;
        }
        else
            return (bool)self::$_isAction[$action];
    }*/
}