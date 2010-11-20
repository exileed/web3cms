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
        if(LoginForm::getLoggingWithField()==='username')
            $user=User::model()->findByAttributes(array('username'=>$this->username));
        else if(LoginForm::getLoggingWithField()==='email')
            $user=User::model()->findByAttributes(array('email'=>$this->username));
        else if(LoginForm::getLoggingWithField()==='usernameOrEmail')
            $user=User::model()->find("`username`=? OR `email`=?",array($this->username,$this->username));
        if($user===null)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if(!$user->validatePassword($this->password))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else if($user->isActive===User::IS_NOT_ACTIVE)
            $this->errorCode=self::ERROR_ACCOUNT_IS_INACTIVE;
        else if(MArea::isBackend() && $user->accessType!==User::ADMINISTRATOR)
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
        else if($user->isActive===User::IS_NOT_ACTIVE)
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
     * Role -> task -> operation are essentially the same thing,
     * as you can see in the code they are of class {@link CAuthItem}.
     * With operations:
     *   $bizRule='return Yii::app()->user->id==$params["model"]->id;';
     *   $task=$auth->createTask('user/updateOwn','update own model',$bizRule);
     *   $task->addChild('user/update');
     * we can check using just:
     *   Yii::app()->user->checkAccess('user/update',array('model'=>$this->loadModel()));
     * Another example is {@link UserController::actionUpdateInterface()}.
     * For more info see: http://www.yiiframework.com/doc/guide/topics.auth
     * @param User model
     */
    private function authorize($user)
    {
        $auth=Yii::app()->authManager;
        // step one. destroy rbac object from previous save
        $auth->clearAll();
        // describe existing operations
        $auth->createOperation('company/create','create a company record');
        $auth->createOperation('company/grid','browse company grid');
        $auth->createOperation('company/list','browse company list');
        $auth->createOperation('company/show','show a company record');
        $auth->createOperation('company/update','update a company record');
        $auth->createOperation('companyPayment/create','create a company payment record');
        $auth->createOperation('companyPayment/grid','browse company payment grid');
        $auth->createOperation('companyPayment/list','browse company payment list');
        $auth->createOperation('companyPayment/show','show a company payment record');
        $auth->createOperation('companyPayment/update','update a company payment record');
        $auth->createOperation('expense/create','create an expense record');
        $auth->createOperation('expense/delete','delete an expense record');
        $bizRule='return is_object($params["model"]) && $params["model"]->invoiceId==0;';
        $task=$auth->createTask('expense/deleteWhenInvoiceIsNotSet','delete an expense record not associated with any invoice yet',$bizRule);
        $task->addChild('expense/delete');
        $auth->createOperation('expense/grid','browse expense grid');
        $auth->createOperation('expense/list','browse expense list');
        $auth->createOperation('expense/show','show an expense record');
        $auth->createOperation('expense/update','update an expense record');
        $bizRule='return is_object($params["model"]) && $params["model"]->invoiceId==0;';
        $task=$auth->createTask('expense/updateWhenInvoiceIsNotSet','update an expense record not associated with any invoice yet',$bizRule);
        $task->addChild('expense/update');
        $auth->createOperation('invoice/create','create an invoice record');
        $auth->createOperation('invoice/grid','browse invoice grid');
        $auth->createOperation('invoice/list','browse invoice list');
        $auth->createOperation('invoice/show','show an invoice record');
        $auth->createOperation('invoice/update','update an invoice record');
        $auth->createOperation('location/create','create a location record');
        $auth->createOperation('location/grid','browse location grid');
        $auth->createOperation('location/list','browse location list');
        $auth->createOperation('location/show','show a location record');
        $auth->createOperation('location/update','update a location record');
        $auth->createOperation('project/create','create a project record');
        $auth->createOperation('project/grid','browse project grid');
        $auth->createOperation('project/list','browse project list');
        $auth->createOperation('project/show','show a project record');
        $auth->createOperation('project/update','update a project record');
        $auth->createOperation('task/create','create a task record');
        $auth->createOperation('task/grid','browse task grid');
        $auth->createOperation('task/list','browse task list');
        $auth->createOperation('task/show','show a task record');
        $auth->createOperation('task/update','update a task record');
        $auth->createOperation('time/create','create a time record');
        $auth->createOperation('time/delete','delete a time record');
        $bizRule='return is_object($params["model"]) && $params["model"]->invoiceId==0;';
        $task=$auth->createTask('time/deleteWhenInvoiceIsNotSet','delete a time record not associated with any invoice yet',$bizRule);
        $task->addChild('time/delete');
        $auth->createOperation('time/grid','browse time grid');
        $auth->createOperation('time/list','browse time list');
        $auth->createOperation('time/report','browse time report');
        $auth->createOperation('time/show','show a time record');
        $auth->createOperation('time/update','update a time record');
        $bizRule='return is_object($params["model"]) && $params["model"]->invoiceId==0;';
        $task=$auth->createTask('time/updateWhenInvoiceIsNotSet','update a time record not associated with any invoice yet',$bizRule);
        $task->addChild('time/update');
        $auth->createOperation('user/create','create an user record');
        $auth->createOperation('user/grid','browse user grid');
        $auth->createOperation('user/list','browse user list');
        $auth->createOperation('user/show','show an user record');
        $bizRule='return Yii::app()->user->id==$params["model"]->id;';
        $task=$auth->createTask('user/showOwn','show user own model',$bizRule);
        $task->addChild('user/show');
        $auth->createOperation('user/update','update an user record');
        $bizRule='return Yii::app()->user->id==$params["model"]->id;';
        $task=$auth->createTask('user/updateOwn','update user own model',$bizRule);
        $task->addChild('user/update');
        $auth->createOperation('user/updateInterface','update an user interface');
        $bizRule='return Yii::app()->user->id==$params["model"]->id;';
        $task=$auth->createTask('user/updateOwnInterface','update user own interface',$bizRule);
        $task->addChild('user/updateInterface');
        // set relations between roles, tasks, operations
        $role=$auth->createRole(User::MEMBER);
        $role->addChild('user/showOwn'); // FIXME: assign all 'Own' once to default/common role rather than many times to each role
        $role->addChild('user/updateOwn');
        $role->addChild('user/updateOwnInterface');
        $role=$auth->createRole(User::CLIENT);
        $role->addChild('company/grid');
        $role->addChild('company/list');
        $role->addChild('company/show');
        $role->addChild('companyPayment/grid');
        $role->addChild('companyPayment/list');
        $role->addChild('companyPayment/show');
        $role->addChild('expense/grid');
        $role->addChild('expense/list');
        $role->addChild('expense/show');
        $role->addChild('invoice/grid');
        $role->addChild('invoice/list');
        $role->addChild('invoice/show');
        $role->addChild('project/grid');
        $role->addChild('project/list');
        $role->addChild('project/show');
        $role->addChild('task/grid');
        $role->addChild('task/list');
        $role->addChild('task/show');
        $role->addChild('time/grid');
        $role->addChild('time/list');
        $role->addChild('time/report');
        $role->addChild('time/show');
        $role->addChild('user/showOwn');
        $role->addChild('user/updateOwn');
        $role->addChild('user/updateOwnInterface');
        $role=$auth->createRole(User::CONSULTANT);
        $role->addChild('location/create');
        $role->addChild('location/grid');
        $role->addChild('location/list');
        $role->addChild('location/show');
        $role->addChild('location/update');
        $role->addChild('project/grid');
        $role->addChild('project/list');
        $role->addChild('project/show');
        $role->addChild('task/grid');
        $role->addChild('task/list');
        $role->addChild('task/show');
        $role->addChild('time/grid');
        $role->addChild('time/list');
        $role->addChild('time/report');
        $role->addChild('time/show');
        $role->addChild('user/showOwn');
        $role->addChild('user/updateOwn');
        $role->addChild('user/updateOwnInterface');
        $role=$auth->createRole(User::MANAGER);
        $role->addChild(User::CONSULTANT);
        $role->addChild('company/create');
        $role->addChild('company/grid');
        $role->addChild('company/list');
        $role->addChild('company/show');
        $role->addChild('companyPayment/grid');
        $role->addChild('companyPayment/list');
        $role->addChild('companyPayment/show');
        $role->addChild('expense/deleteWhenInvoiceIsNotSet');
        $role->addChild('expense/updateWhenInvoiceIsNotSet');
        $role->addChild('expense/grid');
        $role->addChild('expense/list');
        $role->addChild('expense/show');
        $role->addChild('invoice/grid');
        $role->addChild('invoice/list');
        $role->addChild('invoice/show');
        $role->addChild('task/create');
        $role->addChild('task/update');
        $role->addChild('time/create');
        $role->addChild('time/deleteWhenInvoiceIsNotSet');
        $role->addChild('time/updateWhenInvoiceIsNotSet');
        $role->addChild('user/grid');
        $role->addChild('user/list');
        $role->addChild('user/show');
        $role=$auth->createRole(User::ADMINISTRATOR);
        $role->addChild(User::MANAGER);
        $role->addChild('company/update');
        $role->addChild('companyPayment/create');
        $role->addChild('companyPayment/update');
        $role->addChild('expense/create');
        $role->addChild('expense/delete');
        $role->addChild('expense/update');
        $role->addChild('invoice/create');
        $role->addChild('invoice/update');
        $role->addChild('project/create');
        $role->addChild('project/update');
        $role->addChild('time/delete');
        $role->addChild('time/update');
        $role->addChild('user/create');
        $role->addChild('user/update');
        $role->addChild('user/updateInterface');
        // assign user his access type as role
        $auth->assign($user->accessType,$user->id);
        // last step. save
        $auth->save();
    }
}