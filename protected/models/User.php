<?php

class User extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'User':
     * @var integer $id
     * @var string $username
     * @var string $password
     * @var string $email
     * @var string $screenName
     * @var string $language
     * @var string $interface
     * @var string $accessType
     * @var integer $accessLevel
     * @var string $isActive
     * @var integer $createTime
     */

    private static $_privateData;

    const IS_ACTIVE='1';
    const IS_NOT_ACTIVE='0';
    const MEMBER='member';
    const MEMBER_T='Member';
    const CLIENT='client';
    const CLIENT_T='Client';
    const CONSULTANT='consultant';
    const CONSULTANT_T='Consultant';
    const MANAGER='manager';
    const MANAGER_T='Manager';
    const ADMINISTRATOR='administrator';
    const ADMINISTRATOR_T='Administrator';

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
            array('email', 'length', 'max'=>255),
            array('interface', 'length', 'max'=>64),
            array('language', 'length', 'max'=>24),
            // on confirmEmail
            // email and emailConfirmationKey are required
            array('email, emailConfirmationKey', 'required', 'on'=>'confirmEmail'),
            // verifyCode needs to be entered correctly
            array('verifyCode', 'captcha', 'on'=>'confirmEmail', 'allowEmpty'=>!extension_loaded('gd')),
            // on confirmEmailUrl
            // email and emailConfirmationKey are required
            array('email, emailConfirmationKey', 'required', 'on'=>'confirmEmailUrl'),
            // on create
            // email, password, screenName and username are required
            array('email, password, screenName, username', 'required', 'on'=>'create'),
            // email, screenName and username have to be unique
            array('email, screenName, username', 'unique', 'on'=>'create'),
            // email has to be a valid email address
            array('email', 'email', 'on'=>'create'),
            // set min/max length
            array('password', 'length', 'min'=>4, 'max'=>64, 'on'=>'create'),
            array('screenName', 'length', 'min'=>3, 'max'=>32, 'on'=>'create'),
            array('username', 'length', 'min'=>3, 'max'=>32, 'on'=>'create'),
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
            'email',
            'email2',
            'emailConfirmationKey',
            'interface',
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
            'details' => array(self::HAS_ONE,'UserDetails','userId','alias'=>'UserUserDetails'),
            // one user has many 'manager time' records associated
            'allManagerTime' => array(self::HAS_MANY,'Time','managerId','order'=>"??.`id` ASC",'alias'=>'UserManagerTime'),
            // one user has many 'consultant time' records associated
            'allConsultantTime' => array(self::HAS_MANY,'Time','consultantId','order'=>"??.`id` ASC",'alias'=>'UserConsultantTime'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'accessLevel'=>Yii::t('t','Access level'),
            'accessType'=>Yii::t('t','Access type'),
            'createTime'=>Yii::t('t','Registration date'),
            'email'=>Yii::t('t','Email'),
            'email2'=>Yii::t('t','Repeat email'),
            'emailConfirmationKey'=>Yii::t('t','Confirmation key'),
            'id'=>Yii::t('t','ID'),
            'interface'=>Yii::t('t','Interface'),
            'isActive'=>Yii::t('t','Member is active'),
            'language'=>Yii::t('t','Language'),
            'password'=>Yii::t('t','Password'),
            'password2'=>Yii::t('t','Repeat password'),
            'screenName'=>Yii::t('t','Screen name'),
            'screenNameSame'=>Yii::t('t','Same as username'),
            'username'=>Yii::t('t','Username'),
            'verifyCode'=>Yii::t('t','Verification code'),
        );
    }

    /**
     * Prepares attributes before performing validation.
     */
    protected function beforeValidate($on)
    {
        if($on==='create' || $on==='register')
        {
            if($this->screenNameSame)
                // make screenName same as username
                $this->screenName=$this->username;
        }
        if(isset($_POST[__CLASS__]['isActive']) && $this->isActive!==self::IS_ACTIVE && $this->isActive!==self::IS_NOT_ACTIVE)
            // enum('0','1') null
            $this->isActive=null;
        if(isset($_POST[__CLASS__]['accessType']) && $this->accessType!==self::MEMBER && $this->accessType!==self::CLIENT
            && $this->accessType!==self::CONSULTANT && $this->accessType!==self::MANAGER && $this->accessType!==self::ADMINISTRATOR
        )
            // we hope to switch to roles in nearest future
            $this->accessType=self::MEMBER;
        // parent does all common work
        return parent::beforeValidate($on);
    }

    /**
     * Last model processing before save in db.
     */
    protected function beforeSave()
    {
        if(isset($_POST[__CLASS__]['password']))
            // password needs to be encrypted
            $this->password=md5($this->password);
        return true;
    }

    /**
     * Whether requested attribute is allowed by /config/params.php 'modelAttributes'
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
                    case self::MEMBER:
                        return Yii::t('t',self::MEMBER_T);
                        break;
                    case self::CLIENT:
                        return Yii::t('t',self::CLIENT_T);
                        break;
                    case self::CONSULTANT:
                        return Yii::t('t',self::CONSULTANT_T);
                        break;
                    case self::MANAGER:
                        return Yii::t('t',self::MANAGER_T);
                        break;
                    case self::ADMINISTRATOR:
                        return Yii::t('t',self::ADMINISTRATOR_T);
                        break;
                    default:
                        return $this->accessType;
                        break;
                }
            case 'interface':
                $availableInterfaces=MParams::getAvailableInterfaces();
                if((is_string($this->interface) || is_int($this->interface)) && array_key_exists($this->interface,$availableInterfaces))
                    return Yii::t('ui',$availableInterfaces[$this->interface]);
                return $this->interface;
            case 'isActive':
                switch($this->isActive)
                {
                    case self::IS_ACTIVE:
                        return Yii::t('attr','Yes (Member account is On)');
                        break;
                    case self::IS_NOT_ACTIVE:
                        return Yii::t('attr','No (Member account is Off)');
                        break;
                    case null:
                        return Yii::t('attr','By default (Member account is On)');
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
     * Returns data array of the attribute for create/update.
     * @param string the attribute name
     * @return array the attribute's data
     */
    public function getAttributeData($attribute)
    {
        switch($attribute)
        {
            case 'accessType':
                return array(
                    self::MEMBER=>Yii::t('t',self::MEMBER_T),
                    self::CLIENT=>Yii::t('t',self::CLIENT_T),
                    self::CONSULTANT=>Yii::t('t',self::CONSULTANT_T),
                    self::MANAGER=>Yii::t('t',self::MANAGER_T),
                    self::ADMINISTRATOR=>Yii::t('t',self::ADMINISTRATOR_T),
                );
            case 'interface':
                return MParams::getAvailableInterfaces();
            case 'isActive':
                return array(
                    null=>Yii::t('attr','By default (Member account is On)'),
                    self::IS_NOT_ACTIVE=>Yii::t('attr','No (Member account is Off)'),
                    self::IS_ACTIVE=>Yii::t('attr','Yes (Member account is On)'),
                );
            case 'language':
                return MParams::getAvailableLanguages();
            default:
                return $this->$attribute;
        }
    }

    /**
     * Find all active members.
     * @return array of User objects
     */
    public static function findAllActiveRecords()
    {
        $criteria=new CDbCriteria;
        $t=self::model()->tableName();
        $criteria->condition="`$t`.`isActive` IS NULL OR `$t`.`isActive` != '".self::IS_NOT_ACTIVE."'";
        return self::model()->findAll($criteria);
    }

    /**
     * Set user private data, such as 'accessType'.
     * Save it in a static array on every page load,
     * because this data can be changed by administrator at any time.
     * Saving this data for optimization in a session array
     * doesn't make much security sence, because session data
     * can be accessed and changed from any point of the system,
     * which doesn't make this data private any more.
     */
    private static function setPrivateData()
    {
        // user is guest if he is not logged in
        if(!Yii::app()->user->isGuest)
        {
            if(($user=self::model()->findByPk(Yii::app()->user->id))!==false)
            {
                // simple save it in a private array for later accessing by {@link self::getPrivateData()}
                self::$_privateData['accessLevel']=$user->accessLevel;
                self::$_privateData['accessType']=$user->accessType;
            }
            else
            {
                // hmmm, user was not loaded? how's that possible...
                Yii::log(W3::t('system',
                    'Could not load {model} model. Model ID: {modelId}. Method called: {method}.',
                    array(
                        '{model}'=>__CLASS__,
                        '{modelId}'=>Yii::app()->user->id,
                        '{method}'=>__METHOD__.'()'
                    )
                ),'error','w3');
                // still hoping that the model load above will get fixed,
                // so we won't need to self::$_privateData=array();
            }
        }
    }

    /**
     * Get user private data, such as 'accessType'.
     * This data is valid for this page only.
     * It can be changed on next page load.
     * @param string $name of the data
     */
    public static function getPrivateData($name)
    {
        if(Yii::app()->user->isGuest)
            // guest doesn't have any private data
            return null;
        if(is_null(self::$_privateData))
            // private data is usually being set from this point
            self::setPrivateData();
        // if we called setPrivateData() it doesn't necessary mean that
        // $_privateData[$name] is set, so we have to check to avoid php notice
        return isset(self::$_privateData[$name]) ? self::$_privateData[$name] : null;
    }

    /**
     * Whether user is member.
     * @param string accessType
     * @return bool
     */
    public static function isMember($accessType=null)
    {
        if(!is_null($accessType))
            return $accessType===self::MEMBER;
        return self::getPrivateData('accessType')===self::MEMBER;
    }

    /**
     * Whether user is client.
     * @param string accessType
     * @return bool
     */
    public static function isClient($accessType=null)
    {
        if(!is_null($accessType))
            return $accessType===self::CLIENT;
        return self::getPrivateData('accessType')===self::CLIENT;
    }

    /**
     * Whether user is consultant.
     * @param string accessType
     * @return bool
     */
    public static function isConsultant($accessType=null)
    {
        if(!is_null($accessType))
            return $accessType===self::CONSULTANT;
        return self::getPrivateData('accessType')===self::CONSULTANT;
    }

    /**
     * Whether user is manager.
     * @param string accessType
     * @return bool
     */
    public static function isManager($accessType=null)
    {
        if(!is_null($accessType))
            return $accessType===self::MANAGER;
        return self::getPrivateData('accessType')===self::MANAGER;
    }

    /**
     * Whether user is administrator.
     * @param string accessType
     * @return bool
     */
    public static function isAdministrator($accessType=null)
    {
        if(!is_null($accessType))
            return $accessType===self::ADMINISTRATOR;
        return self::getPrivateData('accessType')===self::ADMINISTRATOR;
    }

    /**
     * Whether user is accessType.
     * Use this function if you have a custom accessType in db,
     * custom = not one of (member, client, consultant, manager, administrator).
     * @param string accessType
     * @return bool
     */
    public static function isAccessType($accessType)
    {
        switch($accessType)
        {
            case self::MEMBER:
                return self::isMember();
                break;
            case self::CLIENT:
                return self::isClient();
                break;
            case self::CONSULTANT:
                return self::isConsultant();
                break;
            case self::MANAGER:
                return self::isManager();
                break;
            case self::ADMINISTRATOR:
                return self::isAdministrator();
                break;
            default:
                return self::getPrivateData('accessType')===$accessType;
                break;
        }
    }

    /**
     * Set whether is action $action.
     * @param string $action
     * @param bool $value
     */
    /*public static function setIsAction($action,$value)
    {
        self::$_isAction[$action]=(bool)$value;
    }*/

    /**
     * Get whether is action $action.
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