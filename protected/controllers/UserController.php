<?php

class UserController extends _CController
{
	const PAGE_SIZE=10;

	/**
	 * @var string specifies the default action to be 'list'.
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
			/*array('deny', // deny authenticated user to perform 'login' actions
                'actions'=>array('login'),
                'users'=>array('@'),
            ),*/
            array('allow',  // allow all users to perform 'list', 'login', 'logout' and 'show' actions
                'actions'=>array('list','login','logout','show'),
                'users'=>array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('create','update'),
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
            // validate user input and redirect to previous page if valid
            if($form->validate())
            {
                // set the welcome message
                MUserFlash::setTopSuccess(Yii::t('w3',
                    '{displayName}, you have been successfully logged in.',
                    array('{displayName}'=>'<strong>'.Yii::app()->user->displayName.'</strong>')
                ));
                // user was just authenticated, but let's check anyway
                if(!Yii::app()->user->isGuest)
                {
                    // update user stats
                    if($userDetails=UserDetails::model()->findByPk(Yii::app()->user->id))
                        $userDetails->saveAttributes(array(
                            'lastLoginOn'=>date('Y-m-d H:i:s'),
                            'lastLoginGmtOn'=>gmdate('Y-m-d H:i:s'),
                            'lastSeenOn'=>date('Y-m-d H:i:s'),
                            'lastSeenGmtOn'=>gmdate('Y-m-d H:i:s'),
                            'totalTimeLoggedIn'=>$userDetails->totalTimeLoggedIn+60
                        ));
                    else
                        Yii::log(Yii::t('w3',
                            'User {userId} has no UserDetails record. Method called: {method}.',
                            array(
                                '{userId}'=>Yii::app()->user->id,
                                '{method}'=>__METHOD__.'()'
                            )
                        ),'error','w3');
                }
                // back to previous page
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        if(!Yii::app()->user->isGuest)
            // warn user if already logged in
            MUserFlash::setTopInfo(Yii::t('w3',
                '{displayName}, this will log you out from the current account! Proceed anyway?',
                array('{displayName}'=>'<strong>'.Yii::app()->user->displayName.'</strong>')
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
        $displayName=$isLoggedIn? Yii::app()->user->displayName : '';
        // log user out and destroy all sessions
        Yii::app()->user->logout();
        if($isLoggedIn) // if user was logged in, we should notify of being logged out
        {
            if(!Yii::app()->getSession()->getIsStarted())
                // have to re-open session destroyed on logout. this is necessary for user flash
                Yii::app()->getSession()->open();
            // set the goodbye message
            MUserFlash::setTopInfo(Yii::t('w3',
                '{displayName}, you have been successfully logged out.',
                array('{displayName}'=>'<strong>'.$displayName.'</strong>')
            ));
        }
        // go to home page
        $this->redirect(Yii::app()->homeUrl);
    }

	/**
	 * Shows a particular model.
	 */
	/*public function actionShow()
	{
		$this->render('show',array('model'=>$this->loadUser()));
	}*/

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	/*public function actionCreate()
	{
		$model=new User;
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('show','id'=>$model->id));
		}
		$this->render('create',array('model'=>$model));
	}*/

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	/*public function actionUpdate()
	{
		$model=$this->loadUser();
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			if($model->save())
				$this->redirect(array('show','id'=>$model->id));
		}
		$this->render('update',array('model'=>$model));
	}*/

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'list' page.
	 */
	/*public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadUser()->delete();
			$this->redirect(array('list'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}*/

	/**
	 * Lists all models.
	 */
	/*public function actionList()
	{
		$criteria=new CDbCriteria;

		$pages=new CPagination(User::model()->count($criteria));
		$pages->pageSize=self::PAGE_SIZE;
		$pages->applyLimit($criteria);

		$models=User::model()->findAll($criteria);

		$this->render('list',array(
			'models'=>$models,
			'pages'=>$pages,
		));
	}*/

	/**
	 * Manages all models.
	 */
	/*public function actionAdmin()
	{
		$this->processAdminCommand();

		$criteria=new CDbCriteria;

		$pages=new CPagination(User::model()->count($criteria));
		$pages->pageSize=self::PAGE_SIZE;
		$pages->applyLimit($criteria);

		$sort=new CSort('User');
		$sort->applyOrder($criteria);

		$models=User::model()->findAll($criteria);

		$this->render('admin',array(
			'models'=>$models,
			'pages'=>$pages,
			'sort'=>$sort,
		));
	}*/

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	public function loadUser($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_model=User::model()->findByPk($id!==null ? $id : $_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

	/**
	 * Executes any command triggered on the admin page.
	 */
	/*protected function processAdminCommand()
	{
		if(isset($_POST['command'], $_POST['id']) && $_POST['command']==='delete')
		{
			$this->loadUser($_POST['id'])->delete();
			// reload the current page to avoid duplicated delete actions
			$this->refresh();
		}
	}*/
}
