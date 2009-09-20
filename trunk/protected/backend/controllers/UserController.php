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
            array('allow',  // allow all users to perform 'captcha', 'login' and 'logout' actions
                'actions'=>array('captcha','login','logout'),
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
                    if(($userDetails=UserDetails::model()->findByPk(Yii::app()->user->id))!==false)
                        $userDetails->saveAttributes(array(
                            'lastLoginDate'=>date('Y-m-d H:i:s'),
                            'lastLoginGmtDate'=>gmdate('Y-m-d H:i:s'),
                            'lastVisitDate'=>date('Y-m-d H:i:s'),
                            'lastVisitGmtDate'=>gmdate('Y-m-d H:i:s'),
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
        $screenName=$isLoggedIn ? Yii::app()->user->screenName : '';
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
}
