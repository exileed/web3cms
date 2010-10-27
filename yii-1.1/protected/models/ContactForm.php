<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class ContactForm extends CFormModel
{
    public $content;
    public $email;
    public $name;
    public $subject;
    public $verifyCode;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // name, email, subject and content are required
            array('content, email, name, subject', 'required'),
            // email has to be a valid email address
            array('email', 'email'),
            // verifyCode needs to be entered correctly
            array('verifyCode', 'captcha', 'allowEmpty'=>!Yii::app()->user->isGuest || !extension_loaded('gd')),
        );
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'content'=>Yii::t('t','Content'),
            'email'=>Yii::t('t','Email'),
            'name'=>Yii::t('t','Name'),
            'subject'=>Yii::t('t','Subject'),
            'verifyCode'=>Yii::t('t','Verification code'),
        );
    }
}