<?php

/**
 * UserLoginForm class.
 * UserLoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'UserController'.
 */
class UserLoginForm extends CFormModel
{
    public $username;
	public $email;
    public $usernameOrEmail;
	public $password;
    public $rememberMe;
	public static $loginWithField;

    public function __construct($attributes=array(),$scenario='')
    {
        parent::__construct($attributes,$scenario);
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
            'usernameOrEmail'=>'Username or Email',
			'rememberMe'=>'Remember me next time',
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
                    $duration=($this->rememberMe && Yii::app()->user->allowAutoLogin) ? 3600*24*30 : 0;
                    // log user in and save in session all appended data
					Yii::app()->user->login($identity,$duration);
					break;
				case _CUserIdentity::ERROR_USERNAME_INVALID:
                    if(self::getLoggingWithField()=='username')
                        $this->addError('username','Username is incorrect.');
                    else if(self::getLoggingWithField()=='email')
                        $this->addError('email','Email is incorrect.');
                    else if(self::getLoggingWithField()=='usernameOrEmail')
					    $this->addError('usernameOrEmail','Username or email is incorrect.');
					break;
				default: // _CUserIdentity::ERROR_PASSWORD_INVALID
					$this->addError('password','Password is incorrect.');
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
