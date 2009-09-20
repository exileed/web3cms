<?php
/**
 * _CUserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
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
            $this->setState('email', $user->email);
            $this->setState('interface', $user->interface);
            $this->setState('language', $user->language);
            $this->setState('screenName', $user->screenName);
        }
        return !$this->errorCode;
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
            $this->setState('email', $user->email);
            $this->setState('interface', $user->interface);
            $this->setState('language', $user->language);
            $this->setState('screenName', $user->screenName);
        }
        return !$this->errorCode;
    }
}