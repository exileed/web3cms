<?php

class UserController extends _CController
{
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
            array('allow',  // allow all users to perform 'captcha', 'login' and 'logout' actions
                'actions'=>array('captcha','login','logout'),
                'users'=>array('*'),
            ),
            //array('allow', // allow authenticated user to perform 'update' actions
                //'actions'=>array('update'),
                //'users'=>array('@'),
            //),
            //array('allow', // allow admin user to perform 'admin' and 'delete' actions
                //'actions'=>array('admin','delete'),
                //'users'=>array('admin'),
            //),
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
     * Displays the login page
     */
    public function actionLogin()
    {
        $form=new LoginForm;
        // collect user input data
        if(isset($_POST['LoginForm']))
        {
            if(isset($_POST['LoginForm']['loginWithField']))
            {
                // if user is logging with email, but param changed to username,
                // we should try to log him in with email.
                // if login attempt is unsuccessful, he will have to try again with username
                LoginForm::$loginWithField=$_POST['LoginForm']['loginWithField'];
                unset($_POST['LoginForm']['loginWithField']);
            }
            $form->attributes=$_POST['LoginForm'];
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
                // go to previous/profile page
                $url=Yii::app()->homeUrl==Yii::app()->user->returnUrl ? array('site/index') : Yii::app()->user->returnUrl;
                $this->redirect($url);
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
        // go to home page
        $this->redirect(Yii::app()->homeUrl);
    }
}
