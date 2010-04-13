<?php
/**
 * _CUserIdentity represents the data needed to create identity of a user.
 * It contains the authentication method that checks if the provided
 * data can create identity of the user.
 */
class _CUserIdentity extends CUserIdentity
{
    const ERROR_UNKNOWN_IDENTITY=10;
    const ERROR_ACCOUNT_IS_INACTIVE=11;
    const ERROR_IS_NOT_ADMINISTRATOR=12;

    private $_id;

    /**
     * @return integer the ID of the user record
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Authenticates a user.
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        if(UserLoginForm::getLoggingWithField()==='username')
            $user=User::model()->findByAttributes(array('username'=>$this->username));
        else if(UserLoginForm::getLoggingWithField()==='email')
            $user=User::model()->findByAttributes(array('email'=>$this->username));
        else if(UserLoginForm::getLoggingWithField()==='usernameOrEmail')
            $user=User::model()->find("`username`=? OR `email`=?",array($this->username,$this->username));
        if($user===null)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if(!$user->validatePassword($this->password))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else if($user->isActive===User::IS_NOT_ACTIVE)
            $this->errorCode=self::ERROR_ACCOUNT_IS_INACTIVE;
        else if(MArea::isBackend() && !User::isAdministrator($user->accessType))
        {
            $this->errorCode=self::ERROR_IS_NOT_ADMINISTRATOR;
            Yii::app()->controller->var->userAccessType=$user->getAttributeView('accessType');
        }
        else
        {
            $this->_id=$user->id;
            $this->errorCode=self::ERROR_NONE;
            // do not store password or other sensitive data in the persistent storage
            // when (config/main.php) allowAutoLogin is true, because
            // all these data will be stored in cookie = it is readable
            $this->setState('email',$user->email);
            $this->setState('interface',$user->interface);
            $this->setState('language',$user->language);
            $this->setState('screenName',$user->screenName);
            // init rbac
            $this->authorize($user);
        }
        return $this->errorCode==self::ERROR_NONE;
    }

    /**
     * Authenticates a user by cookie.
     * Is called by {@link _CWebUser::restoreFromCookie()}.
     * @return boolean whether authentication succeeds.
     */
    public function authenticateByCookie()
    {
        $user=User::model()->findByPk($this->username);
        if($user===null)
            $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
        else if($user->isActive==='0')
            $this->errorCode=self::ERROR_ACCOUNT_IS_INACTIVE;
        else
        {
            $this->_id=$user->id;
            $this->errorCode=self::ERROR_NONE;
            // do not store password or other sensitive data in the persistent storage
            // when (config/main.php) allowAutoLogin is true, because
            // all these data will be stored in cookie = it is readable
            $this->setState('email',$user->email);
            $this->setState('interface',$user->interface);
            $this->setState('language',$user->language);
            $this->setState('screenName',$user->screenName);
            // init rbac
            $this->authorize($user);
        }
        return !$this->errorCode;
    }

    /**
     * Initialize Role-Based Access Control (RBAC).
     * @param User model
     */
    private function authorize($user)
    {
        $auth=Yii::app()->authManager;
        // first step. destroy rbac object from previous save
        $auth->clearAll();
        // describe existing operations
        $auth->createOperation('company/grid','browse company grid');
        $auth->createOperation('company/gridData','data for company grid');
        $auth->createOperation('company/list','browse company list');
        $auth->createOperation('companyPayment/grid','browse company payment grid');
        $auth->createOperation('companyPayment/gridData','data for company payment grid');
        $auth->createOperation('companyPayment/list','browse company payment list');
        $auth->createOperation('expense/grid','browse expense grid');
        $auth->createOperation('expense/gridData','data for expense grid');
        $auth->createOperation('expense/list','browse expense list');
        $auth->createOperation('invoice/grid','browse invoice grid');
        $auth->createOperation('invoice/gridData','data for invoice grid');
        $auth->createOperation('invoice/list','browse invoice list');
        $auth->createOperation('project/grid','browse project grid');
        $auth->createOperation('project/gridData','data for project grid');
        $auth->createOperation('project/list','browse project list');
        $auth->createOperation('task/grid','browse task grid');
        $auth->createOperation('task/gridData','data for task grid');
        $auth->createOperation('task/list','browse task list');
        $auth->createOperation('time/grid','browse time grid');
        $auth->createOperation('time/gridData','data for time grid');
        $auth->createOperation('time/list','browse time list');
        $auth->createOperation('user/grid','browse user grid');
        $auth->createOperation('user/gridData','data for user grid');
        $auth->createOperation('user/list','browse user list');
        // set relations between roles, tasks, operations
        $role=$auth->createRole(User::CLIENT);
        $role->addChild('company/grid');
        $role->addChild('company/gridData');
        $role->addChild('company/list');
        $role->addChild('companyPayment/grid');
        $role->addChild('companyPayment/gridData');
        $role->addChild('companyPayment/list');
        $role->addChild('expense/grid');
        $role->addChild('expense/gridData');
        $role->addChild('expense/list');
        $role->addChild('invoice/grid');
        $role->addChild('invoice/gridData');
        $role->addChild('invoice/list');
        $role->addChild('project/grid');
        $role->addChild('project/gridData');
        $role->addChild('project/list');
        $role->addChild('task/grid');
        $role->addChild('task/gridData');
        $role->addChild('task/list');
        $role->addChild('time/grid');
        $role->addChild('time/gridData');
        $role->addChild('time/list');
        $role=$auth->createRole(User::CONSULTANT);
        $role->addChild('project/grid');
        $role->addChild('project/gridData');
        $role->addChild('project/list');
        $role->addChild('task/grid');
        $role->addChild('task/gridData');
        $role->addChild('task/list');
        $role->addChild('time/grid');
        $role->addChild('time/gridData');
        $role->addChild('time/list');
        $role=$auth->createRole(User::MANAGER);
        $role->addChild(User::CONSULTANT);
        $role->addChild('company/grid');
        $role->addChild('company/gridData');
        $role->addChild('company/list');
        $role->addChild('companyPayment/grid');
        $role->addChild('companyPayment/gridData');
        $role->addChild('companyPayment/list');
        $role->addChild('expense/grid');
        $role->addChild('expense/gridData');
        $role->addChild('expense/list');
        $role->addChild('invoice/grid');
        $role->addChild('invoice/gridData');
        $role->addChild('invoice/list');
        $role->addChild('user/grid');
        $role->addChild('user/gridData');
        $role->addChild('user/list');
        $role=$auth->createRole(User::ADMINISTRATOR);
        $role->addChild(User::MANAGER);
        // assign user his access type as role
        $auth->assign($user->accessType,$user->id);
        // last step. save
        $auth->save();
    }
}