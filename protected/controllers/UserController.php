<?php

class UserController extends _CController
{
    const PAGE_SIZE=10;

    /**
     * @var string specifies the default action to be 'login'.
     */
    public $defaultAction='login';

    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;

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
            array('allow',  // allow all users to perform 'captcha', 'confirmEmail', 'list', 'login', 'logout', 'register' and 'show' actions
                'actions'=>array('captcha','confirmEmail','list','login','logout','register','show'),
                'users'=>array('*'),
            ),
            array('allow', // allow authenticated user to perform 'update' actions
                'actions'=>array('update'),
                'users'=>array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('admin','delete'),
                'users'=>array('admin'),
            ),
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
     * Confirm email address.
     */
    public function actionConfirmEmail()
    {
        $done=false;
        // use power of models
        $model=new User;
        // collect user input data
        if(isset($_POST['User']))
            // use the magic of safeAttributes()
            $model->attributes=$_POST['User'];
        else
        {
            // parse url parameters (from the link in the 'welcome' email)
            isset($_GET['email']) && ($model->email=$_GET['email']);
            isset($_GET['key']) && ($model->emailConfirmationKey=$_GET['key']);
        }
        // attempt to confirm email
        if((isset($_POST['User']) && $model->validate('confirmEmail')) || (!isset($_POST['User']) && isset($_GET['email'],$_GET['key']) && $model->validate('confirmEmailUrl')))
        {
            // find user by email
            if($user=User::model()->with('details')->findByAttributes(array('email'=>$model->email)))
            {
                if(is_object($user->details))
                {
                    if($user->details->isEmailConfirmed==='1')
                        // was confirmed earlier
                        MUserFlash::setTopInfo(Yii::t('user',
                            'Email address {email} was confirmed earlier.',
                            array('{email}'=>'<strong>'.$user->email.'</strong>')
                        ));
                    else
                    {
                        if($user->details->emailConfirmationKey!==$model->emailConfirmationKey)
                            // wrong key
                            MUserFlash::setTopError(Yii::t('user',
                                'We are sorry, but email address {email} has a different confirmation key. You provided: {emailConfirmationKey}.',
                                array(
                                    '{email}'=>'<strong>'.$user->email.'</strong>',
                                    '{emailConfirmationKey}'=>'<strong>'.$model->emailConfirmationKey.'</strong>',
                                )
                            ));
                        else
                        {
                            // confirm email
                            if($user->details->saveAttributes(array('isEmailConfirmed'=>'1')))
                            {
                                // set success message
                                MUserFlash::setTopSuccess(Yii::t('user',
                                    'Email address {email} has been successfully confirmed.',
                                    array('{email}'=>'<strong>'.$user->email.'</strong>')
                                ));
                                // renew key in db
                                $user->details->saveAttributes(array('emailConfirmationKey'=>md5(uniqid(rand(),true))));
                                // clear form values
                                $model=new User;
                                // variable for view
                                $done=true;
                            }
                            else
                            {
                                // set error message
                                MUserFlash::setTopError(Yii::t('user',
                                    'Error! Email address {email} could not be confirmed.',
                                    array('{email}'=>'<strong>'.$user->email.'</strong>')
                                ));
                                Yii::log(Yii::t('system',
                                    'Could not save attributes of the {model} model. Model ID: {modelId}. Method called: {method}.',
                                    array(
                                        '{model}'=>'UserDetails',
                                        '{modelId}'=>$user->details->userId,
                                        '{method}'=>__METHOD__.'()'
                                    )
                                ),'error','w3');
                            }
                        }
                    }
                }
                else
                {
                    // hmmm, user details does not exists
                    MUserFlash::setTopError(Yii::t('user','System failure! Please accept our apologies...'));
                    Yii::log(Yii::t('system',
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
                MUserFlash::setTopInfo(Yii::t('user',
                    'A member account with email address {email} could not be found.',
                    array('{email}'=>'<strong>'.$model->email.'</strong>')
                ));
                // pay visitor attention to 'email' field
                $model->addError('email','');
            }
        }
        // display the confirm email form
        $this->render('confirmEmail',array('model'=>$model,'done'=>$done));
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
            $form->attributes=$_POST['UserLoginForm'];
            if(isset($_POST['UserLoginForm']['loginWithField']))
                // if user is logging with email, but param changed to username,
                // we should try to log him in with email.
                // if login attempt is unsuccessful, he will have to try again with username
                UserLoginForm::$loginWithField=$_POST['UserLoginForm']['loginWithField'];
            // validate user input and redirect to return page if valid
            if($form->validate())
            {
                // set the welcome message
                MUserFlash::setTopSuccess(Yii::t('user',
                    '{screenName}, you have been successfully logged in.',
                    array('{screenName}'=>'<strong>'.Yii::app()->user->screenName.'</strong>')
                ));
                // user was just authenticated, but let's check anyway
                if(!Yii::app()->user->isGuest)
                {
                    // update user stats
                    if($userDetails=UserDetails::model()->findByPk(Yii::app()->user->id))
                        $userDetails->saveAttributes(array(
                            'lastLoginOn'=>date('Y-m-d H:i:s'),
                            'lastLoginGmtOn'=>gmdate('Y-m-d H:i:s'),
                            'lastSeenOn'=>date('Y-m-d H:i:s'),
                            'lastSeenGmtOn'=>gmdate('Y-m-d H:i:s'),
                            'totalTimeLoggedIn'=>$userDetails->totalTimeLoggedIn+60
                        ));
                    else
                        // hmmm, user details does not exists
                        Yii::log(Yii::t('system',
                            'Member with ID {userId} has no UserDetails record associated. Method called: {method}.',
                            array(
                                '{userId}'=>Yii::app()->user->id,
                                '{method}'=>__METHOD__.'()'
                            )
                        ),'error','w3');
                }
                // go to previous/profile page
                $url=Yii::app()->homeUrl==Yii::app()->user->returnUrl ? array('/user/show') : Yii::app()->user->returnUrl;
                $this->redirect($url);
            }
        }
        if(!Yii::app()->user->isGuest)
            // warn user if already logged in
            MUserFlash::setTopInfo(Yii::t('user',
                '{screenName}, this action will log you out from your current account.',
                array('{screenName}'=>'<strong>'.Yii::app()->user->screenName.'</strong>')
            ));
        // display the login form
        $this->render('login',array('form'=>$form));
    }

    /**
     * Logout the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        $isLoggedIn=!Yii::app()->user->isGuest;
        $screenName=$isLoggedIn? Yii::app()->user->screenName : '';
        // log user out and destroy all sessions
        Yii::app()->user->logout();
        if($isLoggedIn) // if user was logged in, we should notify of being logged out
        {
            if(!Yii::app()->getSession()->getIsStarted())
                // have to re-open session destroyed on logout. this is necessary for user flash
                Yii::app()->getSession()->open();
            // set the goodbye message
            MUserFlash::setTopInfo(Yii::t('user',
                '{screenName}, you have been successfully logged out.',
                array('{screenName}'=>'<strong>'.$screenName.'</strong>')
            ));
        }
        // go to home page
        $this->redirect(Yii::app()->homeUrl);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'login' page.
     */
    public function actionRegister()
    {
        $model=new User;
        // collect user input data
        if(isset($_POST['User']))
        {
            // use the magic of safeAttributes()
            $model->attributes=$_POST['User'];
            // validate with $on = 'register'
            if($model->validate('register'))
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
                // create user record
                if($model->save())
                {
                    // prepare data
                    $emailConfirmationKey=md5(uniqid(rand(),true));
                    // create user details record
                    $userDetails=new UserDetails;
                    $userDetails->userId=$model->id;
                    $userDetails->attributes=array(
                        'emailConfirmationKey'=>$emailConfirmationKey,
                    );
                    if(!$userDetails->save())
                        // hmmm, what could be the problem?
                        Yii::log(Yii::t('system',
                            'Failed creating UserDetails record. Member ID: {userId}. Method called: {method}.',
                            array(
                                '{userId}'=>$model->id,
                                '{method}'=>__METHOD__.'()'
                            )
                        ),'error','w3');
                    // set success message
                    MUserFlash::setTopSuccess(Yii::t('user',
                        '{screenName}, your member account has been successfully created.',
                        array('{screenName}'=>'<strong>'.$model->screenName.'</strong>')
                    ));
                    // send welcome email
                    $headers="From: ".MParams::getAdminEmailAddress()."\r\nReply-To: ".MParams::getAdminEmailAddress();
                    $content=Yii::t('email',
                        'Content(New member account)',
                        array(
                            '{siteTitle}'=>MParams::getSiteTitle(),
                            '{screenName}'=>$model->screenName,
                            '{emailConfirmationKey}'=>$emailConfirmationKey,
                            '{emailConfirmationLink}'=>Yii::app()->createAbsoluteUrl('user/confirmEmail',array('email'=>$model->email,'key'=>$emailConfirmationKey)),
                        )
                    );
                    @mail($model->email,Yii::t('email','New member account'),$content,$headers);
                    // created a user account? please, login...
                    $this->redirect(array('user/login'));
                }
            }
        }
        else
        {
            // pre-assigned user attributes
            $model->screenNameSame=true;
            $model->language=MParams::getLanguage();
            $model->cssTheme=MParams::getCssTheme();
        }
        if(!Yii::app()->user->isGuest)
            // warn user if already logged in
            MUserFlash::setTopInfo(Yii::t('user',
                '{screenName}, this action will log you out from your current account.',
                array('{screenName}'=>'<strong>'.Yii::app()->user->screenName.'</strong>')
            ));
        // display the register form
        $this->render('register',array('model'=>$model));
    }

    /**
     * Shows a particular model.
     */
    public function actionShow()
    {
        $me=(isset($_GET['id']) && (Yii::app()->user->isGuest || $_GET['id']!==Yii::app()->user->id)) ? false : true;
        $model=$this->loadUser($me ? Yii::app()->user->id : null);
        $this->render('show',array('model'=>$model,'me'=>$me,'admin'=>(!Yii::app()->user->isGuest && Yii::app()->user->id==='1')));
    }

    /**
     * Updates a particular model.
     * Accessible only to authenticated users.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate()
    {
        // if 'id' is set and it is not my id and i'm not the admin
        if(isset($_GET['id']) && $_GET['id']!==Yii::app()->user->id && Yii::app()->user->id!=='1')
            // only admin can update other users' profile
            unset($_GET['id']);
        $me=(isset($_GET['id'])/* || Yii::app()->user->isGuest*/) ? false : true;
        $model=$this->loadUser($me ? Yii::app()->user->id : null);
        if(isset($_POST['User']))
        {
            // use the magic of safeAttributes()
            $model->attributes=$_POST['User'];
            // validate with $on = 'update'
            if($model->validate('update'))
            {
                // update user record
                if($model->save())
                {
                    // me means my profile. alternative: admin update someone's profile
                    if($me)
                    {
                      // update user states for {@link _CController::init()}
                      Yii::app()->user->setState('cssTheme',$model->cssTheme);
                      Yii::app()->user->setState('language',$model->language);
                    }
                    // go to 'show' page
                    $this->redirect($me ? array('show') : array('show','id'=>$model->id));
                }
            }
        }
        // display the update form
        $this->render('update',array('model'=>$model,'me'=>$me,'screenName'=>$model->screenName));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'list' page.
     */
    /*public function actionDelete()
    {
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            $this->loadUser()->delete();
            $this->redirect(array('list'));
        }
        else
            throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
    }*/

    /**
     * Lists all models.
     */
    /*public function actionList()
    {
        $criteria=new CDbCriteria;

        $pages=new CPagination(User::model()->count($criteria));
        $pages->pageSize=self::PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=User::model()->findAll($criteria);

        $this->render('list',array(
            'models'=>$models,
            'pages'=>$pages,
        ));
    }*/

    /**
     * Manages all models.
     */
    /*public function actionAdmin()
    {
        $this->processAdminCommand();

        $criteria=new CDbCriteria;

        $pages=new CPagination(User::model()->count($criteria));
        $pages->pageSize=self::PAGE_SIZE;
        $pages->applyLimit($criteria);

        $sort=new CSort('User');
        $sort->applyOrder($criteria);

        $models=User::model()->findAll($criteria);

        $this->render('admin',array(
            'models'=>$models,
            'pages'=>$pages,
            'sort'=>$sort,
        ));
    }*/

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
     */
    public function loadUser($id=null)
    {
        if($this->_model===null)
        {
            if($id!==null || isset($_GET['id']))
                $this->_model=User::model()->with('details')->findByPk($id!==null ? $id : $_GET['id']);
            if($this->_model===null)
                throw new CHttpException(404,'The requested page does not exist.');
        }
        return $this->_model;
    }

    /**
     * Executes any command triggered on the admin page.
     */
    /*protected function processAdminCommand()
    {
        if(isset($_POST['command'], $_POST['id']) && $_POST['command']==='delete')
        {
            $this->loadUser($_POST['id'])->delete();
            // reload the current page to avoid duplicated delete actions
            $this->refresh();
        }
    }*/
}
