<?php
class ForumController extends _CController
{
    public function ActionIndex()
    {
	$this->render('index',array('page'=>'index'));
    }

    public function missingAction($missingActionId)
    {
    	if (file_exists(Yii::app()->getBasePath() . '/../_forum/'.$missingActionId.'.php')) {
            $this->render('index',array('page'=>$missingActionId));
    	}
    }
}