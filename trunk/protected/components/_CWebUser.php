<?php
/**
 * _CWebUser class file
 * 
 * Override some functions of Yii core class CWebUser.
 */
class _CWebUser extends CWebUser
{
    /**
     * This function needs to be overriden because user
     * might get deactivated by admin, or some states
     * might be changed by either admin or user himself.
     * TODO: finish working on this function and test it
     */
    protected function restoreFromCookie()
    {
        $app=Yii::app();
        $cookie=$app->getRequest()->getCookies()->itemAt($this->getStateKeyPrefix());
        if($cookie && !empty($cookie->value) && ($data=$app->getSecurityManager()->validateData($cookie->value))!==false)
        {
            $data=unserialize($data);
            if(isset($data[0],$data[1],$data[2],$data[3]))
            {
                list($id,$name,$duration,$states)=$data;
                // this code is being rewritten: $this->changeIdentity($id,$name,$states);
                // below is the new code
                $identity=new _CUserIdentity($id,'');
                $identity->authenticateByCookie();
                switch($identity->errorCode)
                {
                    case _CUserIdentity::ERROR_NONE:
                        $this->login($identity);
                        // LOOKS LIKE MAIN CONTROLLER IS NOT INITIALIZED YET
                        /*// set user preferences (for welcome message, and so on)
                        if(isset(Yii::app()->user->interface) && !empty(Yii::app()->user->interface))
                            // set user preferred interface
                            W3::setInterface(Yii::app()->user->interface);
                        if(isset(Yii::app()->user->language) && !empty(Yii::app()->user->language))
                            // set user preferred language
                            W3::setLanguage(Yii::app()->user->language);
                        // set the welcome-back message
                        MUserFlash::setTopSuccess(Yii::t('hint',
                            '{screenName}, welcome back! Automatic authentication has been successfully passed.',
                            array('{screenName}'=>'<strong>'.$this->getState('screenName').'</strong>')
                        ));*/
                        break;
                    case _CUserIdentity::ERROR_ACCOUNT_IS_INACTIVE:
                        // set the error message
                        /*MUserFlash::setTopError(Yii::t('hint',
                            'We are sorry, but your member account is marked as "inactive". Inactive member accounts are temporarely inaccessible. {contactLink}.',
                            array('{contactLink}'=>CHtml::link(Yii::t('link','Contact us'),array('site/contact')))
                        ));*/
                        break;
                    case _CUserIdentity::ERROR_UNKNOWN_IDENTITY:
                    default:
                        // should we call logout() here?
                        //throw new CHttpException(401,Yii::t('yii','Unknown Identity'));
                        break;
                }
                if($this->autoRenewCookie)
                {
                    $cookie->expire=time()+$duration;
                    $app->getRequest()->getCookies()->add($cookie->name,$cookie);
                }
            }
        }
    }
}