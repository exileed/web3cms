<?php

class User extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'w3_user':
     * @var integer $id
     * @var string $username
     * @var string $password
     * @var string $salt
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
    public $countTask,$countTime,$countConsultantTime,$countMangerTime;

    /**
     * Temporary fix
     */
    public $invoiceId;

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
        return 'user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $retval=array();
        if(User::isAdministrator())
        {
            // accessLevel has to be integer
            $retval[]=array('accessLevel', 'numerical', 'integerOnly'=>true);
            // accessLevel has to be 1 characters length max
            $retval[]=array('accessLevel', 'length', 'max'=>1);
            // accessType has to be 32 characters length max
            $retval[]=array('accessType', 'length', 'max'=>32);
        }
        if($this->hasVirtualAttribute('email2'))
            // email should be compared with email2 on register
            $retval[]=array('email', 'compare', 'compareAttribute'=>'email2', 'on'=>'register');
        // email has to be a valid email address on create, register and update
        $retval[]=array('email', 'email', 'on'=>'create, register, update');
        // email has to be 255 characters length max on create, register and update
        $retval[]=array('email', 'length', 'max'=>255, 'on'=>'create, register, update');
        // email is required on confirmEmail, confirmEmailUrl, create, register and update
        $retval[]=array('email', 'required', 'on'=>'confirmEmail, confirmEmailUrl, create, register, update');
        // email has to be unique on create, register and update
        $retval[]=array('email', 'unique', 'on'=>'create, register, update');
        // email2 is safe on register
        $retval[]=array('email2', 'safe', 'on'=>'register');
        // emailConfirmationKey is required on confirmEmail and confirmEmailUrl
        $retval[]=array('emailConfirmationKey', 'required', 'on'=>'confirmEmail, confirmEmailUrl');
        // interface has to be 64 characters length max on create, register and updateInterface
        $retval[]=array('interface', 'length', 'max'=>64, 'on'=>'create, register, updateInterface');
        if(User::isAdministrator())
            // isActive is in range
            $retval[]=array('isActive', 'in', 'range'=>array(null,self::IS_ACTIVE,self::IS_NOT_ACTIVE), 'strict'=>true, 'allowEmpty'=>false);
        // language has to be 24 characters length max on create, register and update
        $retval[]=array('language', 'length', 'max'=>24, 'on'=>'create, register, update');
        // password should be compared with password2 on register
        $retval[]=array('password', 'compare', 'compareAttribute'=>'password2', 'on'=>'register');
        // password has to be between 4 and 64 characters length on create and register
        $retval[]=array('password', 'length', 'min'=>4, 'max'=>64, 'on'=>'create, register');
        // password is required on create and register
        $retval[]=array('password', 'required', 'on'=>'create, register');
        // password2 is safe on register
        $retval[]=array('password2', 'safe', 'on'=>'register');
        // screenName has to be between 3 and 32 characters length
        $retval[]=array('screenName', 'length', 'min'=>3, 'max'=>32, 'on'=>'create, register, update');
        // screenName is required on create, register and update
        $retval[]=array('screenName', 'required', 'on'=>'create, register, update');
        // screenName has to be unique on create, register and update
        $retval[]=array('screenName', 'unique', 'on'=>'create, register, update');
        // screenNameSame needs to be a boolean on create and register
        $retval[]=array('screenNameSame', 'boolean', 'on'=>'create, register');
        // salt has to be 128 characters length max
        $retval[]=array('salt', 'length', 'max'=>128);
        // salt is unsafe
        $retval[]=array('salt', 'unsafe');
        if($this->hasVirtualAttribute('username'))
        {
            // username has to be between 3 and 32 characters length on create and register
            $retval[]=array('username', 'length', 'min'=>3, 'max'=>32, 'on'=>'create, register');
            // username is required on create and register
            $retval[]=array('username', 'required', 'on'=>'create, register');
            // username has to be unique on create and register
            $retval[]=array('username', 'unique', 'on'=>'create, register');
        }
        // verifyCode needs to be entered correctly on confirmEmail and register
        $retval[]=array('verifyCode', 'captcha', 'on'=>'confirmEmail, register', 'allowEmpty'=>!extension_loaded('gd'));
        return $retval;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations=array(
            // each user has a 'details' record associated
            'details' => array(self::HAS_ONE,'UserDetails','userId',
                'alias'=>'User_UserDetails'
            ),
        );
        if(file_exists(Yii::app()->basePath.'/models/Company.php') && file_exists(Yii::app()->basePath.'/models/User2Company.php'))
            // many user has many 'company' records associated
            $relations['allCompany']=array(self::MANY_MANY,'Company',
                User2Company::model()->tableName().'(userId,companyId)',
                'order'=>"`allCompany_User_Company`.`companyPriority` ASC, `allCompany_User_Company`.`id` ASC",
                'alias'=>'User_Company'
            );
        if(file_exists(Yii::app()->basePath.'/models/User2Company.php'))
            // one user has many 'user2company' records associated
            $relations['allUser2Company']=array(self::HAS_MANY,'User2Company','userId',
                'order'=>"`t`.`companyPriority` ASC, `t`.`id` ASC",
                'alias'=>'User_User2Company'
            );
        if(file_exists(Yii::app()->basePath.'/models/User2Project.php'))
        {
            // one user has many 'consultant2project' records associated
            $relations['allConsultant2Project']=array(self::HAS_MANY,'User2Project','userId',
                'on'=>"`t`.`role`='".User2Project::CONSULTANT."'",
                'order'=>"`t`.`projectPriority` ASC, `t`.`id` ASC",
                'alias'=>'User_Consultant2Project'
            );
            // one user has many 'manager2project' records associated
            $relations['allManager2Project']=array(self::HAS_MANY,'User2Project','userId',
                'on'=>"`t`.`role`='".User2Project::MANAGER."'",
                'order'=>"`t`.`projectPriority` ASC, `t`.`id` ASC",
                'alias'=>'User_Manager2Project'
            );
            // one user has many 'user2project' records associated
            $relations['allUser2Project']=array(self::HAS_MANY,'User2Project','userId',
                'order'=>"`t`.`projectPriority` ASC, `t`.`id` ASC",
                'alias'=>'User_User2Project'
            );
        }
        if(file_exists(Yii::app()->basePath.'/models/Project.php') && file_exists(Yii::app()->basePath.'/models/User2Project.php'))
        {
            // many user has many 'consultant project' records associated
            $relations['allConsultantProject']=array(self::MANY_MANY,'Project',
                User2Project::model()->tableName().'(userId,projectId)',
                'on'=>"`allConsultantProject_User_ConsultantProject`.`role`='".User2Project::CONSULTANT."'",
                'order'=>"`allConsultantProject_User_ConsultantProject`.`projectPriority` ASC, `allConsultantProject_User_ConsultantProject`.`id` ASC",
                'alias'=>'User_ConsultantProject'
            );
            // many user has many 'manager project' records associated
            $relations['allManagerProject']=array(self::MANY_MANY,'Project',
                User2Project::model()->tableName().'(userId,projectId)',
                'on'=>"allManagerProject_User_ManagerProject.`role`='".User2Project::MANAGER."'",
                'order'=>"allManagerProject_User_ManagerProject.`projectPriority` ASC, allManagerProject_User_ManagerProject.`id` ASC",
                'alias'=>'User_ManagerProject'
            );
            // many user has many 'project' records associated
            $relations['allProject']=array(self::MANY_MANY,'Project',
                User2Project::model()->tableName().'(userId,projectId)',
                'order'=>"`allProject_User_Project`.`projectPriority` ASC, `allProject_User_Project`.`id` ASC",
                'alias'=>'User_Project'
            );
        }
        if(file_exists(Yii::app()->basePath.'/models/User2Task.php'))
        {
            // one user has many 'consultant2task' records associated
            $relations['allConsultant2Task']=array(self::HAS_MANY,'User2Task','userId',
                'on'=>"`t`.`role`='".User2Task::CONSULTANT."'",
                'order'=>"`t`.`taskPriority` ASC, `t`.`id` ASC",
                'alias'=>'User_Consultant2Task'
            );
            // one user has many 'manager2task' records associated
            $relations['allManager2Task']=array(self::HAS_MANY,'User2Task','userId',
                'on'=>"`t`.`role`='".User2Task::MANAGER."'",
                'order'=>"`t`.`taskPriority` ASC, `t`.`id` ASC",
                'alias'=>'User_Manager2Task'
            );
            // one user has many 'user2task' records associated
            $relations['allUser2Task']=array(self::HAS_MANY,'User2Task','userId',
                'order'=>"`t`.`taskPriority` ASC, `t`.`id` ASC",
                'alias'=>'User_User2Task'
            );
        }
        if(file_exists(Yii::app()->basePath.'/models/Task.php') && file_exists(Yii::app()->basePath.'/models/User2Task.php'))
        {
            // many user has many 'consultant task' records associated
            $relations['allConsultantTask']=array(self::MANY_MANY,'Task',
                User2Task::model()->tableName().'(userId,taskId)',
                'on'=>"`allConsultantTask_User_ConsultantTask`.`role`='".User2Project::CONSULTANT."'",
                'order'=>"`allConsultantTask_User_ConsultantTask`.`taskPriority` ASC, `allConsultantTask_User_ConsultantTask`.`id` ASC",
                'alias'=>'User_ConsultantTask'
            );
            // one user has a number of 'consultant task' records associated
            $relations['consultantTaskCount']=array(self::STAT,'Task',
                User2Task::model()->tableName().'(userId,taskId)',
                //'on'=>"`t`.`role`='".User2Project::CONSULTANT."'",
            );
            // many user has many 'manager task' records associated
            $relations['allManagerTask']=array(self::MANY_MANY,'Task',
                User2Task::model()->tableName().'(userId,taskId)',
                'on'=>"`allManagerTask_User_ManagerTask`.`role`='".User2Task::MANAGER."'",
                'order'=>"`allManagerTask_User_ManagerTask`.`taskPriority` ASC, `allManagerTask_User_ManagerTask`.`id` ASC",
                'alias'=>'User_ManagerTask'
            );
        }
        if(file_exists(Yii::app()->basePath.'/models/Time.php'))
        {
            // one user has many 'consultant time' records associated
            $relations['allConsultantTime']=array(self::HAS_MANY,'Time','consultantId',
                'order'=>"`t`.`id` ASC",
                'alias'=>'User_ConsultantTime'
            );
            // one user has one of many 'consultant time' records associated
            //$relations['oneConsultantTime']=array(self::HAS_ONE,'Time','consultantId',
                //'alias'=>'User_OneConsultantTime'
            //);
            // one user has a number of 'consultant time' records associated
            $relations['consultantTimeCount']=array(self::STAT,'Time','consultantId');
            // one user has many 'manager time' records associated
            $relations['allManagerTime']=array(self::HAS_MANY,'Time','managerId',
                'order'=>"`t`.`id` ASC",
                'alias'=>'User_ManagerTime'
            );
            // one user has one of many 'manager time' records associated
            //$relations['oneManagerTime']=array(self::HAS_ONE,'Time','managerId',
                //'alias'=>'User_OneManagerTime'
            //);
            // one user has a number of 'manager time' records associated
            $relations['managerTimeCount']=array(self::STAT,'Time','managerId');
        }
        return $relations;
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
    protected function beforeValidate()
    {
        $scenario=$this->getScenario();
        if(($scenario==='create' || $scenario==='register') && $this->screenNameSame && $this->username!=='' && $this->username!==null)
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
        if($scenario==='update' && isset($_POST[__CLASS__]['email']) && $this->email!==$_POST[__CLASS__]['email'])
        {
            // email is being updated
            $this->email=$_POST[__CLASS__]['email'];
            $this->details->isEmailConfirmed=UserDetails::EMAIL_IS_NOT_CONFIRMED;
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
        return parent::beforeValidate();
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

    /*
     * Set userId to 0 @ forum tables to avoid link creation to profile
     */
    protected function afterDelete() {
        forumTopics::model()->updateAll(array('userId'=>0),'`userId`='.$this->id);
        forumPosts::model()->updateAll(array('userId'=>0),'`userId`='.$this->id);
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
                switch($this->$attribute)
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
                        return $this->$attribute;
                }
            case 'interface':
                $availableInterfaces=MParams::getAvailableInterfaces();
                if((is_string($this->$attribute) || is_int($this->$attribute)) && array_key_exists($this->$attribute,$availableInterfaces))
                    return Yii::t('ui',$availableInterfaces[$this->$attribute]);
                return $this->$attribute;
            case 'isActive':
                switch($this->$attribute)
                {
                    case self::IS_ACTIVE:
                        return Yii::t('attr','Yes (Member account is On)');
                    case self::IS_NOT_ACTIVE:
                        return Yii::t('attr','No (Member account is Off)');
                    case null:
                        return Yii::t('attr','By default (Member account is On)');
                    default:
                        return $this->$attribute;
                }
            case 'language':
                $availableLanguages=MParams::getAvailableLanguages();
                if((is_string($this->$attribute) || is_int($this->$attribute)) && array_key_exists($this->$attribute,$availableLanguages))
                    return Yii::t('t',$availableLanguages[$this->$attribute]);
                return $this->$attribute;
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
     * Checks if the given password is correct.
     * @param string the password to be validated
     * @return boolean whether the password is valid
     */
    public function validatePassword($password)
    {
        return $this->hashPassword($password,$this->salt)===$this->password;
    }

    /**
     * Generates the password hash.
     * @param string password
     * @param string salt
     * @return string hash
     */
    public function hashPassword($password,$salt)
    {
        return md5($salt.$password);
    }

    /**
     * Generates a salt that can be used to generate a password hash.
     * @return string the salt
     */
    protected function generateSalt()
    {
        return md5(microtime());
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
        $criteria->condition="`t`.`isActive` IS NULL OR `t`.`isActive` != '".self::IS_NOT_ACTIVE."'";
        if($id)
            $criteria->condition.=" OR `t`.`id` = '$id'";
        return self::model()->findAll($criteria);
    }

    /**
     * Whether user is me, current model is my member account.
     * @return boolean
     */
    public function getIsMe()
    {
        return !Yii::app()->user->isGuest && $this->id===Yii::app()->user->id;
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
}