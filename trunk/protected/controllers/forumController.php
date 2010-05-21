<?php

class forumController extends _CController {

    /**
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model,$breadcrumbs;

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
                'actions'=>array('NewTopic','Reply'),
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
        $id = Yii::app()->request->getQuery('id',false);
        if ($id == false)
            $this->redirect($this->createAbsoluteUrl('forum/index'));
        $criteria = new CDbCriteria(array('with'=>array('section'=>array('select'=>'parentId,name,description'),'user'=>array('select'=>'username'),'post'),'condition'=>'`t`.`sectionId`='.$id));
        $models = forumTopics::model()->findAll($criteria);
        if (empty($models)) {
            MUserFlash::setTopInfo('There are no topics in this section.');
            $this->render($this->action->id, array('section'=>forumSections::model()->findByPk($id),'id'=>$id));
        } else {
        $this->render($this->action->id, array(
                'models'=>$models,'id'=>$id)
        );}
    }

    /**
     * Viewing a topic
     */
    public function actionTopic() {
        $id = Yii::app()->request->getQuery('id',false);
        if ($id == false)
            $this->redirect($this->createAbsoluteUrl('forum/index'));
        $criteria = new CDbCriteria(array('with'=>array('topic','user'=>array('select'=>'username,createTime'),'section'=>array('select'=>'name')),'condition'=>'`t`.`topicId`='.$id));
        $this->render($this->action->id, array(
                'models'=>forumPosts::model()->findAll($criteria),'id'=>$id)
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
        $id = Yii::app()->request->getQuery('id',false);
        if ($id == false)
            $this->redirect($this->createAbsoluteUrl('forum/index'));
        $forumPosts = new forumPosts;
        $forumTopics = new forumTopics;
        if (Yii::app()->request->isPostRequest) {
            $sid = Yii::app()->request->getParam('id',NULL);
            $forumTopics->sectionId = $sid;
            $forumTopics->created = time();
            $forumPosts->sectionId = $sid;
            $forumPosts->attributes = $_POST['forumPosts'];
            $forumPosts->postedBy = Yii::app()->user->id;
            $forumTopics->postedBy = Yii::app()->user->id;
            $forumPosts->postTime = time();
            if ($forumPosts->validate() && $forumTopics->validate()) {
                $forumTopics->save(false);
                $forumPosts->topicId = $forumTopics->id;
                $forumPosts->save(false);
                $this->redirect(array('forum/topic', 'id'=>Yii::app()->db->getLastInsertID()));
            }
        }
        $this->render($this->action->id,array('forumPosts'=>$forumPosts,'forumTopics'=>$forumTopics));
    }

    /**
     * Postage of a reply
     */
    public function actionReply() {
        // if (!Yii::app()->request->isPostRequest || !Yii::app()->request->getPost('topic',false)) $this->redirect($this->createAbsoluteUrl('forum/index'));
        // TODO
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
}