<?php

class forumController extends _CController {

    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow',  // allow all users to perform 'index' and 'view' actions
                'actions'=>array('index','section','topic'),
                'users'=>array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions'=>array('NewTopic','Reply','ajax'),
                'users'=>array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions'=>array('NewSection'),
                'users'=>array('admin'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Lists all sections.
     */
    public function actionIndex() {
        $criteria = new CDbCriteria(array('order'=>'position'));
        $models = forumSections::model()->findAll($criteria);
        if (empty($models))
            MUserFlash::setTopInfo('There are no forum sections defined.');
        $this->render($this->action->id, array(
                'models'=>$models));
    }

    /**
     * Lists all topics.
     */
    public function actionSection() {
        $sectionId = Yii::app()->request->getQuery('sid',false);
        if ($sectionId == false)
            $this->redirect($this->createAbsoluteUrl('forum/index'));
        $criteria = new CDbCriteria(array('with'=>array('section'=>array('select'=>'parentId,name,description'),'user'=>array('select'=>'username'),'post'),'condition'=>'`t`.`sectionId`='.$sectionId));
        $models = forumTopics::model()->findAll($criteria);
        if (empty($models)) {
            MUserFlash::setTopInfo('There are no topics in this section.');
            $this->render($this->action->id, array('section'=>forumSections::model()->findByPk($sectionId),'sid'=>$sectionId));
        } else {
        $this->render($this->action->id, array(
                'models'=>$models,'sid'=>$sectionId)
        );}
    }

    /**
     * Viewing a topic
     */
    public function actionTopic() {
        $topicId = Yii::app()->request->getQuery('id',false);
        if ($topicId == false)
            $this->redirect($this->createAbsoluteUrl('forum/index'));
        $criteria = new CDbCriteria(array('with'=>array('topic','user'=>array('select'=>'username,createTime'),'section'=>array('select'=>'name')),'condition'=>'`t`.`topicId`='.$topicId,'order'=>'postTime'));
        $this->render($this->action->id, array(
                'models'=>forumPosts::model()->findAll($criteria),'tid'=>$topicId)
        );
    }

    /**
     * Adding a new section
     */
    public function actionNewSection() {
        $model = new forumSections;
        if (Yii::app()->request->isPostRequest) {
            $model->attributes = $_POST['forumSections'];
            $model->position = $model->find(new CDbCriteria(array('select'=>'MAX(position) AS position')))->position + 1;
            if ($model->save())
                $this->redirect(array('forum/index', 'id'=>$model->id));
        }
        $this->render($this->action->id,array('model'=>$model));
    }

    /**
     * Postage of a new topic
     */
    public function actionNewTopic() {
        $sectionId = Yii::app()->request->getQuery('sid',false);
        if ($sectionId == false)
            $this->redirect($this->createAbsoluteUrl('forum/index'));
        list($forumPosts,$forumTopics) = array(new forumPosts,new forumTopics);
        if (Yii::app()->request->isPostRequest) {
            $forumTopics->sectionId = $sectionId;
            $forumTopics->postedBy = Yii::app()->user->id;
            $forumTopics->created = time();
            $forumPosts->sectionId = $sectionId;
            $forumPosts->attributes = $_POST['forumPosts'];
            $forumPosts->postedBy = Yii::app()->user->id;
            $forumPosts->postTime = time();
            if ($forumPosts->validate() && $forumTopics->validate()) {
                $forumTopics->save(false);
                $forumPosts->topicId = $forumTopics->id;
                $forumPosts->save(false);
                $this->redirect(array('forum/topic', 'id'=>$forumTopics->id));
            }
        }
        $this->render($this->action->id,array('forumPosts'=>$forumPosts,'forumTopics'=>$forumTopics));
    }

    /**
     * Postage of a reply
     */
    public function actionReply() {
        $topicId = Yii::app()->request->getQuery('tid',false);
        $sectionId = Yii::app()->request->getQuery('sid',false);
        if ($topicId == false || $sectionId == false)
            $this->redirect($this->createAbsoluteUrl('forum/index'));
        $forumPosts = new forumPosts;
        if (Yii::app()->request->isPostRequest) {
            $forumPosts->sectionId = $sectionId;
            $forumPosts->attributes = $_POST['forumPosts'];
            $forumPosts->postedBy = Yii::app()->user->id;
            $forumPosts->postTime = time();
            $forumPosts->topicId = $topicId;
            if ($forumPosts->save()) {
                forumTopics::model()->updateCounters(array('replyCount'=>1),array('condition'=>'`id`='.$topicId));
                $this->redirect(array('forum/topic', 'id'=>$topicId));
            }
        }
        $this->render($this->action->id,array('forumPosts'=>$forumPosts));
    }

    /**
     * Edition of a section
     */
    public function actionEditSection() {
        $model = $this->loadModel();
        if (isset($_POST['forumSections'])) {
            $model->attributes = $_POST['forumSections'];
            if ($model->save())
                $this->redirect(array('view', 'id'=>$model->id));
        }

        $this->render('update', array(
                'model'=>$model,
        ));
    }

    public function actionAjax() {
        if (Yii::app()->request->getIsAjaxRequest() != false && Yii::app()->request->getParam('action') != false) {
            switch(Yii::app()->request->getParam('action')) {
                case 'renderBB':
                    echo MHtml::renderBB(Yii::app()->request->getParam('data'));
                    break;
            }
            Yii::app()->end();
        }
    }

}