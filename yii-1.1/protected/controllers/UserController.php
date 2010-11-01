<?php

class UserController extends _CController
{
    /**
     * @var string specifies the default action to be 'grid'.
     */
    public $defaultAction='grid';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            //array('deny', // deny authenticated user to perform 'login' actions
                //'actions'=>array('login'),
                //'users'=>array('@'),
            //),
            array('allow', // allow all users to perform 'captcha', 'confirmEmail', 'login', 'logout', 'register' and 'show' actions
                'actions'=>array('captcha','confirmEmail','login','logout','register','show'),
                'users'=>array('*'),
            ),
            array('allow', // following actions are checked by {@link checkAccessBeforeAction}
                'actions'=>array('create','grid','gridData','list','update','updateInterface'),
                'users'=>array('*'),
            ),
            /*array('allow', // allow authenticated user to perform 'create' actions
                'actions'=>array('create'),
                'users'=>array('@'),
            ),*/
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image
            // this is used by the register page
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xFFFFFF,
            ),
        );
    }

    /**
     * Returns id of the model that should be now loaded.
     * @return mixed int or numeric string of the model to be loaded or null.
     */
    public function loadModelId()
    {
        if(isset($_GET['id']))
            $id=$_GET['id'];
        else if(isset($_POST['id']))
            $id=$_POST['id'];
        else if(!Yii::app()->user->isGuest)
            $id=Yii::app()->user->id;
        else
            $id=null;
        return $id;
    }

    /**
     * Returns array of associated records which this model should be loaded with.
     * @return array of models to load this model with. 
     */
    public function loadModelWith()
    {
        return array('details');
    }

    /**
     * Confirm email address.
     */
    public function actionConfirmEmail()
    {
        $done=false;
        if(isset($_GET['email'],$_GET['key']) && !isset($_POST['User']))
        {
            // scenario 1: confirm using url
            $model=new User('confirmEmailUrl');
            // parse url parameters (from the link in the 'welcome' email)
            if(isset($_GET['email']))
                $model->email=$_GET['email'];
            if(isset($_GET['key']))
                $model->emailConfirmationKey=$_GET['key'];
        }
        else
        {
            // scenario 2: confirm using form
            $model=new User($this->action->id);
            if(isset($_POST['User']))
                // collect user input data
                $model->attributes=$_POST['User'];
        }
        // attempt to confirm email
        if((isset($_POST['User']) || isset($_GET['email'],$_GET['key'])) && $model->validate())
        {
            // find user by email
            if(($user=User::model()->with('details')->findByAttributes(array('email'=>$model->email)))!==null)
            {
                if(is_object($user->details))
                {
                    // explicitly set model scenario to be current action
                    $user->details->setScenario($this->action->id);
                    if($user->details->isEmailConfirmed===UserDetails::EMAIL_IS_CONFIRMED)
                        // was confirmed earlier
                        MUserFlash::setTopInfo(Yii::t('hint',
                            'Email address {email} was confirmed earlier.',
                            array('{email}'=>MHtml::wrapInTag($user->email,'strong'))
                        ));
                    else
                    {
                        if($user->details->emailConfirmationKey!==$model->emailConfirmationKey)
                            // wrong key
                            MUserFlash::setTopError(Yii::t('hint',
                                'We are sorry, but email address {email} has a different confirmation key. You provided: {emailConfirmationKey}.',
                                array(
                                    '{email}'=>MHtml::wrapInTag($user->email,'strong'),
                                    '{emailConfirmationKey}'=>MHtml::wrapInTag($model->emailConfirmationKey,'strong'),
                                )
                            ));
                        else
                        {
                            // confirm email
                            if($user->details->saveAttributes(array('isEmailConfirmed'=>UserDetails::EMAIL_IS_CONFIRMED)))
                            {
                                // set success message
                                MUserFlash::setTopSuccess(Yii::t('hint',
                                    'Email address {email} has been successfully confirmed.',
                                    array('{email}'=>MHtml::wrapInTag($user->email,'strong'))
                                ));
                                // renew key in db
                                $user->details->saveAttributes(array('emailConfirmationKey'=>$user->details->generateConfirmationKey()));
                                // clear form values
                                $model=new User($this->action->id);
                                // variable for view
                                $done=true;
                            }
                            else
                            {
                                // set error message
                                MUserFlash::setTopError(Yii::t('hint',
                                    'Error! Email address {email} could not be confirmed.',
                                    array('{email}'=>MHtml::wrapInTag($user->email,'strong'))
                                ));
                                Yii::log(W3::t('system',
                                    'Could not save attributes of the {model} model. Model ID: {modelId}. Method called: {method}.',
                                    array('{model}'=>get_class($user->details),'{modelId}'=>$user->details->userId,'{method}'=>__METHOD__.'()')
                                ),'error','w3');
                            }
                        }
                    }
                }
                else
                {
                    // hmmm, user details does not exists
                    MUserFlash::setTopError(Yii::t('hint','System failure! Please accept our apologies...'));
                    Yii::log(W3::t('system',
                        'Member with ID {userId} has no UserDetails record associated. Method called: {method}.',
                        array(
                            '{userId}'=>$user->id,
                            '{method}'=>__METHOD__.'()'
                        )
                    ),'error','w3');
                }
            }
            else
            {
                // email is not registered?
                MUserFlash::setTopInfo(Yii::t('hint',
                    'A member account with email address {email} could not be found.',
                    array('{email}'=>MHtml::wrapInTag($model->email,'strong'))
                ));
                // pay visitor attention to the 'email' field
                $model->addError('email','');
            }
        }
        // display the confirm email form
        $this->render($this->action->id,array('model'=>$model,'done'=>$done));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'show' page.
     */
    public function actionCreate()
    {
        $model=new User($this->action->id);
        if(isset($_POST['User']))
        {
            // collect user input data
            $model->attributes=$_POST['User'];
            // instantiate a new user details object
            $model->details=new UserDetails($this->action->id);
            $model->details->emailConfirmationKey=$model->details->generateConfirmationKey();
            if(isset($_POST['UserDetails']))
                $model->details->attributes=$_POST['UserDetails'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // save user details record
                $model->details->userId=$model->id;
                if($model->details->save(false)===false)
                    // hmmm, what could be the problem?
                    Yii::log(W3::t('system',
                        'Failed creating UserDetails record. Member ID: {userId}. Method called: {method}.',
                        array(
                            '{userId}'=>$model->id,
                            '{method}'=>__METHOD__.'()'
                        )
                    ),'error','w3');
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The new "{screenName}" member record has been successfully created.',
                    array('{screenName}'=>MHtml::wrapInTag($model->screenName,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        else
        {
            // pre-assigned attributes (default values for a new record)
            $model->interface=MParams::getInterface();
            $model->language=MParams::getLanguage();
            $model->screenNameSame=true;
        }
        if(!isset($model->details))
            // new associated user details
            $model->details=new UserDetails($this->action->id);
        // display the create form
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $form=new UserLoginForm;
        // collect user input data
        if(isset($_POST['UserLoginForm']))
        {
            if(isset($_POST['UserLoginForm']['loginWithField']))
            {
                // if user is logging with email, but param changed to username,
                // we should try to log him in with email.
                // if login attempt is unsuccessful, he will have to try again with username
                UserLoginForm::$loginWithField=$_POST['UserLoginForm']['loginWithField'];
                unset($_POST['UserLoginForm']['loginWithField']);
            }
            $form->attributes=$_POST['UserLoginForm'];
            // validate user input and redirect to return page if valid
            if($form->validate())
            {
                // set the welcome message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    '{screenName}, you have been successfully logged in.',
                    array('{screenName}'=>MHtml::wrapInTag(Yii::app()->user->screenName,'strong'))
                ));
                // user was just authenticated, but let's check anyway
                if(!Yii::app()->user->isGuest)
                {
                    // update user stats
                    if(($userDetails=UserDetails::model()->findByPk(Yii::app()->user->id))!==null)
                        $userDetails->saveAttributes(array(
                            'lastLoginTime'=>time(),
                            'lastVisitTime'=>time(),
                            'totalTimeLoggedIn'=>$userDetails->totalTimeLoggedIn+60
                        ));
                    else
                        // hmmm, user details does not exists
                        Yii::log(W3::t('system',
                            'Member with ID {userId} has no UserDetails record associated. Method called: {method}.',
                            array(
                                '{userId}'=>Yii::app()->user->id,
                                '{method}'=>__METHOD__.'()'
                            )
                        ),'error','w3');
                }
                $this->redirect($this->getGotoUrl());
            }
        }
        if(!Yii::app()->user->isGuest)
            // warn user if already logged in
            MUserFlash::setTopInfo(Yii::t('hint',
                '{screenName}, this action will log you out from your current account.',
                array('{screenName}'=>MHtml::wrapInTag(Yii::app()->user->screenName,'strong'))
            ));
        // display the login form
        $this->render($this->action->id,array('form'=>$form));
    }

    /**
     * Logout the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        $isLoggedIn=!Yii::app()->user->isGuest;
        $screenName=$isLoggedIn ? Yii::app()->user->screenName : '';
        // log user out and destroy all session data
        // if you want to keep the session alive, then use Yii::app()->user->logout(false) instead
        Yii::app()->user->logout();
        // if user was logged in, we should notify about logout
        if($isLoggedIn)
        {
            if(!Yii::app()->getSession()->getIsStarted())
                // if session is destroyed, we need to re-open it. this is necessary for user flash
                Yii::app()->getSession()->open();
            // set the goodbye message
            MUserFlash::setTopInfo(Yii::t('hint',
                '{screenName}, you have been successfully logged out.',
                array('{screenName}'=>MHtml::wrapInTag($screenName,'strong'))
            ));
        }
        $this->redirect($this->getGotoUrl());
    }

    /**
     * Register a new member account.
     * If creation is successful, the browser will be redirected to the 'login' page.
     */
    public function actionRegister()
    {
        $model=new User($this->action->id);
        // collect user input data
        if(isset($_POST['User']))
        {
            // collect user input data
            $model->attributes=$_POST['User'];
            // instantiate a new user details object
            $model->details=new UserDetails($this->action->id);
            $model->details->emailConfirmationKey=$model->details->generateConfirmationKey();
            if(isset($_POST['UserDetails']))
                $model->details->attributes=$_POST['UserDetails'];
            // validate with the current action as scenario
            if(($validated=$model->validate())!==false)
            {
                // if user is logged in
                if(!Yii::app()->user->isGuest)
                {
                    // if you place this code before validate() then verifyCode will be invalid
                    // log user out from the current account
                    Yii::app()->user->logout();
                    if(!Yii::app()->getSession()->getIsStarted())
                        // restore http session. this is necessary for user flash messages
                        Yii::app()->getSession()->open();
                }
                // create user record (without validation)
                if(($saved=$model->save(false))!==false)
                {
                    // save user details record
                    $model->details->userId=$model->id;
                    if($model->details->save(false)===false)
                        // hmmm, what could be the problem?
                        Yii::log(W3::t('system',
                            'Failed creating UserDetails record. Member ID: {userId}. Method called: {method}.',
                            array(
                                '{userId}'=>$model->id,
                                '{method}'=>__METHOD__.'()'
                            )
                        ),'error','w3');
                    // set success message
                    MUserFlash::setTopSuccess(Yii::t('hint',
                        '{screenName}, your member account has been successfully created.',
                        array('{screenName}'=>MHtml::wrapInTag($model->screenName,'strong'))
                    ));
                    // send welcome email
                    $headers="From: ".MParams::getAdminEmailAddress()."\r\nReply-To: ".MParams::getAdminEmailAddress();
                    $content=Yii::t('email',
                        'Content(New member account)',
                        array(
                            '{siteTitle}'=>MParams::getSiteTitle(),
                            '{screenName}'=>$model->screenName,
                            '{emailConfirmationKey}'=>$model->details->emailConfirmationKey,
                            '{emailConfirmationLink}'=>Yii::app()->createAbsoluteUrl($this->id.'/confirmEmail',array('email'=>$model->email,'key'=>$model->details->emailConfirmationKey)),
                        )
                    );
                    $sent=@mail($model->email,Yii::t('email','New member account'),$content,$headers);
                    // log email
                    Yii::log($model->email.' '."\t".'New member account',$sent?'sent':'not-sent','email');
                    Yii::log($model->email."\n".'Subject: New member account'."\n".'Content: '.$content."\n".'Headers: '.$headers,$sent?'sent':'not-sent','email-details');
                    // go to login page
                    $this->redirect($this->getGotoUrl());
                }
            }
        }
        else
        {
            // pre-assigned attributes (default values for a new record)
            $model->screenNameSame=true;
            $model->language=MParams::getLanguage();
            $model->interface=MParams::getInterface();
        }
        if(!Yii::app()->user->isGuest)
            // warn user if already logged in
            MUserFlash::setTopInfo(Yii::t('hint',
                '{screenName}, this action will log you out from your current account.',
                array('{screenName}'=>MHtml::wrapInTag(Yii::app()->user->screenName,'strong'))
            ));
        if(!isset($model->details))
            // new associated user details
            $model->details=new UserDetails($this->action->id);
        // render the view file
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Shows a particular model.
     */
    public function actionShow()
    {
        $pkIsPassed=isset($_GET['id']);
        if(($model=$this->loadModel())===null)
        {
            // model not found
            if(!$pkIsPassed && Yii::app()->user->isGuest)
            {
                // visitor requested his member page, but he is not logged in
                MUserFlash::setTopError(Yii::t('hint','Your session has expired. Please authorize.'));
                Yii::app()->user->loginRequired();
            }
            else
                // just show a message if model is not found
                $this->render('notFound');
            return false;
        }
        // loaded user is me?
        if(!$model->isMe && !Yii::app()->user->checkAccess($this->route))
        {
            // access denied
            MUserFlash::setTopError(Yii::t('accessDenied',$this->route,array(1,'{id}'=>$model->id)));
            $this->redirect($this->getGotoUrl());
        }
        // render the view file
        $this->render($this->action->id,array('model'=>$model,'pkIsPassed'=>$pkIsPassed));
    }

    /**
     * Updates a particular model.
     * Accessible only to authenticated users and admin.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate()
    {
        $pkIsPassed=isset($_GET['id']);
        if(($model=$this->loadModel())===null)
        {
            // model not found
            MUserFlash::setTopError(Yii::t('modelNotFound',$this->id));
            $this->redirect($this->getGotoUrl());
        }
        // explicitly set model scenario to be current action
        //$model->setScenario($this->action->id);
        //if(is_object($model->details))
            //$model->details->setScenario($this->action->id);
        // whether data is passed
        if(isset($_POST['User']))
        {
            // collect user input data
            $model->attributes=$_POST['User'];
            $detailsCopy=$model->details;
            // email is assigned in {@link User::beforeValidate}
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                if($model->isMe)
                {
                    // update variables previously defined in {@link _CUserIdentity} class
                    // update user states in the session for {@link _CController::init}
                    Yii::app()->user->setState('language',$model->language);
                    // update user screenName, so we continue calling visitor right, 
                    Yii::app()->user->setState('screenName',$model->screenName);
                    // set user preferred language
                    if(!empty($model->language))
                        W3::setLanguage($model->language);
                    // we do not need to update user cookie any more because
                    // we overrode auto-login with {@link _CWebUser::restoreFromCookie}
                }
                // user details
                $details=array();
                if($model->isActive===User::IS_ACTIVE && $model->details->deactivationTime!==null) // FIXME: if null means active, then add || $model->isActive===null
                    $details['deactivationTime']=null;
                else if(($model->isActive===User::IS_NOT_ACTIVE || $model->isActive===null) && empty($model->details->deactivationTime))
                    $details['deactivationTime']=time();
                if(isset($_POST['UserDetails']) || count($details)>=1 || $model->details!=$detailsCopy)
                {
                    if(isset($_POST['UserDetails']))
                        // collect user input data
                        $model->details->attributes=$_POST['UserDetails'];
                    foreach($details as $attribute=>$value)
                        // set attributes outside of the form
                        $model->details->$attribute=$value;
                    // validate with the current action as scenario
                    if(($validated=$model->details->validate())!==false)
                    {
                        if(($saved=$model->details->save())!==false)
                        {
                            // set success message
                            MUserFlash::setTopSuccess(Yii::t('hint',
                                $model->isMe ?
                                    '{screenName}, your profile has been updated.' :
                                    'The member account "{screenName}" has been updated.'
                                ,
                                array('{screenName}'=>MHtml::wrapInTag($model->screenName,'strong'))
                            ));
                            // go to 'show' page
                            $this->redirect(($model->isMe&&!$pkIsPassed) ? array('show') : array('show','id'=>$model->id));
                        }
                        else
                        {
                            // set error message
                            MUserFlash::setTopError(Yii::t('hint',
                                $model->isMe ?
                                    'Error! {screenName}, your profile could not be updated.' :
                                    'Error! The member account "{screenName}" could not be updated.'
                                ,
                                array('{screenName}'=>MHtml::wrapInTag($model->screenName,'strong'))
                            ));
                            Yii::log(W3::t('system',
                                'Could not save attributes of the {model} model. Model ID: {modelId}. Method called: {method}.',
                                array('{model}'=>get_class($model->details),'{modelId}'=>$model->details->userId,'{method}'=>__METHOD__.'()')
                            ),'error','w3');
                        }
                    }
                }
            }
            else if($validated && !$saved)
            {
                // set error message
                MUserFlash::setTopError(Yii::t('hint',
                    $model->isMe ?
                        'Error! {screenName}, your profile could not be updated.' :
                        'Error! The member account "{screenName}" could not be updated.'
                    ,
                    array('{screenName}'=>MHtml::wrapInTag($model->screenName,'strong'))
                ));
                Yii::log(W3::t('system',
                    'Could not save attributes of the {model} model. Model ID: {modelId}. Method called: {method}.',
                    array('{model}'=>get_class($model),'{modelId}'=>$model->id,'{method}'=>__METHOD__.'()')
                ),'error','w3');
            }
        }
        // display the update form
        $this->render($this->action->id,array('model'=>$model,'pkIsPassed'=>$pkIsPassed));
    }

    /**
     * Update user interface.
     * Accessible only to authenticated users and admin.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdateInterface()
    {
        if(!Yii::app()->user->checkAccess($this->route,array('model'=>$this->loadModel())))
        {
            // access denied
            MUserFlash::setTopError(Yii::t('accessDenied',$this->route));
            $this->redirect($this->getGotoUrl());
        }
        $pkIsPassed=isset($_GET['id']);
        if(($model=$this->loadModel())===null)
        {
            // model not found
            MUserFlash::setTopError(Yii::t('modelNotFound',$this->id));
            $this->redirect($this->getGotoUrl());
        }
        // explicitly set model scenario to be current action
        $model->setScenario($this->action->id);
        if(is_object($model->details))
            $model->details->setScenario($this->action->id);
        // whether data is passed
        if(isset($_POST['User']))
        {
            // collect user input data
            $model->attributes=$_POST['User'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // take care of updateTime (this is not critical)
                $model->details->saveAttributes(array('updateTime'=>time()));
                // update variables first defined in {@link _CUserIdentity} class
                if($model->isMe)
                {
                    // update user states in the session for {@link _CController::init}
                    Yii::app()->user->setState('interface',$model->interface);
                    // set user preferred interface
                    if(!empty($model->interface))
                        W3::setInterface($model->interface);
                    // we do not need to update user cookie any more because
                    // we overrode auto-login with {@link _CWebUser::restoreFromCookie}
                }
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    $model->isMe ?
                        '{screenName}, new user interface has been applied.' :
                        'The user interface for member account "{screenName}" has been updated.'
                    ,
                    array('{screenName}'=>MHtml::wrapInTag($model->screenName,'strong'))
                ));
                // go to 'show' page
                $this->redirect($model->isMe ? array('show') : array('show','id'=>$model->id));
            }
            else if($validated && !$saved)
            {
                // set error message
                MUserFlash::setTopError(Yii::t('hint',
                    $model->isMe ?
                        'Error! {screenName}, new user interface could not be applied.' :
                        'Error! The user interface for member account "{screenName}" could not be updated.'
                    ,
                    array('{screenName}'=>MHtml::wrapInTag($model->screenName,'strong'))
                ));
                Yii::log(W3::t('system',
                    'Could not save attributes of the {model} model. Model ID: {modelId}. Method called: {method}.',
                    array('{model}'=>get_class($model),'{modelId}'=>$model->id,'{method}'=>__METHOD__.'()')
                ),'error','w3');
            }
        }
        // display the update form
        $this->render($this->action->id,array('model'=>$model,'pkIsPassed'=>$pkIsPassed));
    }

    /**
     * Lists all models.
     */
    public function actionList()
    {
        $criteria=new CDbCriteria;
        $criteria->order="`t`.`screenName`";

        $pages=new CPagination(User::model()->count($criteria));
        $pages->pageSize=self::LIST_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=User::model()->with('details')->findAll($criteria);

        $this->render($this->action->id,array(
            'models'=>$models,
            'pages'=>$pages,
        ));
    }

    /**
     * Grid of all models.
     */
    public function actionGrid()
    {
        // specify filter parameters
        $accessType=isset($_GET['accessType']) ? $_GET['accessType'] : null;
        if($accessType!=='all' && $accessType!==(string)User::MEMBER && $accessType!==(string)User::CLIENT && $accessType!==(string)User::CONSULTANT && $accessType!==(string)User::MANAGER && $accessType!==(string)User::ADMINISTRATOR)
            $accessType='all';
        $state=isset($_GET['state']) ? $_GET['state'] : null;
        if($state!=='all' && $state!=='active' && $state!=='inactive')
            $state='all';

        // criteria
        $criteria=new CDbCriteria;
        if($accessType===(string)User::MEMBER)
        {
            $criteria->addCondition("`t`.`accessType`=:member");
            $criteria->params[':member']=User::MEMBER;
        }
        else if($accessType===(string)User::CLIENT)
        {
            $criteria->addCondition("`t`.`accessType`=:client");
            $criteria->params[':client']=User::CLIENT;
        }
        else if($accessType===(string)User::CONSULTANT)
        {
            $criteria->addCondition("`t`.`accessType`=:consultant");
            $criteria->params[':consultant']=User::CONSULTANT;
        }
        else if($accessType===(string)User::MANAGER)
        {
            $criteria->addCondition("`t`.`accessType`=:manager");
            $criteria->params[':manager']=User::MANAGER;
        }
        else if($accessType===(string)User::ADMINISTRATOR)
        {
            $criteria->addCondition("`t`.`accessType`=:administrator");
            $criteria->params[':administrator']=User::ADMINISTRATOR;
        }
        if($state==='active')
        {
            $criteria->addCondition("(`t`.`isActive` IS NULL OR `t`.`isActive`!=:isNotActive)");
            $criteria->params[':isNotActive']=User::IS_NOT_ACTIVE;
        }
        else if($state==='inactive')
        {
            $criteria->addCondition("`t`.`isActive`=:isNotActive");
            $criteria->params[':isNotActive']=User::IS_NOT_ACTIVE;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'User_UserDetails')!==false)
            $with[]='details';
        if(count($with)>=1)
            $pages=new CPagination(User::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(User::model()->count($criteria));
        $pages->pageSize=self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('User');
        $sort->attributes=array(
            'accessType'=>array('asc'=>"`t`.`accessLevel`",'desc'=>"`t`.`accessLevel` desc",'label'=>User::model()->getAttributeLabel('accessType')),
            'createTime'=>array('asc'=>"`t`.`createTime`",'desc'=>"`t`.`createTime` desc",'label'=>User::model()->getAttributeLabel('Registered')),
            'email'=>array('asc'=>"`t`.`email`",'desc'=>"`t`.`email` desc",'label'=>User::model()->getAttributeLabel('email')),
            'screenName'=>array('asc'=>"`t`.`screenName`",'desc'=>"`t`.`screenName` desc",'label'=>User::model()->getAttributeLabel('screenName')),
            'deactivationTime'=>array('asc'=>"`User_UserDetails`.`deactivationTime`",'desc'=>"`User_UserDetails`.`deactivationTime` desc",'label'=>UserDetails::model()->getAttributeLabel('Deact')),
            'occupation'=>array('asc'=>"`User_UserDetails`.`occupation`",'desc'=>"`User_UserDetails`.`occupation` desc",'label'=>UserDetails::model()->getAttributeLabel('occupation')),
        );
        $sort->defaultOrder="`t`.`screenName`";
        $sort->applyOrder($criteria);

        // find all
        $models=User::model()->with('details')->findAll($criteria);

        // filters data
        $filters=array('accessType'=>$accessType,'state'=>$state);
        $allAccessType=array(
            array(
                'text'=>Yii::t('t','All'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('accessType'=>'all'))),
                'active'=>$accessType==='all'
            ),
            array(
                'text'=>Yii::t('t',User::MEMBER_T),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('accessType'=>User::MEMBER))),
                'active'=>$accessType===(string)User::MEMBER
            ),
            array(
                'text'=>Yii::t('t',User::CLIENT_T),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('accessType'=>User::CLIENT))),
                'active'=>$accessType===(string)User::CLIENT
            ),
            array(
                'text'=>Yii::t('t',User::CONSULTANT_T),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('accessType'=>User::CONSULTANT))),
                'active'=>$accessType===(string)User::CONSULTANT
            ),
            array(
                'text'=>Yii::t('t',User::MANAGER_T),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('accessType'=>User::MANAGER))),
                'active'=>$accessType===(string)User::MANAGER
            ),
            array(
                'text'=>Yii::t('t',User::ADMINISTRATOR_T),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('accessType'=>User::ADMINISTRATOR))),
                'active'=>$accessType===(string)User::ADMINISTRATOR
            ),
        );
        switch($accessType)
        {
            case 'all':
                $accessTypeLinkText=Yii::t('t','All access types');
                break;
            case (string)User::MEMBER:
                $accessTypeLinkText=Yii::t('t',User::MEMBER_T);
                break;
            case (string)User::CLIENT:
                $accessTypeLinkText=Yii::t('t',User::CLIENT_T);
                break;
            case (string)User::CONSULTANT:
                $accessTypeLinkText=Yii::t('t',User::CONSULTANT_T);
                break;
            case (string)User::MANAGER:
                $accessTypeLinkText=Yii::t('t',User::MANAGER_T);
                break;
            case (string)User::ADMINISTRATOR:
                $accessTypeLinkText=Yii::t('t',User::ADMINISTRATOR_T);
                break;
            default:
                $accessTypeLinkText='&nbsp;';
        }
        $allState=array(
            array(
                'text'=>Yii::t('t','All'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'all'))),
                'active'=>$state==='all'
            ),
            array(
                'text'=>Yii::t('t','Active[members]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'active'))),
                'active'=>$state==='active'
            ),
            array(
                'text'=>Yii::t('t','Inactive[members]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'inactive'))),
                'active'=>$state==='inactive'
            ),
        );
        switch($state)
        {
            case 'all':
                $stateLinkText=Yii::t('t','All states[member]');
                break;
            case 'active':
                $stateLinkText=Yii::t('t','Active[members]');
                break;
            case 'inactive':
                $stateLinkText=Yii::t('t','Inactive[members]');
                break;
            default:
                $stateLinkText='&nbsp;';
        }

        // rows for the static grid
        $gridRows=array();
        foreach($models as $model)
        {
            $gridRows[]=array(
                array(
                    'content'=>CHtml::encode($model->screenName),
                ),
                array(
                    'content'=>CHtml::encode($model->details->occupation),
                ),
                array(
                    'content'=>CHtml::encode($model->email),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->createTime,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->createTime,'full')),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->details->deactivationTime,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->details->deactivationTime,'full')),
                ),
                array(
                    'content'=>CHtml::encode($model->getAttributeView('accessType')),
                ),
                array(
                    'content'=>
                        CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                            'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                            'title'=>Yii::t('link','Show')
                        )).
                        CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                            'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-last ui-corner-all',
                            'title'=>Yii::t('link','Edit')
                        )),
                ),
            );
        }

        // render the view file
        $this->render($this->action->id,array(
            'models'=>$models,
            'pages'=>$pages,
            'sort'=>$sort,
            'accessType'=>$accessType,
            'state'=>$state,
            'filters'=>$filters,
            'allAccessType'=>$allAccessType,
            'accessTypeLinkText'=>$accessTypeLinkText,
            'allState'=>$allState,
            'stateLinkText'=>$stateLinkText,
            'gridRows'=>$gridRows,
        ));
    }

    /**
     * Print out array of models for the jqGrid rows.
     */
    public function actionGridData()
    {
        if(!Yii::app()->request->isPostRequest)
        {
            throw new CHttpException(400,Yii::t('http','Invalid request. Please do not repeat this request again.'));
            exit;
        }

        // specify request details
        $jqGrid=$this->processJqGridRequest();

        // specify filter parameters
        $accessType=isset($_GET['accessType']) ? $_GET['accessType'] : null;
        if($accessType!=='all' && $accessType!==(string)User::MEMBER && $accessType!==(string)User::CLIENT && $accessType!==(string)User::CONSULTANT && $accessType!==(string)User::MANAGER && $accessType!==(string)User::ADMINISTRATOR)
            $accessType='all';
        $state=isset($_GET['state']) ? $_GET['state'] : null;
        if($state!=='all' && $state!=='active' && $state!=='inactive')
            $state='all';

        // criteria
        $criteria=new CDbCriteria;
        if($jqGrid['searchField']!==null && $jqGrid['searchString']!==null && $jqGrid['searchOper']!==null)
        {
            $field=array(
                'accessType'=>"`t`.`accessType`",
                'createTime'=>"`t`.`createTime`",
                'email'=>"`t`.`email`",
                'screenName'=>"`t`.`screenName`",
                'deactivationTime'=>"`User_UserDetails`.`deactivationTime`",
                'occupation'=>"`User_UserDetails`.`occupation`",
            );
            $operation=$this->getJqGridOperationArray();
            $keywordFormula=$this->getJqGridKeywordFormulaArray();
            if(isset($field[$jqGrid['searchField']]) && isset($operation[$jqGrid['searchOper']]))
            {
                $criteria->condition='('.$field[$jqGrid['searchField']].' '.$operation[$jqGrid['searchOper']].' :keyword)';
                $criteria->params=array(':keyword'=>str_replace('keyword',$jqGrid['searchString'],$keywordFormula[$jqGrid['searchOper']]));
                // search by special field types
                if($jqGrid['searchField']==='createTime' && ($keyword=strtotime($jqGrid['searchString']))!==false)
                {
                    $criteria->params=array(':keyword'=>str_replace('keyword',$keyword,$keywordFormula[$jqGrid['searchOper']]));
                    if(date('H:i:s',$keyword)==='00:00:00')
                        // visitor is looking for a precision by day, not by second
                        $criteria->condition='(TO_DAYS(FROM_UNIXTIME('.$field[$jqGrid['searchField']].',"%Y-%m-%d")) '.$operation[$jqGrid['searchOper']].' TO_DAYS(FROM_UNIXTIME(:keyword,"%Y-%m-%d")))';
                }
            }
        }
        if($accessType===(string)User::MEMBER)
        {
            $criteria->addCondition("`t`.`accessType`=:member");
            $criteria->params[':member']=User::MEMBER;
        }
        else if($accessType===(string)User::CLIENT)
        {
            $criteria->addCondition("`t`.`accessType`=:client");
            $criteria->params[':client']=User::CLIENT;
        }
        else if($accessType===(string)User::CONSULTANT)
        {
            $criteria->addCondition("`t`.`accessType`=:consultant");
            $criteria->params[':consultant']=User::CONSULTANT;
        }
        else if($accessType===(string)User::MANAGER)
        {
            $criteria->addCondition("`t`.`accessType`=:manager");
            $criteria->params[':manager']=User::MANAGER;
        }
        else if($accessType===(string)User::ADMINISTRATOR)
        {
            $criteria->addCondition("`t`.`accessType`=:administrator");
            $criteria->params[':administrator']=User::ADMINISTRATOR;
        }
        if($state==='active')
        {
            $criteria->addCondition("(`t`.`isActive` IS NULL OR `t`.`isActive`!=:isNotActive)");
            $criteria->params[':isNotActive']=User::IS_NOT_ACTIVE;
        }
        else if($state==='inactive')
        {
            $criteria->addCondition("`t`.`isActive`=:isNotActive");
            $criteria->params[':isNotActive']=User::IS_NOT_ACTIVE;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'User_UserDetails')!==false)
            $with[]='details';
        if(count($with)>=1)
            $pages=new CPagination(User::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(User::model()->count($criteria));
        $pages->pageSize=$jqGrid['pageSize']!==null ? $jqGrid['pageSize'] : self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('User');
        $sort->attributes=array(
            'accessType'=>array('asc'=>"`t`.`accessLevel`",'desc'=>"`t`.`accessLevel` desc",'label'=>User::model()->getAttributeLabel('accessType')),
            'createTime'=>array('asc'=>"`t`.`createTime`",'desc'=>"`t`.`createTime` desc",'label'=>User::model()->getAttributeLabel('Registered')),
            'email'=>array('asc'=>"`t`.`email`",'desc'=>"`t`.`email` desc",'label'=>User::model()->getAttributeLabel('email')),
            'screenName'=>array('asc'=>"`t`.`screenName`",'desc'=>"`t`.`screenName` desc",'label'=>User::model()->getAttributeLabel('screenName')),
            'deactivationTime'=>array('asc'=>"`User_UserDetails`.`deactivationTime`",'desc'=>"`User_UserDetails`.`deactivationTime` desc",'label'=>UserDetails::model()->getAttributeLabel('Deact')),
            'occupation'=>array('asc'=>"`User_UserDetails`.`occupation`",'desc'=>"`User_UserDetails`.`occupation` desc",'label'=>UserDetails::model()->getAttributeLabel('occupation')),
        );
        $sort->defaultOrder="`t`.`screenName`";
        $sort->applyOrder($criteria);

        // find all
        $models=User::model()->with('details')->findAll($criteria);

        // create resulting data array
        $data=array(
            'page'=>$pages->getCurrentPage()+1,
            'total'=>$pages->getPageCount(),
            'records'=>$pages->getItemCount(),
            'rows'=>array()
        );
        foreach($models as $model)
        {
            $data['rows'][]=array('id'=>$model->id,'cell'=>array(
                CHtml::encode($model->screenName),
                CHtml::encode($model->details->occupation),
                CHtml::encode($model->email),
                CHtml::encode(MDate::format($model->createTime,'medium',null)),
                CHtml::encode(MDate::format($model->details->deactivationTime,'medium',null)),
                CHtml::encode($model->getAttributeView('accessType')),
                CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                    'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                    'title'=>Yii::t('link','Show')
                )).
                CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                    'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-last ui-corner-all',
                    'title'=>Yii::t('link','Edit')
                )),
            ));
        }
        $this->printJson($data);
    }
}
