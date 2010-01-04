<?php

class User extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'W3User':
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
    /**
     * @var string, the 'Repeat email' field
     */
    public $email2;

    /**
     * @var string, is necessary for {@link UserController::actionConfirmEmail}
     */
    public $emailConfirmationKey;

    /**
     * @var string, the 'Repeat password' field
     */
    public $password2;

    /**
     * @var boolean whether screenName is the same as username
     */
    public $screenNameSame;

    /**
     * @var boolean string verification code
     */
    public $verifyCode;

    /**
     * @var integer counters used in the sql queries
     */
    public $countTask;
    public $countTime;
    public $countConsultantTime;
    public $countMangerTime;

    /**
     * @var array of user private data, like 'accessType'.
     * Is used by {@link isAdministrator} and so on.
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
            // email, password and screenName are required
            array('email, password, screenName', 'required', 'on'=>'create'),
            // email and screenName have to be unique
            array('email, screenName', 'unique', 'on'=>'create'),
            // email has to be a valid email address
            array('email', 'email', 'on'=>'create'),
            // set min/max length
            array('password', 'length', 'min'=>4, 'max'=>64, 'on'=>'create'),
            array('screenName', 'length', 'min'=>3, 'max'=>32, 'on'=>'create'),
            // on register
            // email, password and screenName are required
            array('email, password, screenName', 'required', 'on'=>'register'),
            // email and screenName have to be unique
            array('email, screenName', 'unique', 'on'=>'register'),
            // email has to be a valid email address
            array('email', 'email', 'on'=>'register'),
            // password should be compared with password2
            array('password', 'compare', 'compareAttribute'=>'password2', 'on'=>'register'),
            // set min/max length
            array('password', 'length', 'min'=>4, 'max'=>64, 'on'=>'register'),
            array('screenName', 'length', 'min'=>3, 'max'=>32, 'on'=>'register'),
            // verifyCode needs to be entered correctly
            array('verifyCode', 'captcha', 'on'=>'register', 'allowEmpty'=>!extension_loaded('gd')),
            // on update
            // email has to be a valid email address
            array('email', 'email', 'on'=>'update'),
            // screenName is required
            array('screenName', 'required', 'on'=>'update'),
            // screenName has to be unique
            array('screenName', 'unique', 'on'=>'update'),
            // set min/max length
            array('screenName', 'length', 'min'=>3, 'max'=>32, 'on'=>'update'),
        );
        if($this->hasVirtualAttribute('username'))
        {
            // username is allowed
            $retval[]=array('username', 'required', 'on'=>'create');
            $retval[]=array('username', 'unique', 'on'=>'create');
            $retval[]=array('username', 'length', 'min'=>3, 'max'=>32, 'on'=>'create');
            $retval[]=array('username', 'required', 'on'=>'register');
            $retval[]=array('username', 'unique', 'on'=>'register');
            $retval[]=array('username', 'length', 'min'=>3, 'max'=>32, 'on'=>'register');
        }
        if($this->hasVirtualAttribute('email2'))
            // email should be compared with email2
            $retval[]=array('email', 'compare', 'compareAttribute'=>'email2', 'on'=>'register');
        return $retval;
    }

    /**
     * @return array attributes that can be massively assigned
     * using something like $model->attributes=$_POST['Model'];
     */
    public function safeAttributes()
    {
        $retval=array(
            //'email',
            'email2',
            'emailConfirmationKey',
            'interface',
            'language',
            'password',
            'password2',
            'screenName',
            'screenNameSame',
            //'username',
            'verifyCode',
        );
        if(User::isAdministrator())
            $retval=array_merge($retval,array('accessLevel','accessType','isActive'));
        return $retval;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // each user has a 'details' record associated
            'details' => array(self::HAS_ONE,'UserDetails','userId',
                'alias'=>'UserUserDetails'
            ),
            // many user has many 'company' records associated
            /*'allCompany' => array(self::MANY_MANY,'Company',
                User2Company::model()->tableName().'(userId,companyId)',
                'order'=>"allCompany_UserCompany.`companyPriority` ASC, allCompany_UserCompany.`id` ASC",
                'alias'=>'UserCompany'
            ),
            // one user has many 'consultant2project' records associated
            'allConsultant2Project' => array(self::HAS_MANY,'User2Project','userId',
                //'condition'=>"??.`role`='".User2Project::CONSULTANT."'",
                'on'=>"??.`role`='".User2Project::CONSULTANT."'",
                'order'=>"??.`projectPriority` ASC, ??.`id` ASC",
                'alias'=>'UserConsultant2Project'
            ),
            // many user has many 'consultant project' records associated
            'allConsultantProject' => array(self::MANY_MANY,'Project',
                User2Project::model()->tableName().'(userId,projectId)',
                'on'=>"allConsultantProject_UserConsultantProject.`role`='".User2Project::CONSULTANT."'",
                'order'=>"allConsultantProject_UserConsultantProject.`projectPriority` ASC, allConsultantProject_UserConsultantProject.`id` ASC",
                'alias'=>'UserConsultantProject'
            ),
            // one user has many 'consultant2task' records associated
            'allConsultant2Task' => array(self::HAS_MANY,'User2Task','userId',
                //'condition'=>"??.`role`='".User2Task::CONSULTANT."'",
                'on'=>"??.`role`='".User2Task::CONSULTANT."'",
                'order'=>"??.`taskPriority` ASC, ??.`id` ASC",
                'alias'=>'UserConsultant2Task'
            ),
            // many user has many 'consultant task' records associated
            'allConsultantTask' => array(self::MANY_MANY,'Task',
                User2Task::model()->tableName().'(userId,taskId)',
                'on'=>"allConsultantTask_UserConsultantTask.`role`='".User2Project::CONSULTANT."'",
                'order'=>"allConsultantTask_UserConsultantTask.`taskPriority` ASC, allConsultantTask_UserConsultantTask.`id` ASC",
                'alias'=>'UserConsultantTask'
            ),
            // one user has a number of 'consultant task' records associated
            'consultantTaskCount' => array(self::STAT,'Task',
                User2Task::model()->tableName().'(userId,taskId)',
                //'on'=>"??.`role`='".User2Project::CONSULTANT."'",
            ),
            // one user has many 'consultant time' records associated
            'allConsultantTime' => array(self::HAS_MANY,'Time','consultantId',
                'order'=>"??.`id` ASC",
                'alias'=>'UserConsultantTime'
            ),
            // one user has one of many 'consultant time' records associated
            //'oneConsultantTime' => array(self::HAS_ONE,'Time','consultantId',
                //'alias'=>'UserOneConsultantTime'
            //),
            // one user has a number of 'consultant time' records associated
            'consultantTimeCount' => array(self::STAT,'Time','consultantId'),
            // one user has many 'manager2project' records associated
            'allManager2Project' => array(self::HAS_MANY,'User2Project','userId',
                //'condition'=>"??.`role`='".User2Project::MANAGER."'",
                'on'=>"??.`role`='".User2Project::MANAGER."'",
                'order'=>"??.`projectPriority` ASC, ??.`id` ASC",
                'alias'=>'UserManager2Project'
            ),
            // many user has many 'manager project' records associated
            'allManagerProject' => array(self::MANY_MANY,'Project',
                User2Project::model()->tableName().'(userId,projectId)',
                'on'=>"allManagerProject_UserManagerProject.`role`='".User2Project::MANAGER."'",
                'order'=>"allManagerProject_UserManagerProject.`projectPriority` ASC, allManagerProject_UserManagerProject.`id` ASC",
                'alias'=>'UserManagerProject'
            ),
            // one user has many 'manager2task' records associated
            'allManager2Task' => array(self::HAS_MANY,'User2Task','userId',
                //'condition'=>"??.`role`='".User2Task::MANAGER."'",
                'on'=>"??.`role`='".User2Task::MANAGER."'",
                'order'=>"??.`taskPriority` ASC, ??.`id` ASC",
                'alias'=>'UserManager2Task'
            ),
            // many user has many 'manager task' records associated
            'allManagerTask' => array(self::MANY_MANY,'Task',
                User2Task::model()->tableName().'(userId,taskId)',
                'on'=>"allManagerTask_UserManagerTask.`role`='".User2Task::MANAGER."'",
                'order'=>"allManagerTask_UserManagerTask.`taskPriority` ASC, allManagerTask_UserManagerTask.`id` ASC",
                'alias'=>'UserManagerTask'
            ),
            // one user has many 'manager time' records associated
            'allManagerTime' => array(self::HAS_MANY,'Time','managerId',
                'order'=>"??.`id` ASC",
                'alias'=>'UserManagerTime'
            ),
            // one user has one of many 'manager time' records associated
            //'oneManagerTime' => array(self::HAS_ONE,'Time','managerId',
                //'alias'=>'UserOneManagerTime'
            //),
            // one user has a number of 'manager time' records associated
            'managerTimeCount' => array(self::STAT,'Time','managerId'),
            // many user has many 'project' records associated
            'allProject' => array(self::MANY_MANY,'Project',
                User2Project::model()->tableName().'(userId,projectId)',
                'order'=>"allProject_UserProject.`projectPriority` ASC, allProject_UserProject.`id` ASC",
                'alias'=>'UserProject'
            ),
            // one user has many 'user2company' records associated
            'allUser2Company' => array(self::HAS_MANY,'User2Company','userId',
                'order'=>"??.`companyPriority` ASC, ??.`id` ASC",
                'alias'=>'UserUser2Company'
            ),
            // one user has many 'user2project' records associated
            'allUser2Project' => array(self::HAS_MANY,'User2Project','userId',
                'order'=>"??.`projectPriority` ASC, ??.`id` ASC",
                'alias'=>'UserUser2Project'
            ),
            // one user has many 'user2task' records associated
            'allUser2Task' => array(self::HAS_MANY,'User2Task','userId',
                'order'=>"??.`taskPriority` ASC, ??.`id` ASC",
                'alias'=>'UserUser2Task'
            ),*/
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
            'Registered'=>Yii::t('t','Registered[member]'),
            'screenName'=>Yii::t('t','Screen name'),
            'screenNameSame'=>Yii::t('t','Same as username'),
            'username'=>Yii::t('t','Username'),
            'User interface'=>Yii::t('t','User interface'),
            'verifyCode'=>Yii::t('t','Verification code'),
        );
    }

    /**
     * Prepares attributes before performing validation.
     */
    protected function beforeValidate($on)
    {
        if(($on==='create' || $on==='register') && $this->screenNameSame && $this->username!=='' && $this->username!==null)
            // make screenName same as username
            $this->screenName=$this->username;
        if($this->isNewRecord && !$this->hasVirtualAttribute('username') && ($this->username==='' || $this->username===null))
        {
            // if username is not allowed, we make it equal to email
            // because in db username field is unique key
            $this->username=substr($this->email,0,128);
            if(User::model()->findByAttributes(array('username'=>$this->username))!==null)
                // if username is already in use, generate an unique id
                $this->username=md5(uniqid(rand(),true));
        }
        if($on==='update' && isset($_POST[__CLASS__]['email']) && $this->email!==$_POST[__CLASS__]['email'])
        {
            // email is being updated
            $this->email=$_POST[__CLASS__]['email'];
            $this->details->isEmailConfirmed='0';
        }
        if(isset($_POST[__CLASS__]['isActive']) && $this->isActive!==self::IS_ACTIVE && $this->isActive!==self::IS_NOT_ACTIVE)
            // enum('0','1') null
            $this->isActive=null;
        if(isset($_POST[__CLASS__]['accessType']) && $this->accessType!==self::MEMBER && $this->accessType!==self::CLIENT
            && $this->accessType!==self::CONSULTANT && $this->accessType!==self::MANAGER && $this->accessType!==self::ADMINISTRATOR
        )
            // we hope to switch to roles in nearest future
            $this->accessType=self::MEMBER;
        if(isset($_POST[__CLASS__]['accessType']))
        {
            // if access type changed, we need to adjust access level
            $accessLevel=array(self::MEMBER=>1,self::CLIENT=>2,self::CONSULTANT=>3,self::MANAGER=>4,self::ADMINISTRATOR=>5);
            $this->accessLevel=$accessLevel[$this->accessType];
        }
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
     * Whether this AR has the named attribute (table column or class property)
     * and it is not disallowed by config
     * (params.php 'modelAttributes', {@link MParams::getModelAttributes}).
     * @param string attribute name
     * @return boolean
     */
    public function hasVirtualAttribute($name)
    {
        return ($this->hasAttribute($name) || property_exists(get_class($this),$name)) && MParams::getModelAttributes(get_class($this),$name)!==false;
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
                    case self::CLIENT:
                        return Yii::t('t',self::CLIENT_T);
                    case self::CONSULTANT:
                        return Yii::t('t',self::CONSULTANT_T);
                    case self::MANAGER:
                        return Yii::t('t',self::MANAGER_T);
                    case self::ADMINISTRATOR:
                        return Yii::t('t',self::ADMINISTRATOR_T);
                    default:
                        return $this->accessType;
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
                    case self::IS_NOT_ACTIVE:
                        return Yii::t('attr','No (Member account is Off)');
                    case null:
                        return Yii::t('attr','By default (Member account is On)');
                    default:
                        return $this->isActive;
                }
            case 'language':
                $availableLanguages=MParams::getAvailableLanguages();
                if((is_string($this->language) || is_int($this->language)) && array_key_exists($this->language,$availableLanguages))
                    return Yii::t('t',$availableLanguages[$this->language]);
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
     * @param array of additional conditions
     * @return array of User objects
     */
    public function findAllActiveRecords($conditions=array())
    {
        $id=(isset($conditions[0]) && ctype_digit($conditions[0]) && $conditions[0]>=1) ? $conditions[0] : null;
        $criteria=new CDbCriteria;
        $t=self::model()->tableName();
        $criteria->condition="`$t`.`isActive` IS NULL OR `$t`.`isActive` != '".self::IS_NOT_ACTIVE."'";
        if($id)
            $criteria->condition.=" OR `$t`.`id` = '$id'";
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
            if(($user=self::model()->findByPk(Yii::app()->user->id))!==null)
            {
                // just save it in a private array for later accessing by {@link getPrivateData}
                self::$_privateData['accessLevel']=$user->accessLevel;
                self::$_privateData['accessType']=$user->accessType;
            }
            else
            {
                // hmmm, user was not loaded? how's that possible...
                Yii::log(W3::t('system',
                    'Could not load {model} model. Model ID: {modelId}. Method called: {method}.',
                    array('{model}'=>__CLASS__,'{modelId}'=>Yii::app()->user->id,'{method}'=>__METHOD__.'()')
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
        // if we called {@link setPrivateData} it doesn't necessary mean that
        // $_privateData[$name] is set, so we have to check to avoid php notice
        return isset(self::$_privateData[$name]) ? self::$_privateData[$name] : null;
    }

    /**
     * Whether user is member.
     * @param string accessType
     * @return boolean
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
     * @return boolean
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
     * @return boolean
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
     * @return boolean
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
     * @return boolean
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
     * @return boolean
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
     * @param boolean $value
     */
    /*public static function setIsAction($action,$value)
    {
        self::$_isAction[$action]=(bool)$value;
    }*/

    /**
     * Get whether is action $action.
     * @param string $action
     * @return boolean
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