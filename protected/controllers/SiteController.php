<?php

class SiteController extends _CController
{
    /**
     * @var string specifies the default action to be 'index'.
     */
    public $defaultAction='index';
    
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
        //$this->render($this->action->id);
        $this->redirect($this->getGotoUrl());
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
                MUserFlash::setTopInfo(Yii::t('hint','Thank you for contacting us. We will respond to you as soon as possible.'));
                $this->refresh();
            }
        }
        $this->render($this->action->id,array('contact'=>$contact));
    }

    /**
     * Displays the site error, while url stays the same
     * (the url of the page where the HTTP exception has been raised).
     * If action is triggered by an error, then the 'error' view is rendered,
     * otherwise - the 404 error is triggered manually.
     * Note: log in runtime/error.log is still being written.
     */
    public function actionError()
    {
        $error=Yii::app()->errorHandler->error;
        if($error)
        {
            /*if(Yii::app()->request->isAjaxRequest)
            {
                if(!headers_sent())
                    header('HTTP/1.0 '.$error['code']);
                exit;
            }
            else*/
                // render the view file
                $this->render($this->action->id,array('error'=>$error));
        }
        else
            // following will cause the script to run the current action again
            // with the 404 'not found' error.
            throw new CHttpException(404,Yii::t('http','Page not found.'));
    }

    /**
     * Display the application wiki.
     */
    public function actionWiki()
    {
        $this->render($this->action->id);
    }
}