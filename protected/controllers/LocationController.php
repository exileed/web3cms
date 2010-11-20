<?php

class LocationController extends _CController
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
            array('allow', // allow all users to perform 'grid', 'gridData', 'list' and 'show' actions
                'actions'=>array('grid','gridData','list','show'),
                'users'=>array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('create','update'),
                'users'=>array('@'),
            ),
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
        $this->render($this->action->id,array('model'=>$this->loadModel()));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'show' page.
     */
    public function actionCreate()
    {
        $model=new Location($this->action->id);
        if(isset($_POST['Location']))
        {
            // collect user input data
            $model->attributes=$_POST['Location'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The new "{title}" location record has been successfully created.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
                ));
                // go to the 'show' page
                $this->redirect(array('show','id'=>$model->id));
            }
        }
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'show' page.
     */
    public function actionUpdate()
    {
        $model=$this->loadModel();
        // explicitly set model scenario to be current action
        //$model->setScenario($this->action->id);
        // whether data is passed
        if(isset($_POST['Location']))
        {
            // collect user input data
            $model->attributes=$_POST['Location'];
            // validate with the current action as scenario and save without validation
            if(($validated=$model->validate())!==false && ($saved=$model->save(false))!==false)
            {
                // set success message
                MUserFlash::setTopSuccess(Yii::t('hint',
                    'The "{title}" location record has been updated.',
                    array('{title}'=>MHtml::wrapInTag($model->title,'strong'))
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

        $pages=new CPagination(Location::model()->count($criteria));
        $pages->pageSize=self::LIST_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $models=Location::model()->findAll($criteria);

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
        $criteria=new CDbCriteria;

        $pages=new CPagination(Location::model()->count($criteria));
        $pages->pageSize=self::GRID_PAGE_SIZE;
        $pages->applyLimit($criteria);

        $sort=new CSort('Location');
        $sort->applyOrder($criteria);

        $models=Location::model()->findAll($criteria);

        $this->render($this->action->id,array(
            'models'=>$models,
            'pages'=>$pages,
            'sort'=>$sort,
        ));
    }
}
