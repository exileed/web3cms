<?php

class ExpenseController extends _CController
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
        if(!User::isClient() && !User::isManager() && !User::isAdministrator())
        {
            // not enough rights
            MUserFlash::setTopError(Yii::t('hint','We are sorry, but you don\'t have enough rights to browse expenses.'));
            $this->redirect($this->getGotoUrl());
        }

        $with=array('company','invoice','manager','project');
        /*if(User::isClient())
            $with[]='company.allUser2Company';*/
        $model=$this->loadModel(array('with'=>$with));
        // may member view this record?
        if(User::isClient())
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
                MUserFlash::setTopError(Yii::t('hint',
                    'We are sorry, but you don\'t have enough rights to view the expense record number {id}.',
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
        $model=new Expense($this->action->id);
        if(isset($_POST['Expense']))
        {
            // collect user input data
            $model->attributes=$_POST['Expense'];
            if(!isset($_POST['Expense']['companyId']))
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
            if(!isset($_POST['Expense']['managerId']))
                // current user is considered to be manager
                $model->managerId=Yii::app()->user->id;
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The new expense record number "{expenseNumber}" has been successfully created.',
                    array('{expenseNumber}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        else
        {
            // pre-assigned attributes (default values for a new record)
            $model->billToCompany=Expense::BILL_TO_COMPANY;
            // current user is considered to be manager
            $model->managerId=Yii::app()->user->id;
            if(isset($_GET['projectId']))
                // project is known
                $model->projectId=$_GET['projectId'];
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
        if(isset($_POST['Expense']))
        {
            // collect user input data
            $model->attributes=$_POST['Expense'];
            if(!isset($_POST['Expense']['companyId']))
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
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The expense record number "{expenseNumber}" has been updated.',
                    array('{expenseNumber}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
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
                    'The expense record number {id} has been successfully deleted.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                // go to the controller default action
                $this->redirect(array($this->id.'/'));
            }
            else
            {
                // set error message
                MUserFlash::setTopError(Yii::t('hint',
                    'Error! The expense record number {id} could not be deleted.',
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
                'message'=>Yii::t('hint','The expense record number {id} has been successfully deleted.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                )
            ));
        else
        {
            // error
            $this->printJson(array('status'=>'error',
                'message'=>Yii::t('hint','Error! The expense record number {id} could not be deleted.',
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
        $criteria->order="`t`.`expenseDate` DESC, `t`.`id` DESC";

        $pages=new CPagination(Expense::model()->count($criteria));
        $pages->pageSize=self::LIST_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=Expense::model()->with('company','invoice','manager','project')->findAll($criteria);

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
        $project=isset($_GET['project']) ? $_GET['project'] : null;
        if($project!=='all' && !ctype_digit($project))
            $project='all';
        $billToCompany=isset($_GET['billToCompany']) ? $_GET['billToCompany'] : null;
        if($billToCompany!=='all' && $billToCompany!==(string)Expense::BILL_TO_COMPANY && $billToCompany!==(string)Expense::DO_NOT_BILL_TO_COMPANY)
            $billToCompany='all';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.amount, `t`.billToCompany, `t`.expenseDate";
        //$criteria->select="`t`.`amount`, `t`.`billToCompany`, `t`.`expenseDate`"; // uncomment in yii-1.1.2
        if($company!=='all')
        {
            $criteria->addCondition("`Expense_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if($project!=='all')
        {
            $criteria->addCondition("`Expense_Project`.`id`=:projectId");
            $criteria->params[':projectId']=$project;
        }
        if($billToCompany===(string)Expense::BILL_TO_COMPANY)
        {
            $criteria->addCondition("`t`.`billToCompany`=:billToCompany");
            $criteria->params[':billToCompany']=Expense::BILL_TO_COMPANY;
        }
        else if($billToCompany===(string)Expense::DO_NOT_BILL_TO_COMPANY)
        {
            $criteria->addCondition("`t`.`billToCompany`=:doNotBillToCompany");
            $criteria->params[':doNotBillToCompany']=Expense::DO_NOT_BILL_TO_COMPANY;
        }
        if(User::isClient())
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Expense_Company')!==false)
            $with[]='company';
        if(strpos($criteria->condition,'Expense_Project')!==false)
            $with[]='project';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='company.allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Expense::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Expense::model()->count($criteria));
        $pages->pageSize=self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Expense');
        $sort->attributes=array(
            'amount'=>array('asc'=>"`t`.`amount`",'desc'=>"`t`.`amount` desc",'label'=>Expense::model()->getAttributeLabel('amount')),
            'billToCompany'=>array('asc'=>"`t`.`billToCompany`",'desc'=>"`t`.`billToCompany` desc",'label'=>Expense::model()->getAttributeLabel('Bill')),
            'expenseDate'=>array('asc'=>"`t`.`expenseDate`",'desc'=>"`t`.`expenseDate` desc",'label'=>Expense::model()->getAttributeLabel('Date')),
            'company'=>array('asc'=>"`Expense_Company`.`title`",'desc'=>"`Expense_Company`.`title` desc",'label'=>Expense::model()->getAttributeLabel('companyId')),
            'project'=>array('asc'=>"`Expense_Project`.`title`",'desc'=>"`Expense_Project`.`title` desc",'label'=>Expense::model()->getAttributeLabel('projectId')),
        );
        $sort->defaultOrder="`t`.`expenseDate` DESC, `t`.`id` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('company'=>array('select'=>'title'),'project'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['company.allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Company_User2Company')!==false;
        if($together)
            $models=Expense::model()->with($with)->together()->findAll($criteria);
        else
            $models=Expense::model()->with($with)->findAll($criteria);

        // filters data
        $filters=array('company'=>$company,'project'=>$project,'billToCompany'=>$billToCompany);
        $allCompany=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('company'=>'all'))),
            'active'=>$company==='all'
        ));
        $companyLinkText=$company==='all' ? Yii::t('t','All companies') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.`id`, `t`.`title`, COUNT(`expense`.`id`) as countExpense";
        if(User::isClient())
        {
            $criteria->join="INNER JOIN `".Expense::model()->tableName()."` `expense` ON `expense`.`companyId`=`t`.`id`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`t`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Expense::model()->tableName()."` `expense` ON `expense`.`companyId`=`t`.`id`";
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
            $allCompany[$i]['text'].=' ('.$model->countExpense.')';
            if($company===(string)$model->id)
                $companyLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }
        $allProject=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('project'=>'all'))),
            'active'=>$project==='all'
        ));
        $projectLinkText=$project==='all' ? Yii::t('t','All projects') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.id, `t`.title, COUNT(`expense`.`id`) as countExpense";
        //$criteria->select="`t`.`id`, `t`.`title`, COUNT(`expense`.`id`) as countExpense"; // uncomment in yii-1.1.2
        if(User::isClient())
        {
            $criteria->join="INNER JOIN `".Expense::model()->tableName()."` `expense` ON `expense`.`projectId`=`t`.`id`".
                " INNER JOIN `".Company::model()->tableName()."` `company` ON `company`.`id`=`expense`.`companyId`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`company`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Expense::model()->tableName()."` `expense` ON `expense`.`projectId`=`t`.`id`";
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
            $allProject[$i]['text'].=' ('.$model->countExpense.')';
            if($project===(string)$model->id)
                $projectLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }
        $allBillToCompany=array(
            array(
                'text'=>Yii::t('t','Bill and not'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('billToCompany'=>'all'))),
                'active'=>$billToCompany==='all'
            ),
            array(
                'text'=>Yii::t('t','Yes'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('billToCompany'=>Expense::BILL_TO_COMPANY))),
                'active'=>$billToCompany===(string)Expense::BILL_TO_COMPANY
            ),
            array(
                'text'=>Yii::t('t','No'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('billToCompany'=>Expense::DO_NOT_BILL_TO_COMPANY))),
                'active'=>$billToCompany===(string)Expense::DO_NOT_BILL_TO_COMPANY
            ),
        );
        switch($billToCompany)
        {
            case 'all':
                $billToCompanyLinkText=Yii::t('t','Bill and not');
                break;
            case (string)Expense::BILL_TO_COMPANY:
                $billToCompanyLinkText=Yii::t('t','Yes');
                break;
            case (string)Expense::DO_NOT_BILL_TO_COMPANY:
                $billToCompanyLinkText=Yii::t('t','No');
                break;
            default:
                $billToCompanyLinkText='&nbsp;';
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
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->expenseDate,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->expenseDate,'full',null)),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode($model->amount),
                ),
                array(
                    'content'=>CHtml::encode($model->getAttributeView('billToCompany','grid')),
                ),
                array(
                    'content'=>
                        ((User::isManager() && empty($model->invoiceId)) || User::isAdministrator()) ?
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
            'project'=>$project,
            'billToCompany'=>$billToCompany,
            'filters'=>$filters,
            'allCompany'=>$allCompany,
            'companyLinkText'=>$companyLinkText,
            'allProject'=>$allProject,
            'projectLinkText'=>$projectLinkText,
            'allBillToCompany'=>$allBillToCompany,
            'billToCompanyLinkText'=>$billToCompanyLinkText,
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
        $project=isset($_GET['project']) ? $_GET['project'] : null;
        if($project!=='all' && !ctype_digit($project))
            $project='all';
        $billToCompany=isset($_GET['billToCompany']) ? $_GET['billToCompany'] : null;
        if($billToCompany!=='all' && $billToCompany!==(string)Expense::BILL_TO_COMPANY && $billToCompany!==(string)Expense::DO_NOT_BILL_TO_COMPANY)
            $billToCompany='all';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.amount, `t`.billToCompany, `t`.expenseDate";
        //$criteria->select="`t`.`amount`, `t`.`billToCompany`, `t`.`expenseDate`"; // uncomment in yii-1.1.2
        if($jqGrid['searchField']!==null && $jqGrid['searchString']!==null && $jqGrid['searchOper']!==null)
        {
            $field=array(
                'amount'=>"`t`.`amount`",
                'billToCompany'=>"`t`.`billToCompany`",
                'expenseDate'=>"`t`.`expenseDate`",
                'company'=>"`Expense_Company`.`title`",
                'project'=>"`Expense_Project`.`title`",
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
            $criteria->addCondition("`Expense_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if($project!=='all')
        {
            $criteria->addCondition("`Expense_Project`.`id`=:projectId");
            $criteria->params[':projectId']=$project;
        }
        if($billToCompany===(string)Expense::BILL_TO_COMPANY)
        {
            $criteria->addCondition("`t`.`billToCompany`=:billToCompany");
            $criteria->params[':billToCompany']=Expense::BILL_TO_COMPANY;
        }
        else if($billToCompany===(string)Expense::DO_NOT_BILL_TO_COMPANY)
        {
            $criteria->addCondition("`t`.`billToCompany`=:doNotBillToCompany");
            $criteria->params[':doNotBillToCompany']=Expense::DO_NOT_BILL_TO_COMPANY;
        }
        if(User::isClient())
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Expense_Company')!==false)
            $with[]='company';
        if(strpos($criteria->condition,'Expense_Project')!==false)
            $with[]='project';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='company.allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Expense::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Expense::model()->count($criteria));
        $pages->pageSize=$jqGrid['pageSize']!==null ? $jqGrid['pageSize'] : self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        //sort
        $sort=new CSort('Expense');
        $sort->attributes=array(
            'amount'=>array('asc'=>"`t`.`amount`",'desc'=>"`t`.`amount` desc",'label'=>Expense::model()->getAttributeLabel('amount')),
            'billToCompany'=>array('asc'=>"`t`.`billToCompany`",'desc'=>"`t`.`billToCompany` desc",'label'=>Expense::model()->getAttributeLabel('Bill')),
            'expenseDate'=>array('asc'=>"`t`.`expenseDate`",'desc'=>"`t`.`expenseDate` desc",'label'=>Expense::model()->getAttributeLabel('Date')),
            'company'=>array('asc'=>"`Expense_Company`.`title`",'desc'=>"`Expense_Company`.`title` desc",'label'=>Expense::model()->getAttributeLabel('companyId')),
            'project'=>array('asc'=>"`Expense_Project`.`title`",'desc'=>"`Expense_Project`.`title` desc",'label'=>Expense::model()->getAttributeLabel('projectId')),
        );
        $sort->defaultOrder="`t`.`expenseDate` DESC, `t`.`id` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('company'=>array('select'=>'title'),'project'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['company.allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Company_User2Company')!==false;
        if($together)
            $models=Expense::model()->with($with)->together()->findAll($criteria);
        else
            $models=Expense::model()->with($with)->findAll($criteria);

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
                CHtml::encode(MDate::format($model->expenseDate,'medium',null)),
                CHtml::encode($model->amount),
                CHtml::encode($model->getAttributeView('billToCompany','grid')),
                ((User::isManager() && empty($model->invoiceId)) || User::isAdministrator()) ?
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
}
