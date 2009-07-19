<?php

/**
 * _CUserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class _CUserIdentity extends CUserIdentity
{
    private $_id;

    /**
     * Authenticates a user.
     * @return boolean whether authentication succeeds.
     */
	public function authenticate()
	{
        if(UserLoginForm::getLoggingWithField()=='username')
            $user=User::model()->findByAttributes(array('username'=>$this->username));
        else if(UserLoginForm::getLoggingWithField()=='email')
            $user=User::model()->findByAttributes(array('email'=>$this->username));
        else if(UserLoginForm::getLoggingWithField()=='usernameOrEmail')
            $user=User::model()->find("`username`=? OR `email`=?",array($this->username,$this->username));
        if($user===null)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if(md5($this->password)!==$user->password)
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
        {
            $this->_id=$user->id;
            $this->errorCode=self::ERROR_NONE;
            // don't store sensitive information when (config/main.php) allowAutoLogin is true
            // as this information will be stored in cookie
            $this->setState('email', $user->email);
            $this->setState('displayName', $user->displayName);
            $this->setState('language', $user->language);
            $this->setState('theme', $user->theme);
            $this->setState('accessType', $user->accessType);
            $this->setState('accessLevel', $user->accessLevel);
            $this->setState('isActive', $user->isActive);
            $this->setState('createdOn', $user->createdOn);
        }
        return !$this->errorCode;
	}

    /**
     * @return integer the ID of the user record
     */
    public function getId()
    {
        return $this->_id;
    }
}