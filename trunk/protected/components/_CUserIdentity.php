<?php

/**
 * _CUserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class _CUserIdentity extends CUserIdentity
{
    const ERROR_ACCOUNT_INACTIVE=10;

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
        else if($user->isActive==='0')
            $this->errorCode=self::ERROR_ACCOUNT_INACTIVE;
        else
        {
            $this->_id=$user->id;
            $this->errorCode=self::ERROR_NONE;
            // don't store sensitive information when (config/main.php) allowAutoLogin is true
            // as this information will be stored in cookie
            //$this->setState('accessLevel', $user->accessLevel);
            //$this->setState('accessType', $user->accessType);
            //$this->setState('createdOn', $user->createdOn);
            //$this->setState('createdGmtOn', $user->createdGmtOn);
            $this->setState('cssTheme', $user->cssTheme);
            $this->setState('email', $user->email);
            //$this->setState('isActive', $user->isActive);
            $this->setState('language', $user->language);
            $this->setState('screenName', $user->screenName);
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