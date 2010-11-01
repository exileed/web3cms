<?php

class TaskController extends _CController
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
            array('allow', // allow all users to perform 'show' actions
                'actions'=>array('show'),
                'users'=>array('*'),
            ),
            array('allow', // following actions are checked by {@link checkAccessBeforeAction}
                'actions'=>array('create','grid','gridData','list','update'),
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

        $with=array('allConsultant','allManager','company','project');
        /*if(Yii::app()->user->checkAccess(User::CLIENT))
            $with[]='company.allUser2Company';*/
        $model=$this->loadModel(array('with'=>$with));
        // may member view this record?
        if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            /*$allOwner=array();
            if(isset($model->company->allUser2Company))
            {
                foreach($model->company->allUser2Company as $user2Company)
                {
                    if($user2Company->position===Company::OWNER)
                        $allOwner[]=$user2Company->userId;
                }
            }
            if(!in_array(Yii::app()->user->id,$allOwner))*/
            if(!isset($model->company->id) || !$model->company->isOwner())
            {
                MUserFlash::setTopError(Yii::t('accessDenied',$this->route,array('{id}'=>MHtml::wrapInTag($model->id,'strong'))));
                $this->redirect($this->getGotoUrl());
            }
        }
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $allConsultant=array();
            foreach($model->allConsultant as $consultant)
                $allConsultant[]=$consultant->id;
            if(!in_array(Yii::app()->user->id,$allConsultant))
            {
                MUserFlash::setTopError(Yii::t('accessDenied',$this->route,array('{id}'=>MHtml::wrapInTag($model->id,'strong'))));
                $this->redirect($this->getGotoUrl());
            }
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
        $model=new Task($this->action->id);
        if(isset($_POST['Task']))
        {
            // collect user input data
            $model->attributes=$_POST['Task'];
            if(!isset($_POST['Task']['companyId']))
            {
                // set company based on the project
                if($model->projectId>=1)
                {
                    $criteria=new CDbCriteria;
                    $criteria->order="`t`.`companyPriority` ASC, `t`.`id` ASC";
                    if(($company2Project=Company2Project::model()->findByAttributes(array('projectId'=>$model->projectId),$criteria))!==null)
                        $model->companyId=$company2Project->companyId;
                    else
                        $model->companyId=0;
                }
                else
                    $model->companyId=0;
            }
            if($model->projectId>=1)
            {
                // set project's hourly rate
                if(($project=Project::model()->findByPk($model->projectId))!==null)
                    $model->hourlyRate=$project->hourlyRate;
            }
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                if(isset($_POST['User2Task']))
                {
                    // assigned consultants
                    $model->allConsultant2Task=array(0=>new User2Task('create'));
                    $model->allConsultant2Task[0]->taskId=$model->id;
                    foreach($model->allConsultant2Task as $user2Task)
                    {
                        $user2Task->attributes=$_POST['User2Task'];
                        $user2Task->save();
                    }
                }
                if($model->projectId>=1)
                {
                    // new relation between task and manager
                    $criteria=new CDbCriteria;
                    $criteria->order="`t`.`userPriority` ASC, `t`.`id` ASC";
                    if(($user2Project=User2Project::model()->findByAttributes(array('projectId'=>$model->projectId,'role'=>User2Project::MANAGER),$criteria))!==null)
                    {
                        $user2Task=new User2Task('create');
                        $user2Task->userId=$user2Project->userId;
                        $user2Task->taskId=$model->id;
                        $user2Task->role=User2Task::MANAGER;
                        $user2Task->save();
                    }
                }
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The new "{title}" task record has been successfully created.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        else
        {
            // pre-assigned attributes (default values for a new record)
            $model->dueDate=MDate::formatToDb(time()+14*86400,'date');
            $model->openDate=MDate::formatToDb(time(),'date');
            $model->priority=Task::PRIORITY_MEDIUM;
            $model->status=Task::NOT_STARTED;
            if(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR))
                $model->isConfirmed=Task::IS_CONFIRMED;
            if(isset($_GET['projectId']))
                // project is known
                $model->projectId=$_GET['projectId'];
        }
        if(!isset($model->allConsultant2Task[0]))
        {
            // new associated consultant
            $model->allConsultant2Task=array(0=>new User2Task('create'));
            $model->allConsultant2Task[0]->taskId=$model->id;
            $model->allConsultant2Task[0]->role=User2Task::CONSULTANT;
        }
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate()
    {
        if(($model=$this->loadModel(array('with'=>array('allConsultant2Task'))))===null)
        {
            // model not found
            MUserFlash::setTopError(Yii::t('modelNotFound',$this->id));
            $this->redirect($this->getGotoUrl());
        }
        // explicitly set model scenario to be current action
        //$model->setScenario($this->action->id);
        // whether data is passed
        if(isset($_POST['Task']))
        {
            // collect user input data
            $model->attributes=$_POST['Task'];
            if(!isset($_POST['Task']['companyId']))
            {
                // set company based on the project
                if($model->projectId>=1)
                {
                    $criteria=new CDbCriteria;
                    $criteria->order="`t`.`companyPriority` ASC, `t`.`id` ASC";
                    if(($company2Project=Company2Project::model()->findByAttributes(array('projectId'=>$model->projectId),$criteria))!==null)
                        $model->companyId=$company2Project->companyId;
                    else
                        $model->companyId=0;
                }
                else
                    $model->companyId=0;
            }
            if($model->projectId>=1 && empty($model->hourlyRate))
            {
                // set project's hourly rate
                if(($project=Project::model()->findByPk($model->projectId))!==null)
                    $model->hourlyRate=$project->hourlyRate;
            }
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                if(isset($_POST['User2Task']))
                {
                    // assigned consultants
                    $hasConsultant=isset($model->allConsultant2Task[0]->id);
                    if(!$hasConsultant)
                    {
                        $model->allConsultant2Task=array(0=>new User2Task('create'));
                        $model->allConsultant2Task[0]->taskId=$model->id;
                    }
                    foreach($model->allConsultant2Task as $consultant2Task)
                    {
                        // collect user input data
                        $consultant2Task->attributes=$_POST['User2Task'];
                        // at the crossroads: which action is expected to be performed
                        if($hasConsultant && empty($consultant2Task->userId))
                            // un-assigned associated record
                            $consultant2Task->delete();
                        else
                            // validate and create/update record
                            $consultant2Task->save();
                    }
                }
                if(($managers2Task=User2Task::model()->findAllByAttributes(array('taskId'=>$model->id,'role'=>User2Task::MANAGER)))!==array())
                {
                    // delete existing relation(s) between task and manager
                    foreach($managers2Task as $manager2Task)
                        $manager2Task->delete();
                }
                if($model->projectId>=1)
                {
                    // new relation between task and manager
                    $criteria=new CDbCriteria;
                    $criteria->order="`t`.`userPriority` ASC, `t`.`id` ASC";
                    if(($user2Project=User2Project::model()->findByAttributes(array('projectId'=>$model->projectId,'role'=>User2Project::MANAGER),$criteria))!==null)
                    {
                        $user2Task=new User2Task('create');
                        $user2Task->userId=$user2Project->userId;
                        $user2Task->taskId=$model->id;
                        $user2Task->role=User2Task::MANAGER;
                        $user2Task->save();
                    }
                }
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The "{title}" task record has been updated.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        if(!isset($model->allConsultant2Task[0]))
        {
            // new associated consultant
            $model->allConsultant2Task=array(0=>new User2Task('create'));
            $model->allConsultant2Task[0]->taskId=$model->id;
            $model->allConsultant2Task[0]->role=User2Task::CONSULTANT;
        }
        // prepare model data for the form
        $model->estimateH=floor($model->estimateMinute/60);
        $model->estimateM=fmod($model->estimateMinute,60);
        // render the view file
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Lists all models.
     */
    public function actionList()
    {
        $criteria=new CDbCriteria;
        $criteria->order="`t`.`closeDate` ASC, `t`.`priority` ASC, `t`.`dueDate` DESC";

        $pages=new CPagination(Task::model()->count($criteria));
        $pages->pageSize=self::LIST_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=Task::model()->with('allConsultant','company','project')->findAll($criteria);

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
        if($consultant!=='all' && $consultant!=='me' && $consultant!=='none' && !ctype_digit($consultant))
            $consultant='all';
        if(Yii::app()->user->checkAccess(User::CONSULTANT) && $consultant!=='none')
            $consultant='me';
        $priority=isset($_GET['priority']) ? $_GET['priority'] : null;
        if($priority!=='all' && $priority!==(string)Task::PRIORITY_HIGHEST && $priority!==(string)Task::PRIORITY_HIGH && $priority!==(string)Task::PRIORITY_MEDIUM && $priority!==(string)Task::PRIORITY_LOW && $priority!==(string)Task::PRIORITY_LOWEST)
            $priority='all';
        $project=isset($_GET['project']) ? $_GET['project'] : null;
        if($project!=='all' && !ctype_digit($project))
            $project='all';
        $state=isset($_GET['state']) ? $_GET['state'] : null;
        if(Yii::app()->user->checkAccess(User::CLIENT) && $state===null)
            $state='all';
        if($state!=='all' && $state!=='closed' && $state!=='open' && $state!=='overdue')
            $state='open';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.dueDate, `t`.priority, `t`.status, `t`.title";
        //$criteria->select="`t`.`dueDate`, `t`.`priority`, `t`.`status`, `t`.`title`"; // uncomment in yii-1.1.2
        if($company!=='all')
        {
            $criteria->addCondition("`Task_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if($consultant!=='all')
        {
            if($consultant==='none')
            {
                $criteria->addCondition("NOT EXISTS (SELECT 1 FROM `".User2Task::model()->tableName()."` `u2t` WHERE `u2t`.`taskId`=`t`.`id` AND `u2t`.`role`=:consultantRole)");
                $criteria->params[':consultantRole']=User2Task::CONSULTANT;
            }
            else if($consultant==='me')
            {
                $criteria->addCondition("`Task_Consultant`.`id`=:consultantId");
                $criteria->params[':consultantId']=Yii::app()->user->id;
            }
            else
            {
                $criteria->addCondition("`Task_Consultant`.`id`=:consultantId");
                $criteria->params[':consultantId']=$consultant;
            }
        }
        if($priority===(string)Task::PRIORITY_HIGHEST)
        {
            $criteria->addCondition("`t`.`priority`=:priorityHighest");
            $criteria->params[':priorityHighest']=Task::PRIORITY_HIGHEST;
        }
        else if($priority===(string)Task::PRIORITY_HIGH)
        {
            $criteria->addCondition("`t`.`priority`=:priorityHigh");
            $criteria->params[':priorityHigh']=Task::PRIORITY_HIGH;
        }
        else if($priority===(string)Task::PRIORITY_MEDIUM)
        {
            $criteria->addCondition("`t`.`priority`=:priorityMedium");
            $criteria->params[':priorityMedium']=Task::PRIORITY_MEDIUM;
        }
        else if($priority===(string)Task::PRIORITY_LOW)
        {
            $criteria->addCondition("`t`.`priority`=:priorityLow");
            $criteria->params[':priorityLow']=Task::PRIORITY_LOW;
        }
        else if($priority===(string)Task::PRIORITY_LOWEST)
        {
            $criteria->addCondition("`t`.`priority`=:priorityLowest");
            $criteria->params[':priorityLowest']=Task::PRIORITY_LOWEST;
        }
        if($project!=='all')
        {
            $criteria->addCondition("`Task_Project`.`id`=:projectId");
            $criteria->params[':projectId']=$project;
        }
        if($state==='closed')
            $criteria->addCondition("((`t`.`closeDate` IS NOT NULL AND TO_DAYS('".MDate::formatToDb(time(),'date')."') <= TO_DAYS(`t`.`closeDate`))").
            " OR `t`.`status`='".Task::CANCELLED."' OR `t`.`status`='".Task::COMPLETED."')";
        else if($state==='open')
            $criteria->addCondition("((`t`.`closeDate` IS NULL OR TO_DAYS('".MDate::formatToDb(time(),'date')."') < TO_DAYS(`t`.`closeDate`))".
            " AND `t`.`status`!='".Task::CANCELLED."' AND `t`.`status`!='".Task::COMPLETED."')");
        else if($state==='overdue')
            $criteria->addCondition("(((`t`.`closeDate` IS NULL OR TO_DAYS('".MDate::formatToDb(time(),'date')."') < TO_DAYS(`t`.`closeDate`))".
            " AND `t`.`status`!='".Task::CANCELLED."' AND `t`.`status`!='".Task::COMPLETED."') AND TO_DAYS('".MDate::formatToDb(time(),'date')."') >= TO_DAYS(`t`.`dueDate`))");
        if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Task_Company')!==false)
            $with[]='company';
        if(strpos($criteria->condition,'Task_Consultant')!==false)
            $with[]='allConsultant';
        if(strpos($criteria->condition,'Task_Project')!==false)
            $with[]='project';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='company.allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Task::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Task::model()->count($criteria));
        $pages->pageSize=self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Task');
        $sort->attributes=array(
            'dueDate'=>array('asc'=>"`t`.`dueDate`",'desc'=>"`t`.`dueDate` desc",'label'=>Task::model()->getAttributeLabel('Due')),
            'priority'=>array('asc'=>"`t`.`priority`",'desc'=>"`t`.`priority` desc",'label'=>Task::model()->getAttributeLabel('Pr')),
            'status'=>array('asc'=>"`t`.`status`",'desc'=>"`t`.`status` desc",'label'=>Task::model()->getAttributeLabel('status')),
            'title'=>array('asc'=>"`t`.`title`",'desc'=>"`t`.`title` desc",'label'=>Task::model()->getAttributeLabel('title')),
            'company'=>array('asc'=>"`Task_Company`.`title`",'desc'=>"`Task_Company`.`title` desc",'label'=>Task::model()->getAttributeLabel('companyId')),
            'consultant'=>array('asc'=>"`Task_Consultant`.`screenName`",'desc'=>"`Task_Consultant`.`screenName` desc",'label'=>User::model()->getAttributeLabel('screenName')),
            'project'=>array('asc'=>"`Task_Project`.`title`",'desc'=>"`Task_Project`.`title` desc",'label'=>Task::model()->getAttributeLabel('projectId')),
        );
        $sort->defaultOrder="`t`.`closeDate` ASC, `t`.`priority` ASC, `t`.`dueDate` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('allConsultant'=>array('select'=>'screenName'),'company'=>array('select'=>'title'),'project'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['company.allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Task_Consultant')!==false || strpos($criteria->order,'Task_Consultant')!==false ||
            strpos($criteria->condition,'Company_User2Company')!==false || strpos($criteria->order,'Company_User2Company')!==false;
        if($together)
            $models=Task::model()->with($with)->together()->findAll($criteria);
        else
            $models=Task::model()->with($with)->findAll($criteria);

        // filters data
        $filters=array('company'=>$company,'project'=>$project,'consultant'=>$consultant,'priority'=>$priority,'state'=>$state);
        $allCompany=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('company'=>'all'))),
            'active'=>$company==='all'
        ));
        $companyLinkText=$company==='all' ? Yii::t('t','All companies') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.`id`, `t`.`title`, COUNT(`task`.`id`) as countTask";
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`".
                " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`project`.`id`".
                " INNER JOIN `".User2Task::model()->tableName()."` `u2t` ON `u2t`.`taskId`=`task`.`id`";
            $criteria->condition="`u2t`.`userId`=:consultantId AND `u2t`.`role`=:consultantRole";
            $criteria->params[':consultantId']=Yii::app()->user->id;
            $criteria->params[':consultantRole']=User2Task::CONSULTANT;
        }
        else if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`".
                " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`project`.`id`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`t`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`".
                " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`project`.`id`";
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
            $allCompany[$i]['text'].=' ('.$model->countTask.')';
            if($company===(string)$model->id)
                $companyLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }
        $allConsultant=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('consultant'=>'all'))),
            'active'=>$consultant==='all'
        ),array(
            'text'=>Yii::t('t','Unassigned[tasks]'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('consultant'=>'none'))),
            'active'=>$consultant==='none'
        ));
        $consultantLinkText=$consultant==='all' ? Yii::t('t','All leaders') : '&nbsp;';
        if($consultant==='none')
            $consultantLinkText=Yii::t('t','Unassigned[tasks]');
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $allConsultant[0]=array(
                'text'=>Yii::t('t','My tasks'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('consultant'=>'me'))),
                'active'=>$consultant==='me'
            );
            if($consultant==='me')
                $consultantLinkText=Yii::t('t','My tasks');
        }
        else
        {
            $criteria=new CDbCriteria;
            $criteria->select="`t`.`id`, `t`.`screenName`, COUNT(`task`.`id`) as countTask";
            if(Yii::app()->user->checkAccess(User::CLIENT))
            {
                $criteria->join="INNER JOIN `".User2Task::model()->tableName()."` `u2t` ON `u2t`.`userId`=`t`.`id`".
                    " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`id`=`u2t`.`taskId`".
                    " INNER JOIN `".Company::model()->tableName()."` `company` ON `company`.`id`=`task`.`companyId`".
                    " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`company`.`id`";
                $criteria->condition="`u2t`.`role`=:consultantRole AND `u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
                $criteria->params[':consultantRole']=User2Task::CONSULTANT;
                $criteria->params[':clientId']=Yii::app()->user->id;
                $criteria->params[':clientPosition']=Company::OWNER;
            }
            else
            {
                $criteria->join="INNER JOIN `".User2Task::model()->tableName()."` `u2t` ON `u2t`.`userId`=`t`.`id` INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`id`=`u2t`.`taskId`";
                $criteria->condition="`u2t`.`role`=:consultantRole";
                $criteria->params[':consultantRole']=User2Task::CONSULTANT;
            }
            $criteria->group="`t`.`id`";
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
                $allConsultant[$i]['text'].=' ('.$model->countTask.')';
                if($consultant===(string)$model->id)
                    $consultantLinkText=$model->screenName===''?Yii::t('t','[no name]'):$model->screenName;
            }
        }
        $allProject=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('project'=>'all'))),
            'active'=>$project==='all'
        ));
        $projectLinkText=$project==='all' ? Yii::t('t','All projects') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.id, `t`.title, COUNT(`task`.`id`) as countTask";
        //$criteria->select="`t`.`id`, `t`.`title`, COUNT(`task`.`id`) as countTask"; // uncomment in yii-1.1.2
        if(Yii::app()->user->checkAccess(User::CONSULTANT))
        {
            $criteria->join="INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`t`.`id`".
                " INNER JOIN `".User2Task::model()->tableName()."` `u2t` ON `u2t`.`taskId`=`task`.`id`";
            $criteria->condition="`u2t`.`userId`=:consultantId AND `u2t`.`role`=:consultantRole";
            $criteria->params[':consultantId']=Yii::app()->user->id;
            $criteria->params[':consultantRole']=User2Task::CONSULTANT;
        }
        else if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->join="INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`t`.`id`".
                " INNER JOIN `".Company::model()->tableName()."` `company` ON `company`.`id`=`task`.`companyId`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`company`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`t`.`id`";
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
            $allProject[$i]['text'].=' ('.$model->countTask.')';
            if($project===(string)$model->id)
                $projectLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }
        $allPriority=array(
            array(
                'text'=>Yii::t('t','All'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>'all'))),
                'active'=>$priority==='all'
            ),
            array(
                'text'=>Yii::t('t','Highest[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Task::PRIORITY_HIGHEST))),
                'active'=>$priority===(string)Task::PRIORITY_HIGHEST
            ),
            array(
                'text'=>Yii::t('t','High[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Task::PRIORITY_HIGH))),
                'active'=>$priority===(string)Task::PRIORITY_HIGH
            ),
            array(
                'text'=>Yii::t('t','Medium[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Task::PRIORITY_MEDIUM))),
                'active'=>$priority===(string)Task::PRIORITY_MEDIUM
            ),
            array(
                'text'=>Yii::t('t','Low[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Task::PRIORITY_LOW))),
                'active'=>$priority===(string)Task::PRIORITY_LOW
            ),
            array(
                'text'=>Yii::t('t','Lowest[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Task::PRIORITY_LOWEST))),
                'active'=>$priority===(string)Task::PRIORITY_LOWEST
            ),
        );
        switch($priority)
        {
            case 'all':
                $priorityLinkText=Yii::t('t','All priorities');
                break;
            case (string)Task::PRIORITY_HIGHEST:
                $priorityLinkText=Yii::t('t','Highest[priority]');
                break;
            case (string)Task::PRIORITY_HIGH:
                $priorityLinkText=Yii::t('t','High[priority]');
                break;
            case (string)Task::PRIORITY_MEDIUM:
                $priorityLinkText=Yii::t('t','Medium[priority]');
                break;
            case (string)Task::PRIORITY_LOW:
                $priorityLinkText=Yii::t('t','Low[priority]');
                break;
            case (string)Task::PRIORITY_LOWEST:
                $priorityLinkText=Yii::t('t','Lowest[priority]');
                break;
            default:
                $priorityLinkText='&nbsp;';
        }
        $allState=array(
            array(
                'text'=>Yii::t('t','All'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'all'))),
                'active'=>$state==='all'
            ),
            array(
                'text'=>Yii::t('t','Open[tasks]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'open'))),
                'active'=>$state==='open'
            ),
            array(
                'text'=>Yii::t('t','Closed[tasks]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'closed'))),
                'active'=>$state==='closed'
            ),
            array(
                'text'=>Yii::t('t','Overdue[tasks]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'overdue'))),
                'active'=>$state==='overdue'
            ),
        );
        switch($state)
        {
            case 'all':
                $stateLinkText=Yii::t('t','All states[task]');
                break;
            case 'closed':
                $stateLinkText=Yii::t('t','Closed[tasks]');
                break;
            case 'open':
                $stateLinkText=Yii::t('t','Open[tasks]');
                break;
            case 'overdue':
                $stateLinkText=Yii::t('t','Overdue[tasks]');
                break;
            default:
                $stateLinkText='&nbsp;';
        }

        // rows for the static grid
        $gridRows=array();
        foreach($models as $model)
        {
            $gridRows[]=array(
                array(
                    'content'=>isset($model->company->id) ? CHtml::link(CHtml::encode($model->company->title),array('company/show','id'=>$model->company->id)) : '',
                ),
                array(
                    'content'=>isset($model->project->id) ? CHtml::link(CHtml::encode($model->project->title),array('project/show','id'=>$model->project->id)) : '',
                ),
                array(
                    'content'=>CHtml::encode($model->getAttributeView('priority')),
                ),
                array(
                    'content'=>CHtml::encode($model->title),
                ),
                array(
                    'content'=>isset($model->allConsultant[0]->id) ? CHtml::link(CHtml::encode($model->allConsultant[0]->screenName),array('user/show','id'=>$model->allConsultant[0]->id)) : '',
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->dueDate,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->dueDate,'full',null)),
                ),
                array(
                    'content'=>CHtml::encode($model->getAttributeView('status')),
                ),
                array(
                    'content'=>
                        CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                            'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                            'title'=>Yii::t('link','Show')
                        )).
                        CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                            'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-last ui-corner-all',
                            'title'=>Yii::t('link','Edit')
                        )),
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
            'priority'=>$priority,
            'project'=>$project,
            'state'=>$state,
            'filters'=>$filters,
            'allCompany'=>$allCompany,
            'companyLinkText'=>$companyLinkText,
            'allConsultant'=>$allConsultant,
            'consultantLinkText'=>$consultantLinkText,
            'allPriority'=>$allPriority,
            'priorityLinkText'=>$priorityLinkText,
            'allProject'=>$allProject,
            'projectLinkText'=>$projectLinkText,
            'allState'=>$allState,
            'stateLinkText'=>$stateLinkText,
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
        if($consultant!=='all' && $consultant!=='me' && $consultant!=='none' && !ctype_digit($consultant))
            $consultant='all';
        if(Yii::app()->user->checkAccess(User::CONSULTANT) && $consultant!=='none')
            $consultant='me';
        $priority=isset($_GET['priority']) ? $_GET['priority'] : null;
        if($priority!=='all' && $priority!==(string)Task::PRIORITY_HIGHEST && $priority!==(string)Task::PRIORITY_HIGH && $priority!==(string)Task::PRIORITY_MEDIUM && $priority!==(string)Task::PRIORITY_LOW && $priority!==(string)Task::PRIORITY_LOWEST)
            $priority='all';
        $project=isset($_GET['project']) ? $_GET['project'] : null;
        if($project!=='all' && !ctype_digit($project))
            $project='all';
        $state=isset($_GET['state']) ? $_GET['state'] : null;
        if(Yii::app()->user->checkAccess(User::CLIENT) && $state===null)
            $state='all';
        if($state!=='all' && $state!=='closed' && $state!=='open' && $state!=='overdue')
            $state='open';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.dueDate, `t`.priority, `t`.status, `t`.title";
        //$criteria->select="`t`.`dueDate`, `t`.`priority`, `t`.`status`, `t`.`title`"; // uncomment in yii-1.1.2
        if($jqGrid['searchField']!==null && $jqGrid['searchString']!==null && $jqGrid['searchOper']!==null)
        {
            $field=array(
                'dueDate'=>"`t`.`dueDate`",
                'priority'=>"`t`.`priority`",
                'status'=>"`t`.`status`",
                'title'=>"`t`.`title`",
                'company'=>"`Task_Company`.`title`",
                'project'=>"`Task_Project`.`title`",
                'consultant'=>"`Task_Consultant`.`screenName`",
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
            if($consultant==='none')
            {
                $criteria->addCondition("NOT EXISTS (SELECT 1 FROM `".User2Task::model()->tableName()."` `u2t` WHERE `u2t`.`taskId`=`t`.`id` AND `u2t`.`role`=:consultantRole)");
                $criteria->params[':consultantRole']=User2Task::CONSULTANT;
            }
            else if($consultant==='me')
            {
                $criteria->addCondition("`Task_Consultant`.`id`=:consultantId");
                $criteria->params[':consultantId']=Yii::app()->user->id;
            }
            else
            {
                $criteria->addCondition("`Task_Consultant`.`id`=:consultantId");
                $criteria->params[':consultantId']=$consultant;
            }
        }
        if($priority===(string)Task::PRIORITY_HIGHEST)
        {
            $criteria->addCondition("`t`.`priority`=:priorityHighest");
            $criteria->params[':priorityHighest']=Task::PRIORITY_HIGHEST;
        }
        else if($priority===(string)Task::PRIORITY_HIGH)
        {
            $criteria->addCondition("`t`.`priority`=:priorityHigh");
            $criteria->params[':priorityHigh']=Task::PRIORITY_HIGH;
        }
        else if($priority===(string)Task::PRIORITY_MEDIUM)
        {
            $criteria->addCondition("`t`.`priority`=:priorityMedium");
            $criteria->params[':priorityMedium']=Task::PRIORITY_MEDIUM;
        }
        else if($priority===(string)Task::PRIORITY_LOW)
        {
            $criteria->addCondition("`t`.`priority`=:priorityLow");
            $criteria->params[':priorityLow']=Task::PRIORITY_LOW;
        }
        else if($priority===(string)Task::PRIORITY_LOWEST)
        {
            $criteria->addCondition("`t`.`priority`=:priorityLowest");
            $criteria->params[':priorityLowest']=Task::PRIORITY_LOWEST;
        }
        if($project!=='all')
        {
            $criteria->addCondition("`Task_Project`.`id`=:projectId");
            $criteria->params[':projectId']=$project;
        }
        if($state==='closed')
            $criteria->addCondition("((`t`.`closeDate` IS NOT NULL AND TO_DAYS('".MDate::formatToDb(time(),'date')."') <= TO_DAYS(`t`.`closeDate`))".
            " OR `t`.`status`='".Task::CANCELLED."' OR `t`.`status`='".Task::COMPLETED."')");
        else if($state==='open')
            $criteria->addCondition("((`t`.`closeDate` IS NULL OR TO_DAYS('".MDate::formatToDb(time(),'date')."') < TO_DAYS(`t`.`closeDate`))".
            " AND `t`.`status`!='".Task::CANCELLED."' AND `t`.`status`!='".Task::COMPLETED."')");
        else if($state==='overdue')
            $criteria->addCondition("(((`t`.`closeDate` IS NULL OR TO_DAYS('".MDate::formatToDb(time(),'date')."') < TO_DAYS(`t`.`closeDate`))".
            " AND `t`.`status`!='".Task::CANCELLED."' AND `t`.`status`!='".Task::COMPLETED."')".
            " AND TO_DAYS('".MDate::formatToDb(time(),'date')."') >= TO_DAYS(`t`.`dueDate`))");
        if(Yii::app()->user->checkAccess(User::CLIENT))
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Task_Company')!==false)
            $with[]='company';
        if(strpos($criteria->condition,'Task_Consultant')!==false)
            $with[]='allConsultant';
        if(strpos($criteria->condition,'Task_Project')!==false)
            $with[]='project';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='company.allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Task::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Task::model()->count($criteria));
        $pages->pageSize=$jqGrid['pageSize']!==null ? $jqGrid['pageSize'] : self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Task');
        $sort->attributes=array(
            'dueDate'=>array('asc'=>"`t`.`dueDate`",'desc'=>"`t`.`dueDate` desc",'label'=>Task::model()->getAttributeLabel('Due')),
            'priority'=>array('asc'=>"`t`.`priority`",'desc'=>"`t`.`priority` desc",'label'=>Task::model()->getAttributeLabel('Pr')),
            'status'=>array('asc'=>"`t`.`status`",'desc'=>"`t`.`status` desc",'label'=>Task::model()->getAttributeLabel('status')),
            'title'=>array('asc'=>"`t`.`title`",'desc'=>"`t`.`title` desc",'label'=>Task::model()->getAttributeLabel('title')),
            'company'=>array('asc'=>"`Task_Company`.`title`",'desc'=>"`Task_Company`.`title` desc",'label'=>Task::model()->getAttributeLabel('companyId')),
            'consultant'=>array('asc'=>"`Task_Consultant`.`screenName`",'desc'=>"`Task_Consultant`.`screenName` desc",'label'=>User::model()->getAttributeLabel('screenName')),
            'project'=>array('asc'=>"`Task_Project`.`title`",'desc'=>"`Task_Project`.`title` desc",'label'=>Task::model()->getAttributeLabel('projectId')),
        );
        $sort->defaultOrder="`t`.`closeDate` ASC, `t`.`priority` ASC, `t`.`dueDate` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('allConsultant'=>array('select'=>'screenName'),'company'=>array('select'=>'title'),'project'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['company.allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Task_Consultant')!==false || strpos($criteria->order,'Task_Consultant')!==false ||
            strpos($criteria->condition,'Company_User2Company')!==false || strpos($criteria->order,'Company_User2Company')!==false;
        if($together)
            $models=Task::model()->with($with)->together()->findAll($criteria);
        else
            $models=Task::model()->with($with)->findAll($criteria);

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
                isset($model->company->id) ? CHtml::link(CHtml::encode($model->company->title),array('company/show','id'=>$model->company->id)) : '',
                isset($model->project->id) ? CHtml::link(CHtml::encode($model->project->title),array('project/show','id'=>$model->project->id)) : '',
                CHtml::encode($model->getAttributeView('priority')),
                CHtml::encode($model->title),
                isset($model->allConsultant[0]->id) ? CHtml::link(CHtml::encode($model->allConsultant[0]->screenName),array('user/show','id'=>$model->allConsultant[0]->id)) : '',
                CHtml::encode(MDate::format($model->dueDate,'medium',null)),
                CHtml::encode($model->getAttributeView('status')),
                CHtml::link('<span class="ui-icon ui-icon-zoomin"></span>',array('show','id'=>$model->id),array(
                    'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-first ui-corner-all',
                    'title'=>Yii::t('link','Show')
                )).
                CHtml::link('<span class="ui-icon ui-icon-pencil"></span>',array('update','id'=>$model->id),array(
                    'class'=>'w3-ig w3-link-icon w3-border-1px-transparent w3-last ui-corner-all',
                    'title'=>Yii::t('link','Edit')
                )),
            ));
        }
        $this->printJson($data);
    }
}
