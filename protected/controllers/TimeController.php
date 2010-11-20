<?php

class TimeController extends _CController
{
    /**
     * @var string specifies the default action to be 'grid'.
     */
    public $defaultAction='grid';

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
            array('allow', // allow all users to perform 'report' and 'show' actions
                'actions'=>array('report','show'),
                'users'=>array('*'),
            ),
            array('allow', // following actions are checked by {@link checkAccessBeforeAction}
                'actions'=>array('ajaxDelete','create','delete','grid','gridData','list','update'),
                'users'=>array('*'),
            ),
            /*array('allow', // allow authenticated user to perform 'create' actions
                'actions'=>array('create'),
                'users'=>array('@'),
            ),*/
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Shows a particular model.
     */
    public function actionShow()
    {
        if(!Yii::app()->user->checkAccess($this->route,array('model'=>($model=$this->loadModel()))))
        {
            // access denied
            MUserFlash::setTopError(Yii::t('accessDenied',$this->route,array(1,'{id}'=>(is_object($model) ? $model->id : '?'))));
            $this->redirect($this->getGotoUrl());
        }

        $with=array('consultant','manager','task','task.company','task.project');
        /*if(Yii::app()->user->checkAccess(User::CLIENT))
            $with[]='task.company.allUser2Company';*/
        $model=$this->loadModel(array('with'=>$with));
        // may member view this record?
        if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            /*$allOwner=array();
            if(isset($model->task->company->allUser2Company))
            {
                foreach($model->task->company->allUser2Company as $user2Company)
                {
                    if($user2Company->position===Company::OWNER)
                        $allOwner[]=$user2Company->userId;
                }
            }
            if(!in_array(Yii::app()->user->id,$allOwner))*/
            if(!isset($model->task->company->id) || !$model->task->company->isOwner())
            {
            	// access denied
                MUserFlash::setTopError(Yii::t('accessDenied',$this->route,array('{id}'=>MHtml::wrapInTag($model->id,'strong'))));
                $this->redirect($this->getGotoUrl());
            }
        }
        if(Yii::app()->user->checkAccess(User::CONSULTANT) && (!isset($model->consultant->id) || $model->consultant->id!==Yii::app()->user->id))
        {
        	// access denied
            MUserFlash::setTopError(Yii::t('accessDenied',$this->route,array('{id}'=>MHtml::wrapInTag($model->id,'strong'))));
            $this->redirect($this->getGotoUrl());
        }

        // render the view file
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'show' page.
     */
    public function actionCreate()
    {
        $model=new Time($this->action->id);
        if(isset($_POST['Time']))
        {
            // collect user input data
            $model->attributes=$_POST['Time'];
            if(!isset($_POST['Time']['managerId']))
            {
                // set manager based on the task
                if($model->taskId>=1)
                {
                    $criteria=new CDbCriteria;
                    $criteria->order="`t`.`userPriority` ASC, `t`.`id` ASC";
                    if(($user2Task=User2Task::model()->findByAttributes(array('taskId'=>$model->taskId,'role'=>User2Task::MANAGER),$criteria))!==null)
                        $model->managerId=$user2Task->userId;
                    else
                        $model->managerId=0;
                }
                else
                    $model->managerId=0;
            }
            if(!isset($_POST['Time']['consultantId']))
            {
                // set consultant based on the task
                if($model->taskId>=1)
                {
                    $criteria=new CDbCriteria;
                    $criteria->order="`t`.`userPriority` ASC, `t`.`id` ASC";
                    if(($user2Task=User2Task::model()->findByAttributes(array('taskId'=>$model->taskId,'role'=>User2Task::CONSULTANT),$criteria))!==null)
                        $model->consultantId=$user2Task->userId;
                    else
                        $model->consultantId=0;
                }
                else
                    $model->consultantId=0;
            }
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The new "{title}" time record has been successfully created.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        else
        {
            // pre-assigned attributes (default values for a new record)
            if(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR))
                $model->isConfirmed=Time::IS_CONFIRMED;
            if(isset($_GET['taskId']))
                // task is known
                $model->taskId=$_GET['taskId'];
        }
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate()
    {
        if(($model=$this->loadModel())===null)
        {
            // model not found
            MUserFlash::setTopError(Yii::t('modelNotFound',$this->id));
            $this->redirect($this->getGotoUrl());
        }
        // explicitly set model scenario to be current action
        //$model->setScenario($this->action->id);
        // whether data is passed
        if(isset($_POST['Time']))
        {
            // collect user input data
            $model->attributes=$_POST['Time'];
            if(!isset($_POST['Time']['managerId']))
            {
                // set manager based on the task
                if($model->taskId>=1)
                {
                    $criteria=new CDbCriteria;
                    $criteria->order="`t`.`userPriority` ASC, `t`.`id` ASC";
                    if(($user2Task=User2Task::model()->findByAttributes(array('taskId'=>$model->taskId,'role'=>'manager'),$criteria))!==null)
                        $model->managerId=$user2Task->userId;
                    else
                        $model->managerId=0;
                }
                else
                    $model->managerId=0;
            }
            if(!isset($_POST['Time']['consultantId']))
            {
                // set consultant based on the task
                if($model->taskId>=1)
                {
                    $criteria=new CDbCriteria;
                    $criteria->order="`t`.`userPriority` ASC, `t`.`id` ASC";
                    if(($user2Task=User2Task::model()->findByAttributes(array('taskId'=>$model->taskId,'role'=>User2Task::CONSULTANT),$criteria))!==null)
                        $model->consultantId=$user2Task->userId;
                    else
                        $model->consultantId=0;
                }
                else
                    $model->consultantId=0;
            }
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The "{title}" time record has been updated.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        // prepare model data for the form
        $model->spentH=floor($model->spentMinute/60);
        $model->spentM=fmod($model->spentMinute,60);
        $model->billedH=floor($model->billedMinute/60);
        $model->billedM=fmod($model->billedMinute,60);
        // render the view file
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'list' page.
     */
    public function actionDelete()
    {
        $model=$this->loadModel();
        // explicitly set model scenario to be current action
        //$model->setScenario($this->action->id);
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            if($model->delete())
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The time record number {id} has been successfully deleted.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                // go to the controller default page
                $this->redirect(array($this->id.'/'));
            }
            else
            {
                // set error message
                MUserFlash::setTopError(Yii::t('hint',
                    'Error! The time record number {id} could not be deleted.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                Yii::log(W3::t('system',
                    'Could not delete the {model} model. Model ID: {modelId}. Method called: {method}.',
                    array('{model}'=>get_class($model),'{modelId}'=>$model->id,'{method}'=>__METHOD__.'()')
                ),'error','w3');
            }
        }
        // render the view file
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Deletes a particular model via ajax request.
     * The report is printed out in the json format,
     * and is displayed in a dialog.
     */
    public function actionAjaxDelete()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            throw new CHttpException(400,Yii::t('http','Invalid request. Please do not repeat this request again.'));
            exit;
        }
        if(!isset($_POST['id']))
            // data not passed
            $this->printJsonExit(array('status'=>'error',
                'message'=>Yii::t('hint','Error! Necessary data was not passed.')
            ));
        if(($model=$this->loadModel())===null)
            // model not found
            $this->printJsonExit(array('status'=>'error',
                'message'=>Yii::t('modelNotFoundById',$this->id,
                    array('{id}'=>MHtml::wrapInTag($_POST['id'],'strong'))
                )
            ));
        if($model->delete())
            // success
            $this->printJson(array('status'=>'success',
                'message'=>Yii::t('hint','The time record number {id} has been successfully deleted.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                )
            ));
        else
        {
            // error
            $this->printJson(array('status'=>'error',
                'message'=>Yii::t('hint','Error! The time record number {id} could not be deleted.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                )
            ));
            // save the error in the logs
            Yii::log(W3::t('system',
                'Could not delete the {model} model. Model ID: {modelId}. Method called: {method}.',
                array('{model}'=>get_class($model),'{modelId}'=>$model->id,'{method}'=>__METHOD__.'()')
            ),'error','w3');
        }
    }

    /**
     * Lists all models.
     */
    public function actionList()
    {
        $criteria=new CDbCriteria;
        $criteria->order="`t`.`timeDate` DESC, `t`.`createTime` DESC";

        $pages=new CPagination(Time::model()->count($criteria));
        $pages->pageSize=self::LIST_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=Time::model()->with('consultant','manager','task','task.company','task.project')->findAll($criteria);

        $this->render($this->action->id,array(
            'models'=>$models,
            'pages'=>$pages,
        ));
    }

    /**
     * Grid of all models.
     */
    public function actionGrid()
    {
        // specify filter parameters
        $company=isset($_GET['company']) ? $_GET['company'] : null;
        if($company!=='all' && !ctype_digit($company))
            $company='all';
        $consultant=isset($_GET['consultant']) ? $_GET['consultant'] : null;
        if($consultant!=='all' && $consultant!=='me' && !ctype_digit($consultant))
            $consultant='all';
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
            $consultant='me';
        $manager=isset($_GET['manager']) ? $_GET['manager'] : null;
        if($manager!=='all' && !ctype_digit($manager))
            $manager='all';
        $project=isset($_GET['project']) ? $_GET['project'] : null;
        if($project!=='all' && !ctype_digit($project))
            $project='all';
        $task=isset($_GET['task']) ? $_GET['task'] : null;
        if($task!=='all' && !ctype_digit($task))
            $task='all';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.billedMinute, `t`.spentMinute, `t`.timeDate, `t`.title";
        //$criteria->select="`t`.`billedMinute`, `t`.`spentMinute`, `t`.`timeDate`, `t`.`title`"; // uncomment in yii-1.1.2
        if($company!=='all')
        {
            $criteria->addCondition("`Task_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if($consultant!=='all')
        {
            if($consultant==='me')
            {
                $criteria->addCondition("`Time_Consultant`.`id`=:consultantId");
                $criteria->params[':consultantId']=Yii::app()->user->id;
            }
            else
            {
                $criteria->addCondition("`Time_Consultant`.`id`=:consultantId");
                $criteria->params[':consultantId']=$consultant;
            }
        }
        if($manager!=='all')
        {
            $criteria->addCondition("`Time_Manager`.`id`=:managerId");
            $criteria->params[':managerId']=$manager;
        }
        if($project!=='all')
        {
            $criteria->addCondition("`Task_Project`.`id`=:projectId");
            $criteria->params[':projectId']=$project;
        }
        if($task!=='all')
        {
            $criteria->addCondition("`Time_Task`.`id`=:taskId");
            $criteria->params[':taskId']=$task;
        }
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Task_Company')!==false)
            $with[]='task.company';
        if(strpos($criteria->condition,'Time_Consultant')!==false)
            $with[]='consultant';
        if(strpos($criteria->condition,'Time_Manager')!==false)
            $with[]='manager';
        if(strpos($criteria->condition,'Task_Project')!==false)
            $with[]='task.project';
        if(strpos($criteria->condition,'Time_Task')!==false)
            $with[]='task';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='task.company.allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Time::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Time::model()->count($criteria));
        $pages->pageSize=self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Time');
        $sort->attributes=array(
            'billedMinute'=>array('asc'=>"`t`.`billedMinute`",'desc'=>"`t`.`billedMinute` desc",'label'=>Time::model()->getAttributeLabel('Bld')),
            'spentMinute'=>array('asc'=>"`t`.`spentMinute`",'desc'=>"`t`.`spentMinute` desc",'label'=>Time::model()->getAttributeLabel('Spt')),
            'timeDate'=>array('asc'=>"`t`.`timeDate`",'desc'=>"`t`.`timeDate` desc",'label'=>Time::model()->getAttributeLabel('Date')),
            'title'=>array('asc'=>"`t`.`title`",'desc'=>"`t`.`title` desc",'label'=>Time::model()->getAttributeLabel('title')),
            'consultant'=>array('asc'=>"`Time_Consultant`.`screenName`",'desc'=>"`Time_Consultant`.`screenName` desc",'label'=>Time::model()->getAttributeLabel('consultantId')),
            'manager'=>array('asc'=>"`Time_Manager`.`screenName`",'desc'=>"`Time_Manager`.`screenName` desc",'label'=>Time::model()->getAttributeLabel('managerId')),
            'task'=>array('asc'=>"`Time_Task`.`title`",'desc'=>"`Time_Task`.`title` desc",'label'=>Time::model()->getAttributeLabel('taskId')),
            'company'=>array('asc'=>"`Task_Company`.`title`",'desc'=>"`Task_Company`.`title` desc",'label'=>Task::model()->getAttributeLabel('companyId')),
            'project'=>array('asc'=>"`Task_Project`.`title`",'desc'=>"`Task_Project`.`title` desc",'label'=>Task::model()->getAttributeLabel('projectId')),
        );
        $sort->defaultOrder="`t`.`timeDate` DESC, `t`.`createTime` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('consultant'=>array('select'=>'screenName'),'manager'=>array('select'=>'screenName'),'task'=>array('select'=>'title'),'task.company'=>array('select'=>'title'),'task.project'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['task.company.allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Company_User2Company')!==false;
        if($together)
            $models=Time::model()->with($with)->together()->findAll($criteria);
        else
            $models=Time::model()->with($with)->findAll($criteria);

        // filters data
        $filters=array('company'=>$company,'project'=>$project,'task'=>$task,'manager'=>$manager,'consultant'=>$consultant);
        $allCompany=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('company'=>'all'))),
            'active'=>$company==='all'
        ));
        $companyLinkText=$company==='all' ? Yii::t('t','All companies') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.`id`, `t`.`title`, COUNT(`time`.`id`) as countTime";
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`".
                " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`project`.`id`".
                " INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`task`.`id`";
            $criteria->condition="`time`.`consultantId`=:consultantId";
            $criteria->params[':consultantId']=Yii::app()->user->id;
        }
        else if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`".
                " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`project`.`id`".
                " INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`task`.`id`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`t`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`".
                " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`project`.`id`".
                " INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`task`.`id`";
        $criteria->group="`t`.`id`";
        $criteria->order="`t`.`title` ASC";
        $criteria->limit=3000;
        foreach(Company::model()->findAll($criteria) as $model)
        {
            $allCompany[]=array(
                'text'=>CHtml::encode($model->title),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('company'=>$model->id))),
                'active'=>$company===(string)$model->id
            );
            $i=count($allCompany)-1;
            if($allCompany[$i]['text']==='')
                $allCompany[$i]['text']=Yii::t('t','[no title]');
            $allCompany[$i]['text'].=' ('.$model->countTime.')';
            if($company===(string)$model->id)
                $companyLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }
        $allConsultant=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('consultant'=>'all'))),
            'active'=>$consultant==='all'
        ));
        $consultantLinkText=$consultant==='all' ? Yii::t('t','All consultants') : '&nbsp;';
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $allConsultant[0]=array(
                'text'=>Yii::t('t','My time'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('consultant'=>'me'))),
                'active'=>$consultant==='me'
            );
            if($consultant==='me')
                $consultantLinkText=Yii::t('t','My time');
        }
        else
        {
            $criteria=new CDbCriteria;
            $criteria->select="`t`.`id`, `t`.`screenName`, COUNT(`time`.`id`) as countTime";
            if(Yii::app()->user->checkAccess(User::CLIENT))
            {
                $criteria->join="INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`consultantId`=`t`.`id`".
                    " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`id`=`time`.`taskId`".
                    " INNER JOIN `".Company::model()->tableName()."` `company` ON `company`.`id`=`task`.`companyId`".
                    " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`company`.`id`";
                $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
                $criteria->params[':clientId']=Yii::app()->user->id;
                $criteria->params[':clientPosition']=Company::OWNER;
            }
            else
                $criteria->join="INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`consultantId`=`t`.`id`";
            $criteria->group="`time`.`consultantId`";
            $criteria->order="`t`.`screenName`";
            $criteria->limit=3000;
            foreach(User::model()->findAll($criteria) as $model)
            {
                $allConsultant[]=array(
                    'text'=>CHtml::encode($model->screenName),
                    'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('consultant'=>$model->id))),
                    'active'=>$consultant===(string)$model->id
                );
                $i=count($allConsultant)-1;
                if($allConsultant[$i]['text']==='')
                    $allConsultant[$i]['text']=Yii::t('t','[no name]');
                $allConsultant[$i]['text'].=' ('.$model->countTime.')';
                if($consultant===(string)$model->id)
                    $consultantLinkText=$model->screenName===''?Yii::t('t','[no name]'):$model->screenName;
            }
        }
        $allManager=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('manager'=>'all'))),
            'active'=>$manager==='all'
        ));
        $managerLinkText=$manager==='all' ? Yii::t('t','All managers') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.`id`, `t`.`screenName`, COUNT(`time`.`id`) as countTime";
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $criteria->join="INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`managerId`=`t`.`id`";
            $criteria->condition="`time`.`consultantId`=:consultantId";
            $criteria->params[':consultantId']=Yii::app()->user->id;
        }
        else if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->join="INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`managerId`=`t`.`id`".
                " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`id`=`time`.`taskId`".
                " INNER JOIN `".Company::model()->tableName()."` `company` ON `company`.`id`=`task`.`companyId`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`company`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`managerId`=`t`.`id`";
        $criteria->group="`time`.`managerId`";
        $criteria->order="`t`.`screenName`";
        $criteria->limit=3000;
        foreach(User::model()->findAll($criteria) as $model)
        {
            $allManager[]=array(
                'text'=>CHtml::encode($model->screenName),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('manager'=>$model->id))),
                'active'=>$manager===(string)$model->id
            );
            $i=count($allManager)-1;
            if($allManager[$i]['text']==='')
                $allManager[$i]['text']=Yii::t('t','[no name]');
            $allManager[$i]['text'].=' ('.$model->countTime.')';
            if($manager===(string)$model->id)
                $managerLinkText=$model->screenName===''?Yii::t('t','[no name]'):$model->screenName;
        }
        $allProject=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('project'=>'all'))),
            'active'=>$project==='all'
        ));
        $projectLinkText=$project==='all' ? Yii::t('t','All projects') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.id, `t`.title, COUNT(`time`.`id`) as countTime";
        //$criteria->select="`t`.`id`, `t`.`title`, COUNT(`time`.`id`) as countTime"; // uncomment in yii-1.1.2
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $criteria->join="INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`t`.`id`".
                " INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`task`.`id`";
            $criteria->condition="`time`.`consultantId`=:consultantId";
            $criteria->params[':consultantId']=Yii::app()->user->id;
        }
        else if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->join="INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`t`.`id`".
                " INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`task`.`id`".
                " INNER JOIN `".Company::model()->tableName()."` `company` ON `company`.`id`=`task`.`companyId`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`company`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`t`.`id`".
                " INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`task`.`id`";
        $criteria->group="`t`.`id`";
        $criteria->order="`Project_Company`.`titleAbbr` ASC, `t`.`title` ASC";
        $criteria->limit=3000;
        foreach(Project::model()->with(array('allCompany'=>array('select'=>'titleAbbr')))->together()->findAll($criteria) as $model)
        {
            $allProject[]=array(
                'text'=>CHtml::encode((isset($model->allCompany[0]->titleAbbr)?$model->allCompany[0]->titleAbbr.' -- ':'').$model->title),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('project'=>$model->id))),
                'active'=>$project===(string)$model->id
            );
            $i=count($allProject)-1;
            if($allProject[$i]['text']==='')
                $allProject[$i]['text']=Yii::t('t','[no title]');
            $allProject[$i]['text'].=' ('.$model->countTime.')';
            if($project===(string)$model->id)
                $projectLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }
        $allTask=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('task'=>'all'))),
            'active'=>$task==='all'
        ));
        $taskLinkText=$task==='all' ? Yii::t('t','All tasks') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.id, `t`.title, COUNT(`time`.`id`) as countTime";
        //$criteria->select="`t`.`id`, `t`.`title`, COUNT(`time`.`id`) as countTime"; // uncomment in yii-1.1.2
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $criteria->join="INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`t`.`id`";
            $criteria->condition="`time`.`consultantId`=:consultantId";
            $criteria->params[':consultantId']=Yii::app()->user->id;
        }
        else if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->join="INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`t`.`id`".
                " INNER JOIN `".Company::model()->tableName()."` `company` ON `company`.`id`=`t`.`companyId`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`company`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Time::model()->tableName()."` `time` ON `time`.`taskId`=`t`.`id`";
        $criteria->group="`t`.`id`";
        $criteria->order="`Task_Project`.`title` ASC, `t`.`title` ASC";
        $criteria->limit=3000;
        foreach(Task::model()->with(array('project'=>array('select'=>'title')))->findAll($criteria) as $model)
        {
            $allTask[]=array(
                'text'=>CHtml::encode((isset($model->project->title)?$model->project->title.' -- ':'').$model->title),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('task'=>$model->id))),
                'active'=>$task===(string)$model->id
            );
            $i=count($allTask)-1;
            if($allTask[$i]['text']==='')
                $allTask[$i]['text']=Yii::t('t','[no title]');
            $allTask[$i]['text'].=' ('.$model->countTime.')';
            if($task===(string)$model->id)
                $taskLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }

        // rows for the static grid
        $gridRows=array();
        foreach($models as $model)
        {
            $gridRows[]=array(
                array(
                    'content'=>isset($model->task->company->id) ? CHtml::link(CHtml::encode($model->task->company->title),array('company/show','id'=>$model->task->company->id)) : '',
                ),
                array(
                    'content'=>isset($model->task->project->id) ? CHtml::link(CHtml::encode($model->task->project->title),array('project/show','id'=>$model->task->project->id)) : '',
                ),
                array(
                    'content'=>isset($model->task->id) ? CHtml::link(CHtml::encode($model->task->title),array('task/show','id'=>$model->task->id)) : '',
                ),
                array(
                    'content'=>isset($model->manager->id) ? CHtml::link(CHtml::encode($model->manager->screenName),array('user/show','id'=>$model->manager->id)) : '',
                ),
                array(
                    'content'=>isset($model->consultant->id) ? CHtml::link(CHtml::encode($model->consultant->screenName),array('user/show','id'=>$model->consultant->id)) : '',
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->timeDate,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->timeDate,'full',null)),
                ),
                array(
                    'content'=>CHtml::encode($model->getAttributeView('spentMinute')),
                ),
                array(
                    'content'=>CHtml::encode($model->getAttributeView('billedMinute')),
                ),
                array(
                    'content'=>CHtml::encode($model->title),
                ),
                array(
                    'content'=>
                        Yii::app()->user->checkAccess('time/show') ?
                            CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                                'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                                'title'=>Yii::t('link','Show')
                            )).
                            CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                                'class'=>'w3-ig w3-link-icon w3-border-1px-transparent ui-corner-all',
                                'title'=>Yii::t('link','Edit')
                            )).
                            CHtml::link('<span class="ui-icon ui-icon-trash"></span>',array('delete','id'=>$model->id),array(
                                'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-last ui-corner-all',
                                'title'=>Yii::t('link','Delete the record number {id}',array('{id}'=>$model->id))
                            ))
                        :
                            CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                                'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first w3-last ui-corner-all',
                                'title'=>Yii::t('link','Show')
                            ))
                    ,
                ),
            );
        }

        // render the view file
        $this->render($this->action->id,array(
            'models'=>$models,
            'pages'=>$pages,
            'sort'=>$sort,
            'company'=>$company,
            'consultant'=>$consultant,
            'manager'=>$manager,
            'project'=>$project,
            'task'=>$task,
            'filters'=>$filters,
            'allCompany'=>$allCompany,
            'companyLinkText'=>$companyLinkText,
            'allConsultant'=>$allConsultant,
            'consultantLinkText'=>$consultantLinkText,
            'allManager'=>$allManager,
            'managerLinkText'=>$managerLinkText,
            'allProject'=>$allProject,
            'projectLinkText'=>$projectLinkText,
            'allTask'=>$allTask,
            'taskLinkText'=>$taskLinkText,
            'gridRows'=>$gridRows,
        ));
    }

    /**
     * Print out array of models for the jqGrid rows.
     */
    public function actionGridData()
    {
        if(!Yii::app()->request->isPostRequest)
        {
            throw new CHttpException(400,Yii::t('http','Invalid request. Please do not repeat this request again.'));
            exit;
        }

        // specify request details
        $jqGrid=$this->processJqGridRequest();

        // specify filter parameters
        $company=isset($_GET['company']) ? $_GET['company'] : null;
        if($company!=='all' && !ctype_digit($company))
            $company='all';
        $consultant=isset($_GET['consultant']) ? $_GET['consultant'] : null;
        if($consultant!=='all' && $consultant!=='me' && !ctype_digit($consultant))
            $consultant='all';
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
            $consultant='me';
        $manager=isset($_GET['manager']) ? $_GET['manager'] : null;
        if($manager!=='all' && !ctype_digit($manager))
            $manager='all';
        $project=isset($_GET['project']) ? $_GET['project'] : null;
        if($project!=='all' && !ctype_digit($project))
            $project='all';
        $task=isset($_GET['task']) ? $_GET['task'] : null;
        if($task!=='all' && !ctype_digit($task))
            $task='all';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.billedMinute, `t`.spentMinute, `t`.timeDate, `t`.title";
        //$criteria->select="`t`.`billedMinute`, `t`.`spentMinute`, `t`.`timeDate`, `t`.`title`"; // uncomment in yii-1.1.2
        if($jqGrid['searchField']!==null && $jqGrid['searchString']!==null && $jqGrid['searchOper']!==null)
        {
            $field=array(
                'billedMinute'=>"`t`.`billedMinute`",
                'spentMinute'=>"`t`.`spentMinute`",
                'timeDate'=>"`t`.`timeDate`",
                'title'=>"`t`.`title`",
                'consultant'=>"`Time_Consultant`.`screenName`",
                'manager'=>"`Time_Manager`.`screenName`",
                'task'=>"`Time_Task`.`title`",
                'company'=>"`Task_Company`.`title`",
                'project'=>"`Task_Project`.`title`",
            );
            $operation=$this->getJqGridOperationArray();
            $keywordFormula=$this->getJqGridKeywordFormulaArray();
            if(isset($field[$jqGrid['searchField']]) && isset($operation[$jqGrid['searchOper']]))
            {
                $criteria->condition='('.$field[$jqGrid['searchField']].' '.$operation[$jqGrid['searchOper']].' :keyword)';
                $criteria->params=array(':keyword'=>str_replace('keyword',$jqGrid['searchString'],$keywordFormula[$jqGrid['searchOper']]));
                // search by special field types
                if($jqGrid['searchField']==='createTime' && ($keyword=strtotime($jqGrid['searchString']))!==false)
                {
                    $criteria->params=array(':keyword'=>str_replace('keyword',$keyword,$keywordFormula[$jqGrid['searchOper']]));
                    if(date('H:i:s',$keyword)==='00:00:00')
                        // visitor is looking for a precision by day, not by second
                        $criteria->condition='(TO_DAYS(FROM_UNIXTIME('.$field[$jqGrid['searchField']].',"%Y-%m-%d")) '.$operation[$jqGrid['searchOper']].' TO_DAYS(FROM_UNIXTIME(:keyword,"%Y-%m-%d")))';
                }
            }
        }
        if($company!=='all')
        {
            $criteria->addCondition("`Task_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if($consultant!=='all')
        {
            if($consultant==='me')
            {
                $criteria->addCondition("`Time_Consultant`.`id`=:consultantId");
                $criteria->params[':consultantId']=Yii::app()->user->id;
            }
            else
            {
                $criteria->addCondition("`Time_Consultant`.`id`=:consultantId");
                $criteria->params[':consultantId']=$consultant;
            }
        }
        if($manager!=='all')
        {
            $criteria->addCondition("`Time_Manager`.`id`=:managerId");
            $criteria->params[':managerId']=$manager;
        }
        if($project!=='all')
        {
            $criteria->addCondition("`Task_Project`.`id`=:projectId");
            $criteria->params[':projectId']=$project;
        }
        if($task!=='all')
        {
            $criteria->addCondition("`Time_Task`.`id`=:taskId");
            $criteria->params[':taskId']=$task;
        }
        if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Task_Company')!==false)
            $with[]='task.company';
        if(strpos($criteria->condition,'Time_Consultant')!==false)
            $with[]='consultant';
        if(strpos($criteria->condition,'Time_Manager')!==false)
            $with[]='manager';
        if(strpos($criteria->condition,'Task_Project')!==false)
            $with[]='task.project';
        if(strpos($criteria->condition,'Time_Task')!==false)
            $with[]='task';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='task.company.allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Time::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Time::model()->count($criteria));
        $pages->pageSize=$jqGrid['pageSize']!==null ? $jqGrid['pageSize'] : self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Time');
        $sort->attributes=array(
            'billedMinute'=>array('asc'=>"`t`.`billedMinute`",'desc'=>"`t`.`billedMinute` desc",'label'=>Time::model()->getAttributeLabel('Bld')),
            'spentMinute'=>array('asc'=>"`t`.`spentMinute`",'desc'=>"`t`.`spentMinute` desc",'label'=>Time::model()->getAttributeLabel('Spt')),
            'timeDate'=>array('asc'=>"`t`.`timeDate`",'desc'=>"`t`.`timeDate` desc",'label'=>Time::model()->getAttributeLabel('Date')),
            'title'=>array('asc'=>"`t`.`title`",'desc'=>"`t`.`title` desc",'label'=>Time::model()->getAttributeLabel('title')),
            'consultant'=>array('asc'=>"`Time_Consultant`.`screenName`",'desc'=>"`Time_Consultant`.`screenName` desc",'label'=>Time::model()->getAttributeLabel('consultantId')),
            'manager'=>array('asc'=>"`Time_Manager`.`screenName`",'desc'=>"`Time_Manager`.`screenName` desc",'label'=>Time::model()->getAttributeLabel('managerId')),
            'task'=>array('asc'=>"`Time_Task`.`title`",'desc'=>"`Time_Task`.`title` desc",'label'=>Time::model()->getAttributeLabel('taskId')),
            'company'=>array('asc'=>"`Task_Company`.`title`",'desc'=>"`Task_Company`.`title` desc",'label'=>Task::model()->getAttributeLabel('companyId')),
            'project'=>array('asc'=>"`Task_Project`.`title`",'desc'=>"`Task_Project`.`title` desc",'label'=>Task::model()->getAttributeLabel('projectId')),
        );
        $sort->defaultOrder="`t`.`timeDate` DESC, `t`.`createTime` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('consultant'=>array('select'=>'screenName'),'manager'=>array('select'=>'screenName'),'task'=>array('select'=>'title'),'task.company'=>array('select'=>'title'),'task.project'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['task.company.allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Company_User2Company')!==false;
        if($together)
            $models=Time::model()->with($with)->together()->findAll($criteria);
        else
            $models=Time::model()->with($with)->findAll($criteria);

        // create resulting data array
        $data=array(
            'page'=>$pages->getCurrentPage()+1,
            'total'=>$pages->getPageCount(),
            'records'=>$pages->getItemCount(),
            'rows'=>array()
        );
        foreach($models as $model)
        {
            $data['rows'][]=array('id'=>$model->id,'cell'=>array(
                isset($model->task->company->id) ? CHtml::link(CHtml::encode($model->task->company->title),array('company/show','id'=>$model->task->company->id)) : '',
                isset($model->task->project->id) ? CHtml::link(CHtml::encode($model->task->project->title),array('project/show','id'=>$model->task->project->id)) : '',
                isset($model->task->id) ? CHtml::link(CHtml::encode($model->task->title),array('task/show','id'=>$model->task->id)) : '',
                isset($model->manager->id) ? CHtml::link(CHtml::encode($model->manager->screenName),array('user/show','id'=>$model->manager->id)) : '',
                isset($model->consultant->id) ? CHtml::link(CHtml::encode($model->consultant->screenName),array('user/show','id'=>$model->consultant->id)) : '',
                CHtml::encode(MDate::format($model->timeDate,'medium',null)),
                CHtml::encode($model->getAttributeView('spentMinute')),
                CHtml::encode($model->getAttributeView('billedMinute')),
                CHtml::encode($model->title),
                Yii::app()->user->checkAccess('time/show') ?
                    CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                        'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                        'title'=>Yii::t('link','Show')
                    )).
                    CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                        'class'=>'w3-ig w3-link-icon w3-border-1px-transparent ui-corner-all',
                        'title'=>Yii::t('link','Edit')
                    )).
                    CHtml::link('<span class="ui-icon ui-icon-trash"></span>',array('delete','id'=>$model->id),array(
                        'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-last ui-corner-all',
                        'title'=>Yii::t('link','Delete the record number {id}',array('{id}'=>$model->id))
                    ))
                :
                    CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                        'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first w3-last ui-corner-all',
                        'title'=>Yii::t('link','Show')
                    ))
                ,
            ));
        }
        $this->printJson($data);
    }

    /**
     * Time report.
     */
    public function actionReport()
    {
        $criteria=new CDbCriteria;
        $criteria->select="`t`.billedMinute, `t`.spentMinute, `t`.timeDate, `t`.title";
        //$criteria->select="`t`.`billedMinute`, `t`.`spentMinute`, `t`.`timeDate`, `t`.`title`"; // uncomment in yii-1.1.2
        $criteria->condition="TO_DAYS(`t`.`timeDate`) >= TO_DAYS('2009-09-09')";
        $criteria->order="`t`.`timeDate` ASC, `t`.`createTime` ASC";

        $with=array('task'=>array('select'=>'hourlyRate,title'),'task.company'=>array('select'=>'title'),'task.project'=>array('select'=>'title'));
        if(Yii::app()->user->checkAccess(User::CLIENT))
            $with['manager']=array('select'=>'screenName');
        else
            $with['consultant']=array('select'=>'screenName');
        $models=Time::model()->with($with)->findAll($criteria);

        $data=array();
        foreach($models as $model)
        {
            $companyId=isset($model->task->company->id) ? $model->task->company->id : 0;
            if(!isset($data[$companyId]['company']) && isset($model->task->company->id))
                $data[$companyId]['company']=$model->task->company;
            $projectId=isset($model->task->project->id) ? $model->task->project->id : 0;
            if(!isset($data[$companyId]['allProject'][$projectId]['project']) && isset($model->task->project->id))
               $data[$companyId]['allProject'][$projectId]['project']=$model->task->project;
            $taskId=isset($model->task->id) ? $model->task->id : 0;
            if(!isset($data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['task']) && isset($model->task->id))
               $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['task']=$model->task;
            $timeId=$model->id;
            if(Yii::app()->user->checkAccess(User::CLIENT) || Yii::app()->user->checkAccess(User::CONSULTANT))
                $member=isset($model->manager->id) ? $model->manager : null;
            else
                $member=isset($model->consultant->id) ? $model->consultant : null;
            $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['gridRows'][]=array(
                array(
                    'content'=>$member!==null ? CHtml::link($member->screenName!==''?CHtml::encode($member->screenName):$member->id,array('user/show','id'=>$member->id)) : '',
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->timeDate,'short',null)),
                    'title'=>CHtml::encode(MDate::format($model->timeDate,'full',null)),
                ),
                array(
                    'content'=>CHtml::link($model->title!==''?CHtml::encode($model->title):$model->id,array('time/show','id'=>$model->id)),
                ),
                array(
                    'align'=>'right',
                    'content'=>$model->getAttributeView('billedMinute'),
                    'title'=>Yii::t('t','Hourly rate').': '.(isset($model->task->id)?(int)$model->task->hourlyRate:'?'),
                ),
                array(
                    'align'=>'right',
                    'content'=>$model->getAttributeView('spentMinute'),
                    'title'=>Yii::t('t','Hourly rate').': '.(isset($model->task->id)?(int)$model->task->hourlyRate:'?'),
                ),
            );
            if(!isset($data[$companyId]['total']['billedMinute']))
                $data[$companyId]['total']['billedMinute']=0;
            if(!isset($data[$companyId]['total']['spentMinute']))
                $data[$companyId]['total']['spentMinute']=0;
            if(!isset($data[$companyId]['total']['billedAmount']))
                $data[$companyId]['total']['billedAmount']=0;
            if(!isset($data[$companyId]['total']['spentAmount']))
                $data[$companyId]['total']['spentAmount']=0;
            if(!isset($data[$companyId]['allProject'][$projectId]['total']['billedMinute']))
                $data[$companyId]['allProject'][$projectId]['total']['billedMinute']=0;
            if(!isset($data[$companyId]['allProject'][$projectId]['total']['spentMinute']))
                $data[$companyId]['allProject'][$projectId]['total']['spentMinute']=0;
            if(!isset($data[$companyId]['allProject'][$projectId]['total']['billedAmount']))
                $data[$companyId]['allProject'][$projectId]['total']['billedAmount']=0;
            if(!isset($data[$companyId]['allProject'][$projectId]['total']['spentAmount']))
                $data[$companyId]['allProject'][$projectId]['total']['spentAmount']=0;
            if(!isset($data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['billedMinute']))
                $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['billedMinute']=0;
            if(!isset($data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['spentMinute']))
                $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['spentMinute']=0;
            if(!isset($data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['billedAmount']))
                $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['billedAmount']=0;
            if(!isset($data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['spentAmount']))
                $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['spentAmount']=0;
            $data[$companyId]['total']['billedMinute']+=$model->billedMinute;
            $data[$companyId]['total']['spentMinute']+=$model->spentMinute;
            $data[$companyId]['allProject'][$projectId]['total']['billedMinute']+=$model->billedMinute;
            $data[$companyId]['allProject'][$projectId]['total']['spentMinute']+=$model->spentMinute;
            $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['billedMinute']+=$model->billedMinute;
            $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['spentMinute']+=$model->spentMinute;
            if(isset($model->task->id))
            {
                $data[$companyId]['total']['billedAmount']+=($model->billedMinute/60)*((int)$model->task->hourlyRate);
                $data[$companyId]['total']['spentAmount']+=($model->spentMinute/60)*((int)$model->task->hourlyRate);
                $data[$companyId]['allProject'][$projectId]['total']['billedAmount']+=($model->billedMinute/60)*((int)$model->task->hourlyRate);
                $data[$companyId]['allProject'][$projectId]['total']['spentAmount']+=($model->spentMinute/60)*((int)$model->task->hourlyRate);
                $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['billedAmount']+=($model->billedMinute/60)*((int)$model->task->hourlyRate);
                $data[$companyId]['allProject'][$projectId]['allTask'][$taskId]['total']['spentAmount']+=($model->spentMinute/60)*((int)$model->task->hourlyRate);
            }
        }

        $this->render($this->action->id,array(
            'data'=>$data,
            'models'=>$models,
            'cn','pn','tn',
        ));
    }
}
