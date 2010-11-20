<?php

class ProjectController extends _CController
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

        $with=array('allCompany','allManager');
        if(User::isClient())
            $with[]='allCompany.allUser2Company';
        if(User::isConsultant())
            $with[]='allTask.allConsultant';
        $model=$this->loadModel(array('with'=>$with));
        // may member view this record?
        if(User::isClient())
        {
            $allOwner=array();
            foreach($model->allCompany as $company)
            {
                foreach($company->allUser2Company as $user2Company)
                {
                    if($user2Company->position===Company::OWNER)
                        $allOwner[]=$user2Company->userId;
                }
            }
            if(!in_array(Yii::app()->user->id,$allOwner))
            {
                MUserFlash::setTopError(Yii::t('hint',
                    'We are sorry, but you don\'t have enough rights to view the project record number {id}.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                $this->redirect($this->getGotoUrl());
            }
        }
        if(User::isConsultant())
        {
            $allConsultant=array();
            foreach($model->allTask as $task)
            {
                foreach($task->allConsultant as $consultant)
                    $allConsultant[]=$consultant->id;
            }
            if(!in_array(Yii::app()->user->id,$allConsultant))
            {
                MUserFlash::setTopError(Yii::t('hint',
                    'We are sorry, but you don\'t have enough rights to view the project record number {id}.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
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
        $model=new Project($this->action->id);
        if(isset($_POST['Project']))
        {
            // collect user input data
            $model->attributes=$_POST['Project'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                if(isset($_POST['Company2Project']))
                {
                    // assigned companies
                    $model->allCompany2Project=array(0=>new Company2Project('create'));
                    $model->allCompany2Project[0]->projectId=$model->id;
                    foreach($model->allCompany2Project as $company2Project)
                    {
                        $company2Project->attributes=$_POST['Company2Project'];
                        $company2Project->save();
                    }
                }
                if(isset($_POST['User2Project']))
                {
                    // assigned managers
                    $model->allManager2Project=array(0=>new User2Project('create'));
                    $model->allManager2Project[0]->projectId=$model->id;
                    foreach($model->allManager2Project as $user2Project)
                    {
                        $user2Project->attributes=$_POST['User2Project'];
                        $user2Project->save();
                    }
                }
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The new "{title}" project record has been successfully created.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        else
        {
            // pre-assigned attributes (default values for a new record)
            $model->priority=Project::PRIORITY_MEDIUM;
            if(isset($_GET['companyId']))
            {
                // company is known
                $model->allCompany2Project=array(0=>new Company2Project('create'));
                $model->allCompany2Project[0]->companyId=$_GET['companyId'];
                $model->allCompany2Project[0]->projectId=$model->id;
            }
        }
        if(!isset($model->allCompany2Project[0]))
        {
            // new associated company
            $model->allCompany2Project=array(0=>new Company2Project('create'));
            $model->allCompany2Project[0]->projectId=$model->id;
        }
        if(!isset($model->allManager2Project[0]))
        {
            // new associated manager
            $model->allManager2Project=array(0=>new User2Project('create'));
            $model->allManager2Project[0]->projectId=$model->id;
            $model->allManager2Project[0]->role=User2Project::MANAGER;
        }
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate()
    {
        $model=$this->loadModel(array('with'=>array('allCompany2Project','allManager2Project')));
        if($model===null)
        {
            // model not found
            MUserFlash::setTopError(Yii::t('modelNotFound',$this->id));
            $this->redirect($this->getGotoUrl());
        }
        // explicitly set model scenario to be current action
        //$model->setScenario($this->action->id);
        // whether data is passed
        if(isset($_POST['Project']))
        {
            // collect user input data
            $model->attributes=$_POST['Project'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                if(isset($_POST['Company2Project']))
                {
                    // assigned companies
                    $hasCompany=isset($model->allCompany2Project[0]->id);
                    if(!$hasCompany)
                    {
                        $model->allCompany2Project=array(0=>new Company2Project('create'));
                        $model->allCompany2Project[0]->projectId=$model->id;
                    }
                    foreach($model->allCompany2Project as $company2Project)
                    {
                        // collect user input data
                        $company2Project->attributes=$_POST['Company2Project'];
                        // at the crossroads: which action is expected to be performed
                        if($hasCompany && empty($company2Project->companyId))
                            // un-assigned associated record
                            $company2Project->delete();
                        else
                            // validate and create/update record
                            $company2Project->save();
                    }
                }
                if(isset($_POST['User2Project']))
                {
                    // assigned managers
                    $hasManager=isset($model->allManager2Project[0]->id);
                    if(!$hasManager)
                    {
                        $model->allManager2Project=array(0=>new User2Project('create'));
                        $model->allManager2Project[0]->projectId=$model->id;
                    }
                    foreach($model->allManager2Project as $manager2Project)
                    {
                        // collect user input data
                        $manager2Project->attributes=$_POST['User2Project'];
                        // at the crossroads: which action is expected to be performed
                        if($hasManager && empty($manager2Project->userId))
                            // un-assigned associated record
                            $manager2Project->delete();
                        else
                            // validate and create/update record
                            $manager2Project->save();
                    }
                }
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The "{title}" project record has been updated.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        if(!isset($model->allCompany2Project[0]))
        {
            // new associated company
            $model->allCompany2Project=array(0=>new Company2Project('create'));
            $model->allCompany2Project[0]->projectId=$model->id;
        }
        if(!isset($model->allManager2Project[0]))
        {
            // new associated manager
            $model->allManager2Project=array(0=>new User2Project('create'));
            $model->allManager2Project[0]->projectId=$model->id;
            $model->allManager2Project[0]->role=User2Project::MANAGER;
        }
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Lists all models.
     */
    public function actionList()
    {
        $criteria=new CDbCriteria;
        $criteria->order="`t`.`closeDate` ASC, `t`.`priority` ASC, `t`.`createTime` DESC";

        $pages=new CPagination(Project::model()->count($criteria));
        $pages->pageSize=self::LIST_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=Project::model()->with('allCompany')->findAll($criteria);

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
        $priority=isset($_GET['priority']) ? $_GET['priority'] : null;
        if($priority!=='all' && $priority!==(string)Project::PRIORITY_HIGHEST && $priority!==(string)Project::PRIORITY_HIGH && $priority!==(string)Project::PRIORITY_MEDIUM && $priority!==(string)Project::PRIORITY_LOW && $priority!==(string)Project::PRIORITY_LOWEST)
            $priority='all';
        $state=isset($_GET['state']) ? $_GET['state'] : null;
        if($state!=='all' && $state!=='closed' && $state!=='open')
            $state='all';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.closeDate, `t`.hourlyRate, `t`.openDate, `t`.priority, `t`.title";
        //$criteria->select="`t`.`closeDate`, `t`.`hourlyRate`, `t`.`openDate`, `t`.`priority`, `t`.`title`"; // uncomment in yii-1.1.2
        if($company!=='all')
        {
            $criteria->addCondition("`Project_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if($priority===(string)Project::PRIORITY_HIGHEST)
        {
            $criteria->addCondition("`t`.`priority`=:priorityHighest");
            $criteria->params[':priorityHighest']=Project::PRIORITY_HIGHEST;
        }
        else if($priority===(string)Project::PRIORITY_HIGH)
        {
            $criteria->addCondition("`t`.`priority`=:priorityHigh");
            $criteria->params[':priorityHigh']=Project::PRIORITY_HIGH;
        }
        else if($priority===(string)Project::PRIORITY_MEDIUM)
        {
            $criteria->addCondition("`t`.`priority`=:priorityMedium");
            $criteria->params[':priorityMedium']=Project::PRIORITY_MEDIUM;
        }
        else if($priority===(string)Project::PRIORITY_LOW)
        {
            $criteria->addCondition("`t`.`priority`=:priorityLow");
            $criteria->params[':priorityLow']=Project::PRIORITY_LOW;
        }
        else if($priority===(string)Project::PRIORITY_LOWEST)
        {
            $criteria->addCondition("`t`.`priority`=:priorityLowest");
            $criteria->params[':priorityLowest']=Project::PRIORITY_LOWEST;
        }
        if($state==='closed')
            $criteria->addCondition("(`t`.`closeDate` IS NOT NULL AND TO_DAYS('".MDate::formatToDb(time(),'date')."') <= TO_DAYS(`t`.`closeDate`))");
        else if($state==='open')
            $criteria->addCondition("(`t`.`closeDate` IS NULL OR TO_DAYS('".MDate::formatToDb(time(),'date')."') < TO_DAYS(`t`.`closeDate`))");
        if(User::isClient())
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        if(User::isConsultant())
        {
            $criteria->addCondition("`Task_Consultant`.`id`=:consultantId");
            $criteria->params[':consultantId']=Yii::app()->user->id;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Project_Company')!==false)
            $with[]='allCompany';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='allCompany.allUser2Company';
        if(strpos($criteria->condition,'Task_Consultant')!==false)
            $with[]='allTask.allConsultant';
        if(count($with)>=1)
            $pages=new CPagination(Project::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Project::model()->count($criteria));
        $pages->pageSize=self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Project');
        $sort->attributes=array(
            'closeDate'=>array('asc'=>"`t`.`closeDate`",'desc'=>"`t`.`closeDate` desc",'label'=>Project::model()->getAttributeLabel('Closed')),
            'hourlyRate'=>array('asc'=>"`t`.`hourlyRate`",'desc'=>"`t`.`hourlyRate` desc",'label'=>Project::model()->getAttributeLabel('Rate')),
            'openDate'=>array('asc'=>"`t`.`openDate`",'desc'=>"`t`.`openDate` desc",'label'=>Project::model()->getAttributeLabel('Opened')),
            'priority'=>array('asc'=>"`t`.`priority`",'desc'=>"`t`.`priority` desc",'label'=>Project::model()->getAttributeLabel('priority')),
            'title'=>array('asc'=>"`t`.`title`",'desc'=>"`t`.`title` desc",'label'=>Project::model()->getAttributeLabel('title')),
            'company'=>array('asc'=>"`Project_Company`.`title`",'desc'=>"`Project_Company`.`title` desc",'label'=>Company2Project::model()->getAttributeLabel('companyId')),
        );
        $sort->defaultOrder="`t`.`closeDate` ASC, `t`.`priority` ASC, `t`.`createTime` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('allCompany'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['allCompany.allUser2Company']=array('select'=>'id');
        if(strpos($criteria->condition,'Task_Consultant')!==false)
            $with['allTask.allConsultant']=array('select'=>'id');
        $together=strpos($criteria->condition,'Project_Company')!==false || strpos($criteria->order,'Project_Company')!==false ||
            strpos($criteria->condition,'Company_User2Company')!==false || strpos($criteria->condition,'Task_Consultant')!==false;
        if($together)
            $models=Project::model()->with($with)->together()->findAll($criteria);
        else
            $models=Project::model()->with($with)->findAll($criteria);

        // filters data
        $filters=array('company'=>$company,'priority'=>$priority,'state'=>$state);
        $allCompany=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('company'=>'all'))),
            'active'=>$company==='all'
        ));
        $companyLinkText=$company==='all' ? Yii::t('t','All companies') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.`id`, `t`.`title`, COUNT(DISTINCT `project`.`id`) as countProject";
        if(User::isConsultant())
        {
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`".
                " INNER JOIN `".Task::model()->tableName()."` `task` ON `task`.`projectId`=`project`.`id`".
                " INNER JOIN `".User2Task::model()->tableName()."` `u2t` ON `u2t`.`taskId`=`task`.`id`";
            $criteria->condition="`u2t`.`userId`=:consultantId AND `u2t`.`role`=:consultantRole";
            $criteria->params[':consultantId']=Yii::app()->user->id;
            $criteria->params[':consultantRole']=User2Task::CONSULTANT;
        }
        else if(User::isClient())
        {
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`t`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Company2Project::model()->tableName()."` `c2p` ON `c2p`.`companyId`=`t`.`id`".
                " INNER JOIN `".Project::model()->tableName()."` `project` ON `project`.`id`=`c2p`.`projectId`";
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
            $allCompany[$i]['text'].=' ('.$model->countProject.')';
            if($company===(string)$model->id)
                $companyLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }
        $allPriority=array(
            array(
                'text'=>Yii::t('t','All'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>'all'))),
                'active'=>$priority==='all'
            ),
            array(
                'text'=>Yii::t('t','Highest[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Project::PRIORITY_HIGHEST))),
                'active'=>$priority===(string)Project::PRIORITY_HIGHEST
            ),
            array(
                'text'=>Yii::t('t','High[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Project::PRIORITY_HIGH))),
                'active'=>$priority===(string)Project::PRIORITY_HIGH
            ),
            array(
                'text'=>Yii::t('t','Medium[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Project::PRIORITY_MEDIUM))),
                'active'=>$priority===(string)Project::PRIORITY_MEDIUM
            ),
            array(
                'text'=>Yii::t('t','Low[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Project::PRIORITY_LOW))),
                'active'=>$priority===(string)Project::PRIORITY_LOW
            ),
            array(
                'text'=>Yii::t('t','Lowest[priority]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('priority'=>Project::PRIORITY_LOWEST))),
                'active'=>$priority===(string)Project::PRIORITY_LOWEST
            ),
        );
        switch($priority)
        {
            case 'all':
                $priorityLinkText=Yii::t('t','All priorities');
                break;
            case (string)Project::PRIORITY_HIGHEST:
                $priorityLinkText=Yii::t('t','Highest[priority]');
                break;
            case (string)Project::PRIORITY_HIGH:
                $priorityLinkText=Yii::t('t','High[priority]');
                break;
            case (string)Project::PRIORITY_MEDIUM:
                $priorityLinkText=Yii::t('t','Medium[priority]');
                break;
            case (string)Project::PRIORITY_LOW:
                $priorityLinkText=Yii::t('t','Low[priority]');
                break;
            case (string)Project::PRIORITY_LOWEST:
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
                'text'=>Yii::t('t','Open[projects]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'open'))),
                'active'=>$state==='open'
            ),
            array(
                'text'=>Yii::t('t','Closed[projects]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'closed'))),
                'active'=>$state==='closed'
            ),
        );
        switch($state)
        {
            case 'all':
                $stateLinkText=Yii::t('t','All states[project]');
                break;
            case 'closed':
                $stateLinkText=Yii::t('t','Closed[projects]');
                break;
            case 'open':
                $stateLinkText=Yii::t('t','Open[projects]');
                break;
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
                    'content'=>isset($model->allCompany[0]->id) ? CHtml::link(CHtml::encode($model->allCompany[0]->title),array('company/show','id'=>$model->allCompany[0]->id)) : '',
                ),
                array(
                    'content'=>CHtml::encode($model->title),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode($model->hourlyRate),
                ),
                array(
                    'content'=>CHtml::encode($model->getAttributeView('priority')),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->openDate,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->openDate,'full',null)),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->closeDate,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->closeDate,'full',null)),
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
            'priority'=>$priority,
            'state'=>$state,
            'filters'=>$filters,
            'allCompany'=>$allCompany,
            'companyLinkText'=>$companyLinkText,
            'allPriority'=>$allPriority,
            'priorityLinkText'=>$priorityLinkText,
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
        $priority=isset($_GET['priority']) ? $_GET['priority'] : null;
        if($priority!=='all' && $priority!==(string)Project::PRIORITY_HIGHEST && $priority!==(string)Project::PRIORITY_HIGH && $priority!==(string)Project::PRIORITY_MEDIUM && $priority!==(string)Project::PRIORITY_LOW && $priority!==(string)Project::PRIORITY_LOWEST)
            $priority='all';
        $state=isset($_GET['state']) ? $_GET['state'] : null;
        if($state!=='all' && $state!=='closed' && $state!=='open')
            $state='all';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.closeDate, `t`.hourlyRate, `t`.openDate, `t`.priority, `t`.title";
        //$criteria->select="`t`.`closeDate`, `t`.`hourlyRate`, `t`.`openDate`, `t`.`priority`, `t`.`title`"; // uncomment in yii-1.1.2
        if($jqGrid['searchField']!==null && $jqGrid['searchString']!==null && $jqGrid['searchOper']!==null)
        {
            $field=array(
                'closeDate'=>"`t`.`closeDate`",
                'hourlyRate'=>"`t`.`hourlyRate`",
                'openDate'=>"`t`.`openDate`",
                'priority'=>"`t`.`priority`",
                'title'=>"`t`.`title`",
                'company'=>"`Project_Company`.`title`",
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
            $criteria->addCondition("`Project_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if($priority===(string)Project::PRIORITY_HIGHEST)
        {
            $criteria->addCondition("`t`.`priority`=:priorityHighest");
            $criteria->params[':priorityHighest']=Project::PRIORITY_HIGHEST;
        }
        else if($priority===(string)Project::PRIORITY_HIGH)
        {
            $criteria->addCondition("`t`.`priority`=:priorityHigh");
            $criteria->params[':priorityHigh']=Project::PRIORITY_HIGH;
        }
        else if($priority===(string)Project::PRIORITY_MEDIUM)
        {
            $criteria->addCondition("`t`.`priority`=:priorityMedium");
            $criteria->params[':priorityMedium']=Project::PRIORITY_MEDIUM;
        }
        else if($priority===(string)Project::PRIORITY_LOW)
        {
            $criteria->addCondition("`t`.`priority`=:priorityLow");
            $criteria->params[':priorityLow']=Project::PRIORITY_LOW;
        }
        else if($priority===(string)Project::PRIORITY_LOWEST)
        {
            $criteria->addCondition("`t`.`priority`=:priorityLowest");
            $criteria->params[':priorityLowest']=Project::PRIORITY_LOWEST;
        }
        if($state==='closed')
            $criteria->addCondition("`t`.`closeDate` IS NOT NULL");
        else if($state==='open')
            $criteria->addCondition("(`t`.`closeDate` IS NULL OR TO_DAYS('".MDate::formatToDb(time(),'date')."') < TO_DAYS(`t`.`closeDate`))");
        if(User::isClient())
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        if(User::isConsultant())
        {
            $criteria->addCondition("`Task_Consultant`.`id`=:consultantId");
            $criteria->params[':consultantId']=Yii::app()->user->id;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Project_Company')!==false)
            $with[]='allCompany';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='allCompany.allUser2Company';
        if(strpos($criteria->condition,'Task_Consultant')!==false)
            $with[]='allTask.allConsultant';
        if(count($with)>=1)
            $pages=new CPagination(Project::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Project::model()->count($criteria));
        $pages->pageSize=$jqGrid['pageSize']!==null ? $jqGrid['pageSize'] : self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Project');
        $sort->attributes=array(
            'closeDate'=>array('asc'=>"`t`.`closeDate`",'desc'=>"`t`.`closeDate` desc",'label'=>Project::model()->getAttributeLabel('Closed')),
            'hourlyRate'=>array('asc'=>"`t`.`hourlyRate`",'desc'=>"`t`.`hourlyRate` desc",'label'=>Project::model()->getAttributeLabel('Rate')),
            'openDate'=>array('asc'=>"`t`.`openDate`",'desc'=>"`t`.`openDate` desc",'label'=>Project::model()->getAttributeLabel('Opened')),
            'priority'=>array('asc'=>"`t`.`priority`",'desc'=>"`t`.`priority` desc",'label'=>Project::model()->getAttributeLabel('priority')),
            'title'=>array('asc'=>"`t`.`title`",'desc'=>"`t`.`title` desc",'label'=>Project::model()->getAttributeLabel('title')),
            'company'=>array('asc'=>"`Project_Company`.`title`",'desc'=>"`Project_Company`.`title` desc",'label'=>Company2Project::model()->getAttributeLabel('companyId')),
        );
        $sort->defaultOrder="`t`.`closeDate` ASC, `t`.`priority` ASC, `t`.`createTime` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('allCompany'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['allCompany.allUser2Company']=array('select'=>'id');
        if(strpos($criteria->condition,'Task_Consultant')!==false)
            $with['allTask.allConsultant']=array('select'=>'id');
        $together=strpos($criteria->condition,'Project_Company')!==false || strpos($criteria->order,'Project_Company')!==false ||
            strpos($criteria->condition,'Company_User2Company')!==false || strpos($criteria->condition,'Task_Consultant')!==false;
        if($together)
            $models=Project::model()->with($with)->together()->findAll($criteria);
        else
            $models=Project::model()->with($with)->findAll($criteria);

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
                isset($model->allCompany[0]->id) ? CHtml::link(CHtml::encode($model->allCompany[0]->title),array('company/show','id'=>$model->allCompany[0]->id)) : '',
                CHtml::encode($model->title),
                CHtml::encode($model->hourlyRate),
                CHtml::encode($model->getAttributeView('priority')),
                CHtml::encode(MDate::format($model->openDate,'medium',null)),
                CHtml::encode(MDate::format($model->closeDate,'medium',null)),
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
