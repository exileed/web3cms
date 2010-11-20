<?php

class InvoiceController extends _CController
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
            MUserFlash::setTopError(Yii::t('hint','We are sorry, but you don\'t have enough rights to browse invoices.'));
            $this->redirect($this->getGotoUrl());
        }

        $with=array('company');
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
                    'We are sorry, but you don\'t have enough rights to view the invoice record number {id}.',
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
        $model=new Invoice($this->action->id);
        if(isset($_POST['Invoice']))
        {
            // collect user input data
            $model->attributes=$_POST['Invoice'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The new invoice record number "{invoiceNumber}" has been successfully created.',
                    array('{invoiceNumber}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        else
        {
            // pre-assigned attributes (default values for a new record)
            if(isset($_GET['companyId']))
                // company is known
                $model->companyId=$_GET['companyId'];
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
        if(isset($_POST['Invoice']))
        {
            // collect user input data
            $model->attributes=$_POST['Invoice'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The invoice record number "{invoiceNumber}" has been updated.',
                    array('{invoiceNumber}'=>MHtml::wrapInTag($model->id,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Lists all models.
     */
    public function actionList()
    {
        $criteria=new CDbCriteria;
        $criteria->order="`t`.`createTime` DESC";

        $pages=new CPagination(Invoice::model()->count($criteria));
        $pages->pageSize=self::LIST_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=Invoice::model()->with('company')->findAll($criteria);

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

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.amountTotal, `t`.createTime, `t`.id";
        //$criteria->select="`t`.`amountTotal`, `t`.`createTime`, `t`.`id`"; // uncomment in yii-1.1.2
        if($company!=='all')
        {
            $criteria->addCondition("`Invoice_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if(User::isClient())
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Invoice_Company')!==false)
            $with[]='company';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='company.allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Invoice::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Invoice::model()->count($criteria));
        $pages->pageSize=self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Invoice');
        $sort->attributes=array(
            'amountTotal'=>array('asc'=>"`t`.`amountTotal`",'desc'=>"`t`.`amountTotal` desc",'label'=>Invoice::model()->getAttributeLabel('Total')),
            'createTime'=>array('asc'=>"`t`.`createTime`",'desc'=>"`t`.`createTime` desc",'label'=>Invoice::model()->getAttributeLabel('Date')),
            'id'=>array('asc'=>"`t`.`id`",'desc'=>"`t`.`id` desc",'label'=>Invoice::model()->getAttributeLabel('id')),
            'company'=>array('asc'=>"`Invoice_Company`.`title`",'desc'=>"`Invoice_Company`.`title` desc",'label'=>Invoice::model()->getAttributeLabel('companyId')),
        );
        $sort->defaultOrder="`t`.`createTime` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('company'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['company.allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Company_User2Company')!==false;
        if($together)
            $models=Invoice::model()->with($with)->together()->findAll($criteria);
        else
            $models=Invoice::model()->with($with)->findAll($criteria);

        // filters data
        $filters=array('company'=>$company);
        $allCompany=array(array(
            'text'=>Yii::t('t','All'),
            'url'=>Yii::app()->createUrl($this->id.'/'.$this->action->id,array_merge($filters,array('company'=>'all'))),
            'active'=>$company==='all'
        ));
        $companyLinkText=$company==='all' ? Yii::t('t','All companies') : '&nbsp;';
        $criteria=new CDbCriteria;
        $criteria->select="`t`.`id`, `t`.`title`, COUNT(`invoice`.`id`) as countInvoice";
        if(User::isClient())
        {
            $criteria->join="INNER JOIN `".Invoice::model()->tableName()."` `invoice` ON `invoice`.`companyId`=`t`.`id`".
                " INNER JOIN `".User2Company::model()->tableName()."` `u2c` ON `u2c`.`companyId`=`t`.`id`";
            $criteria->condition="`u2c`.`userId`=:clientId AND `u2c`.`position`=:clientPosition";
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }
        else
            $criteria->join="INNER JOIN `".Invoice::model()->tableName()."` `invoice` ON `invoice`.`companyId`=`t`.`id`";
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
            $allCompany[$i]['text'].=' ('.$model->countInvoice.')';
            if($company===(string)$model->id)
                $companyLinkText=$model->title===''?Yii::t('t','[no title]'):$model->title;
        }

        // rows for the static grid
        $gridRows=array();
        foreach($models as $model)
        {
            $gridRows[]=array(
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode($model->id),
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode(MDate::format($model->createTime,'medium',null)),
                    'title'=>CHtml::encode(MDate::format($model->createTime,'full',null)),
                ),
                array(
                    'content'=>isset($model->company->id) ? CHtml::link(CHtml::encode($model->company->title),array('company/show','id'=>$model->company->id)) : '',
                ),
                array(
                    'align'=>'right',
                    'content'=>CHtml::encode($model->amountTotal),
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
            'filters'=>$filters,
            'allCompany'=>$allCompany,
            'companyLinkText'=>$companyLinkText,
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

        // criteria
        $criteria=new CDbCriteria;
        $criteria->group="`t`.`id`"; // required by together()
        $criteria->select="`t`.amountTotal, `t`.createTime, `t`.id";
        //$criteria->select="`t`.`amountTotal`, `t`.`createTime`, `t`.`id`"; // uncomment in yii-1.1.2
        if($jqGrid['searchField']!==null && $jqGrid['searchString']!==null && $jqGrid['searchOper']!==null)
        {
            $field=array(
                'amountTotal'=>"`t`.`amountTotal`",
                'createTime'=>"`t`.`createTime`",
                'id'=>"`t`.`id`",
                'company'=>"`Invoice_Company`.`title`",
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
            $criteria->addCondition("`Invoice_Company`.`id`=:companyId");
            $criteria->params[':companyId']=$company;
        }
        if(User::isClient())
        {
            $criteria->addCondition("`Company_User2Company`.`userId`=:clientId AND `Company_User2Company`.`position`=:clientPosition");
            $criteria->params[':clientId']=Yii::app()->user->id;
            $criteria->params[':clientPosition']=Company::OWNER;
        }

        // pagination
        $with=array();
        if(strpos($criteria->condition,'Invoice_Company')!==false)
            $with[]='company';
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with[]='company.allUser2Company';
        if(count($with)>=1)
            $pages=new CPagination(Invoice::model()->with($with)->count($criteria));
        else
            $pages=new CPagination(Invoice::model()->count($criteria));
        $pages->pageSize=$jqGrid['pageSize']!==null ? $jqGrid['pageSize'] : self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        // sort
        $sort=new CSort('Invoice');
        $sort->attributes=array(
            'amountTotal'=>array('asc'=>"`t`.`amountTotal`",'desc'=>"`t`.`amountTotal` desc",'label'=>Invoice::model()->getAttributeLabel('Total')),
            'createTime'=>array('asc'=>"`t`.`createTime`",'desc'=>"`t`.`createTime` desc",'label'=>Invoice::model()->getAttributeLabel('Date')),
            'id'=>array('asc'=>"`t`.`id`",'desc'=>"`t`.`id` desc",'label'=>Invoice::model()->getAttributeLabel('id')),
            'company'=>array('asc'=>"`Invoice_Company`.`title`",'desc'=>"`Invoice_Company`.`title` desc",'label'=>Invoice::model()->getAttributeLabel('companyId')),
        );
        $sort->defaultOrder="`t`.`createTime` DESC";
        $sort->applyOrder($criteria);

        // find all
        $with=array('company'=>array('select'=>'title'));
        if(strpos($criteria->condition,'Company_User2Company')!==false)
            $with['company.allUser2Company']=array('select'=>'id');
        $together=strpos($criteria->condition,'Company_User2Company')!==false;
        if($together)
            $models=Invoice::model()->with($with)->together()->findAll($criteria);
        else
            $models=Invoice::model()->with($with)->findAll($criteria);

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
                CHtml::encode($model->id),
                CHtml::encode(MDate::format($model->createTime,'medium',null)),
                isset($model->company->id) ? CHtml::link(CHtml::encode($model->company->title),array('company/show','id'=>$model->company->id)) : '',
                CHtml::encode($model->amountTotal),
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
