<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping user login form data.
 * It is used by the 'login' action of 'UserController'.
 */
class LoginForm extends CFormModel
{
    public $email;
    public $password;
    public $rememberMe;
    public $username;
    public $usernameOrEmail;
    public static $loginWithField;

    public function __construct($scenario='')
    {
        parent::__construct($scenario);
        self::$loginWithField=$this->getLoginWithField();
    }

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username or email or usernameOrEmail is required
            array(self::getLoggingWithField(), 'required'),
            // password is required
            array('password', 'required'),
            // password needs to be authenticated
            array('password', 'authenticate'),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // all attributes are safe
            array('email, password, rememberMe, username, usernameOrEmail', 'safe'),
        );
    }

    /**
     * Declares the attribute labels.
     * If an attribute is not delcared here, it will use the default label
     * generation algorithm to get its label.
     */
    public function attributeLabels()
    {
        return array(
            'rememberMe'=>Yii::t('hint','Remember my member account on this computer'),
            'password'=>Yii::t('t','Password'),
            'username'=>Yii::t('t','Username'),
            'usernameOrEmail'=>Yii::t('t','Username or email'),
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute,$params)
    {
        if(!$this->hasErrors())  // we only want to authenticate when no input errors
        {
            $identity=new _CUserIdentity($this->{self::getLoggingWithField()},$this->password);
            $identity->authenticate();
            switch($identity->errorCode)
            {
                case _CUserIdentity::ERROR_NONE:
                    // if user is already logged in
                    if(!Yii::app()->user->isGuest)
                    {
                        // log user out from the current account. i want to sleep well, do you? ;)
                        Yii::app()->user->logout();
                        if(!Yii::app()->getSession()->getIsStarted())
                            // restore http session. this is necessary for login
                            Yii::app()->getSession()->open();
                    }
                    // remember for 30 days. makes sence only if auto-login is allowed
                    $duration=(Yii::app()->user->allowAutoLogin && $this->rememberMe) ? 3600*24*30 : 0;
                    // log user in and save in session all appended data
                    Yii::app()->user->login($identity,$duration);
                    // set user preferences (for welcome message, and so on)
                    if(isset(Yii::app()->user->interface) && !empty(Yii::app()->user->interface))
                        // set user preferred interface
                        W3::setInterface(Yii::app()->user->interface);
                    if(isset(Yii::app()->user->language) && !empty(Yii::app()->user->language))
                        // set user preferred language
                        W3::setLanguage(Yii::app()->user->language);
                    break;
                case _CUserIdentity::ERROR_USERNAME_INVALID:
                    if(self::getLoggingWithField()==='username')
                        $this->addError('username',Yii::t('t','Username is incorrect.'));
                    else if(self::getLoggingWithField()==='email')
                        $this->addError('email',Yii::t('t','Email is incorrect.'));
                    else if(self::getLoggingWithField()==='usernameOrEmail')
                        $this->addError('usernameOrEmail',Yii::t('t','Username or email is incorrect.'));
                    break;
                case _CUserIdentity::ERROR_ACCOUNT_IS_INACTIVE:
                    // set the error message
                    MUserFlash::setTopError(Yii::t('hint',
                        'We are sorry, but your member account is marked as "inactive". Inactive member accounts are temporarely inaccessible. {contactLink}.',
                        array('{contactLink}'=>CHtml::link(Yii::t('link','Contact us'),array('site/contact')))
                    ));
                    // add to username (first field in the login form) error css class
                    // and make the validate() to fail
                    $attribute=self::getLoggingWithField();
                    $attribute!=='username' && $attribute!=='email' && $attribute!=='usernameOrEmail' && ($attribute='username');
                    $this->addError($attribute,'');
                    break;
                case _CUserIdentity::ERROR_IS_NOT_ADMINISTRATOR:
                    // set the error message
                    MUserFlash::setTopError(Yii::t('hint',
                        'We are sorry, but your access type is {accessType}. Required access type: {requiredAccessType}.',
                        array(
                            '{accessType}'=>Yii::app()->controller->var->userAccessType,
                            '{requiredAccessType}'=>Yii::t('t',User::ADMINISTRATOR_T)
                        )
                    ));
                    unset(Yii::app()->controller->var->userAccessType); // we do not need this any more
                    // add to username (first field in the login form) error css class
                    // and make the validate() to fail
                    $attribute=self::getLoggingWithField();
                    $attribute!=='username' && $attribute!=='email' && $attribute!=='usernameOrEmail' && ($attribute='username');
                    $this->addError($attribute,'');
                    break;
                case _CUserIdentity::ERROR_PASSWORD_INVALID:
                default:
                    $this->addError('password',Yii::t('t','Password is incorrect.'));
                    break;
            }
        }
    }

    /**
    * Which field to log user in with
    * 
    * @return string
    */
    public function getLoginWithField()
    {
        $array=array(
            'username'=>'username',
            'email'=>'email',
            '_any_'=>'usernameOrEmail'
        );
        return $array[MParams::getUserLoginWithField()];
    }

    /**
    * Which field is user now trying to login with
    * If trying to login with username, but later userLoginWithField param
    * was changed to email, still username should be required, not email, right?
    * 
    * @return string
    */
    public static function getLoggingWithField()
    {
        switch(self::$loginWithField)
        {
            case 'username':
            case 'email':
            case 'usernameOrEmail':
                return self::$loginWithField;
                break;
            default:
                return 'username';
                break;
        }
    }
}
