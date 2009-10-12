<?php

class SiteController extends _CController
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image
            // this is used by the contact page
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xFFFFFF,/*EBF4FB*/
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/_layouts/main.php'
        $this->render('index');
    }

    /**
     * Displays the contact page
     */
    public function actionContact()
    {
        $contact=new ContactForm;
        if(isset($_POST['ContactForm']))
        {
            $contact->attributes=$_POST['ContactForm'];
            if($contact->validate())
            {
                $headers="From: {$contact->email}\r\nReply-To: {$contact->email}";
                @mail(MParams::getAdminEmailAddress(),$contact->subject,$contact->content,$headers);
                MUserFlash::setTopInfo(Yii::t('feedback','Thank you for contacting us. We will respond to you as soon as possible.'));
                $this->refresh();
            }
        }
        $this->render('contact',array('contact'=>$contact));
    }

    /**
     * Displays site error, while url stays the same
     * (url of the page where the HTTP exception has been raised).
     * If the action is triggered by an error we render 'error' view,
     * if not - we trigger 404 error manually.
     * (Manually means action will call itself, but with a 404 'Page not found.' error.)
     * Note: log in runtime/error.log is still being written.
     */
    public function actionError()
    {
        $error=Yii::app()->errorHandler->error;
        if($error)
            $this->render('error',array('error'=>$error));
        else
            throw new CHttpException(404,Yii::t('http','Page not found.'));
    }
}