<?php

class CompanyController extends _CController
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
        if(!User::isClient() && !User::isManager() && !User::isAdministrator())
        {
            // not enough rights
            MUserFlash::setTopError(Yii::t('hint','We are sorry, but you don\'t have enough rights to browse companies.'));
            $this->redirect($this->getGotoUrl());
        }

        if(isset($_GET['my']))
        {
            // show client's company
            if(Yii::app()->user->isGuest)
            {
                // guest may not have any company
                MUserFlash::setTopError(Yii::t('hint','Please authorize to view your company.'));
                $this->redirect($this->getGotoUrl());
            }
            if(($user2Company=User2Company::model()->findByAttributes(array('userId'=>Yii::app()->user->id,'position'=>Company::OWNER),array('order'=>"`companyPriority` ASC")))!==null)
                $id=$user2Company->companyId;
            else
            {
                // user is not an owner yet
                MUserFlash::setTopError(Yii::t('hint','We are sorry, but you are not the owner of any company yet.'));
                $this->redirect($this->getGotoUrl());
            }
        }
        else
            // get id from the url
            $id=isset($_GET['id']) ? $_GET['id'] : 0;
        // load model
        $with=array('allUser');
        /*if(User::isClient())
            $with[]='allUser2Company';*/
        $model=$this->loadModel(array('id'=>$id,'with'=>$with));
        // may member view this record?
        if(User::isClient())
        {
            /*$allOwner=array();
            foreach($model->allUser2Company as $user2Company)
            {
                if($user2Company->position===Company::OWNER)
                    $allOwner[]=$user2Company->userId;
            }
            if(!in_array(Yii::app()->user->id,$allOwner))*/
            if(!$model->isOwner())
            {
                MUserFlash::setTopError(Yii::t('hint',
                    'We are sorry, but you don\'t have enough rights to view the company record number {id}.',
                    array('{id}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                $this->redirect($this->getGotoUrl());
            }
        }
        // FIXME: check is owner or manager or admin
        // transaction's payments
        $companyPayments=CompanyPayment::model()->findAllByAttributes(array('companyId'=>$model->id),
            new CDbCriteria(array(
                'order'=>"`t`.`paymentDate` ASC, `t`.`id` ASC"
            ))
        );
        // transaction's invoices
        $invoices=Invoice::model()->findAllByAttributes(array('companyId'=>$model->id),
            new CDbCriteria(array(
                'order'=>"`t`.`invoiceDate` ASC, `t`.`id` ASC"
            ))
        );
        // construct tmp array of all transactions. use time as index
        $array=array();
        foreach($companyPayments as $companyPayment)
        {
            $time=strtotime($companyPayment->paymentDate);
            while(array_key_exists($time,$array))
                $time++;
            $array[$time]=array(
                'date'=>$companyPayment->paymentDate,
                'credit'=>$companyPayment->amount,
                'number'=>$companyPayment->paymentNumber,
                'method'=>$companyPayment->getAttributeView('paymentMethod'),
                'id'=>$companyPayment->id,
                'controllerId'=>'companyPayment',
            );
        }
        foreach($invoices as $invoice)
        {
            $time=strtotime($invoice->invoiceDate);
            while(array_key_exists($time,$array))
                $time++;
            $array[$time]=array(
                'date'=>$invoice->invoiceDate,
                'debit'=>$invoice->amountTotal,
                'id'=>$invoice->id,
                'controllerId'=>'invoice',
            );
        }
        // sort by index
        ksort($array);
        // construct transaction history array
        $transactions=array();
        $balance=$debit=$credit=0;
        foreach($array as $row)
        {
            $d=isset($row['debit']) ? $row['debit'] : 0;
            $c=isset($row['credit']) ? $row['credit'] : 0;
            $balance=$balance-$d+$c;
            $debit+=$d;
            $credit+=$c;
            $transactions[]=array(
                'date'=>$row['date'],
                'debit'=>isset($row['debit']) ? $row['debit'] : null,
                'credit'=>isset($row['credit']) ? $row['credit'] : null,
                'number'=>isset($row['number']) ? $row['number'] : null,
                'method'=>isset($row['method']) ? $row['method'] : null,
                'balance'=>$balance,
                'id'=>$row['id'],
                'controllerId'=>$row['controllerId'],
            );
        }

        // rows for the static grid
        $gridRows=array();
        foreach($transactions as $transaction)
        {
            $gridRows[]=array(
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($transaction['date'],'long',null)),
                    'title'=>CHtml::encode(MDate::format($transaction['date'],'full',null)),
                ),
                array(
                    'content'=>CHtml::link(
                        $transaction['controllerId']==='invoice' ?
                            Yii::t('link','Invoice {number}',array('{number}'=>$transaction['number']===null ? $transaction['id'] : $transaction['number'])) :
                            Yii::t('link','Payment {number} ({method})',array('{number}'=>($transaction['number']===null ? $transaction['id'] : $transaction['number']),'{method}'=>$transaction['method']))
                        ,
                        array($transaction['controllerId'].'/show','id'=>$transaction['id'])
                    ),
                ),
                array(
                    'align'=>'right',
                    'content'=>is_numeric($transaction['debit']) ? CHtml::encode(MCurrency::format($transaction['debit'])) : '',
                ),
                array(
                    'align'=>'right',
                    'content'=>is_numeric($transaction['credit']) ? CHtml::encode(MCurrency::format($transaction['credit'])) : '',
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MCurrency::format($transaction['balance'])),
                ),
            );
        }

        // render the view file
        $this->render($this->action->id,array(
            'model'=>$model,
            //'transactions'=>$transactions,
            'debit'=>$debit,
            'credit'=>$credit,
            'balance'=>($credit-$debit),
            'gridRows'=>$gridRows,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'show' page.
     */
    public function actionCreate()
    {
        $model=new Company($this->action->id);
        if(isset($_POST['Company']))
        {
            // collect user input data
            $model->attributes=$_POST['Company'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                if(isset($_POST['User2Company']))
                {
                    // assigned users
                    $model->allUser2Company=array(0=>new User2Company('create'));//createCompany
                    $model->allUser2Company[0]->companyId=$model->id;
                    foreach($model->allUser2Company as $user2Company)
                    {
                        $user2Company->attributes=$_POST['User2Company'];
                        $user2Company->position='owner';
                        $user2Company->save();
                    }
                }
                if(isset($_POST['Location']))
                {
                    // assigned locations
                    $model->allLocation=array(0=>new Location('reate'));
                    foreach($model->allLocation as $location)
                    {
                        // collect user input data
                        $location->attributes=$_POST['Location'];
                        // validate and create/update record
                        if($location->save())
                        {
                            $location2Record=new Location2Record('create');
                            $location2Record->locationId=$location->id;
                            $location2Record->record=get_class($model);
                            $location2Record->recordId=$model->id;
                            $location2Record->save();
                        }
                    }
                }
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The new "{title}" company record has been successfully created.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        else
        {
            // pre-assigned attributes (default values for a new record)
            $model->isActive=Company::IS_ACTIVE;
        }
        if(!isset($model->allUser2Company[0]))
            // new associated user
            $model->allUser2Company=array(0=>new User2Company('create'));
            $model->allUser2Company[0]->companyId=$model->id;
        if(!isset($model->allLocation[0]))
            // new associated location
            $model->allLocation=array(0=>new Location('create'));
        // display the create form
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate()
    {
        $model=$this->loadModel(array('with'=>array('allUser2Company','allLocation')));
        if($model===null)
        {
            // model not found
            MUserFlash::setTopError(Yii::t('modelNotFound',$this->id));
            $this->redirect($this->getGotoUrl());
        }
        // explicitly set model scenario to be current action
        //$model->setScenario($this->action->id);
        // whether data is passed
        if(isset($_POST['Company']))
        {
            // collect user input data
            $model->attributes=$_POST['Company'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                if(isset($_POST['User2Company']))
                {
                    // assigned users
                    $hasUser=isset($model->allUser2Company[0]->id);
                    if(!$hasUser)
                    {
                        $model->allUser2Company=array(0=>new User2Company('create'));
                        $model->allUser2Company[0]->companyId=$model->id;
                    }
                    foreach($model->allUser2Company as $user2Company)
                    {
                        // collect user input data
                        $user2Company->attributes=$_POST['User2Company'];
                        $user2Company->position='owner';
                        // at the crossroads: which action is expected to be performed
                        if($hasUser && empty($user2Company->userId))
                            // un-assigned associated record
                            $user2Company->delete();
                        else
                            // validate and create/update record
                            $user2Company->save();
                    }
                }
                if(isset($_POST['Location']))
                {
                    // assigned locations
                    if(!isset($model->allLocation[0]->id))
                        $model->allLocation=array(0=>new Location('create'));
                    foreach($model->allLocation as $location)
                    {
                        // collect user input data
                        $location->attributes=$_POST['Location'];
                        // validate and create/update record
                        if($location->save())
                        {
                            $location2Record=new Location2Record('create');
                            $location2Record->locationId=$location->id;
                            $location2Record->record=get_class($model);
                            $location2Record->recordId=$model->id;
                            $location2Record->save();
                        }
                    }
                }
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The "{title}" company record has been updated.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        if(!isset($model->allUser2Company[0]))
        {
            // new associated user
            $model->allUser2Company=array(0=>new User2Company('create'));
            $model->allUser2Company[0]->companyId=$model->id;
        }
        if(!isset($model->allLocation[0]))
            // new associated location
            $model->allLocation=array(0=>new Location('create'));
        // display the update form
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Lists all models.
     */
    public function actionList()
    {
        $criteria=new CDbCriteria;
        $criteria->order="`t`.`deactivationTime` ASC, `t`.`title` ASC";

        $pages=new CPagination(Company::model()->count($criteria));
        $pages->pageSize=self::LIST_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=Company::model()/*->with('allUser')*/->findAll($criteria);

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
        $state=isset($_GET['state']) ? $_GET['state'] : null;
        if($state!=='all' && $state!=='closed' && $state!=='open')
            $state='all';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.contactName, `t`.createTime, `t`.deactivationTime, `t`.title, `t`.titleAbbr";
        //$criteria->select="`t`.`contactName`, `t`.`createTime`, `t`.`deactivationTime`, `t`.`title`, `t`.`titleAbbr`"; // uncomment in yii-1.1.2
        if($state==='closed')
        {
            $criteria->addCondition("(`t`.`isActive` IS NULL OR `t`.`isActive`!=:isActive)");
            $criteria->params[':isActive']=Company::IS_ACTIVE;
        }
        else if($state==='open')
        {
            $criteria->addCondition("`t`.`isActive`=:isActive");
            $criteria->params[':isActive']=Company::IS_ACTIVE;
        }
        if(User::isClient())
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['allUser2Company']=array('select'=>'id');
        if(count($with)>=1)
            $pages=new CPagination(Company::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Company::model()->count($criteria));
        $pages->pageSize=self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Company');
        $sort->attributes=array(
            'contactName'=>array('asc'=>"`t`.`contactName`",'desc'=>"`t`.`contactName` desc",'label'=>Company::model()->getAttributeLabel('contactName')),
            'createTime'=>array('asc'=>"`t`.`createTime`",'desc'=>"`t`.`createTime` desc",'label'=>Company::model()->getAttributeLabel('Opened')),
            'deactivationTime'=>array('asc'=>"`t`.`deactivationTime`",'desc'=>"`t`.`deactivationTime` desc",'label'=>Company::model()->getAttributeLabel('Closed')),
            'title'=>array('asc'=>"`t`.`title`",'desc'=>"`t`.`title` desc",'label'=>Company::model()->getAttributeLabel('title')),
            'titleAbbr'=>array('asc'=>"`t`.`titleAbbr`",'desc'=>"`t`.`titleAbbr` desc",'label'=>Company::model()->getAttributeLabel('Abbr')),
        );
        $sort->defaultOrder="`t`.`deactivationTime` ASC, `t`.`title` ASC";
        $sort->applyOrder($criteria);

        // find all
        $with=array(/*'allUser'*/);
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='allUser2Company';
        $together=strpos($criteria->condition,'Company_User2Company')!==false;
        if($together)
            $models=Company::model()->with($with)->together()->findAll($criteria);
        else
            $models=Company::model()->with($with)->findAll($criteria);

        // filters data
        $filters=array('state'=>$state);
        $allState=array(
            array(
                'text'=>Yii::t('t','All'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'all'))),
                'active'=>$state==='all'
            ),
            array(
                'text'=>Yii::t('t','Open[companies]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'open'))),
                'active'=>$state==='open'
            ),
            array(
                'text'=>Yii::t('t','Closed[companies]'),
                'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('state'=>'closed'))),
                'active'=>$state==='closed'
            ),
        );
        switch($state)
        {
            case 'all':
                $stateLinkText=Yii::t('t','All states[company]');
                break;
            case 'closed':
                $stateLinkText=Yii::t('t','Closed[companies]');
                break;
            case 'open':
                $stateLinkText=Yii::t('t','Open[companies]');
                break;
            case 'overdue':
                $stateLinkText=Yii::t('t','Overdue[companies]');
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
                    'content'=>CHtml::encode($model->title),
                ),
                array(
                    'content'=>CHtml::encode($model->titleAbbr),
                ),
                array(
                    'content'=>CHtml::encode($model->contactName),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->createTime,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->createTime,'full')),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->deactivationTime,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->deactivationTime,'full')),
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
            'state'=>$state,
            'filters'=>$filters,
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
        $state=isset($_GET['state']) ? $_GET['state'] : null;
        if($state!=='all' && $state!=='closed' && $state!=='open')
            $state='all';

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.contactName, `t`.createTime, `t`.deactivationTime, `t`.title, `t`.titleAbbr";
        //$criteria->select="`t`.`contactName`, `t`.`createTime`, `t`.`deactivationTime`, `t`.`title`, `t`.`titleAbbr`"; // uncomment in yii-1.1.2
        if($jqGrid['searchField']!==null && $jqGrid['searchString']!==null && $jqGrid['searchOper']!==null)
        {
            $field=array(
                'contactName'=>"`t`.`contactName`",
                'createTime'=>"`t`.`createTime`",
                'deactivationTime'=>"`t`.`deactivationTime`",
                'title'=>"`t`.`title`",
                'titleAbbr'=>"`t`.`titleAbbr`",
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
        if($state==='closed')
        {
            $criteria->addCondition("(`t`.`isActive` IS NULL OR `t`.`isActive`!=:isActive)");
            $criteria->params[':isActive']=Company::IS_ACTIVE;
        }
        else if($state==='open')
        {
            $criteria->addCondition("`t`.`isActive`=:isActive");
            $criteria->params[':isActive']=Company::IS_ACTIVE;
        }
        if(User::isClient())
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Company::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Company::model()->count($criteria));
        $pages->pageSize=$jqGrid['pageSize']!==null ? $jqGrid['pageSize'] : self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Company');
        $sort->attributes=array(
            'contactName'=>array('asc'=>"`t`.`contactName`",'desc'=>"`t`.`contactName` desc",'label'=>Company::model()->getAttributeLabel('contactName')),
            'createTime'=>array('asc'=>"`t`.`createTime`",'desc'=>"`t`.`createTime` desc",'label'=>Company::model()->getAttributeLabel('Opened')),
            'deactivationTime'=>array('asc'=>"`t`.`deactivationTime`",'desc'=>"`t`.`deactivationTime` desc",'label'=>Company::model()->getAttributeLabel('Closed')),
            'title'=>array('asc'=>"`t`.`title`",'desc'=>"`t`.`title` desc",'label'=>Company::model()->getAttributeLabel('title')),
            'titleAbbr'=>array('asc'=>"`t`.`titleAbbr`",'desc'=>"`t`.`titleAbbr` desc",'label'=>Company::model()->getAttributeLabel('Abbr')),
        );
        $sort->defaultOrder="`t`.`deactivationTime` ASC, `t`.`title` ASC";
        $sort->applyOrder($criteria);

        // find all
        $with=array(/*'allUser'*/);
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Company_User2Company')!==false;
        if($together)
            $models=Company::model()->with($with)->together()->findAll($criteria);
        else
            $models=Company::model()->with($with)->findAll($criteria);

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
                CHtml::encode($model->title),
                CHtml::encode($model->titleAbbr),
                CHtml::encode($model->contactName),
                CHtml::encode(MDate::format($model->createTime,'medium',null)),
                CHtml::encode(MDate::format($model->deactivationTime,'medium',null)),
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
