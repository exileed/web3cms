<?php
class ForumController extends _CController
{
    public function ActionIndex()
    {
	$this->render('index',array('page'=>'index'));
    }

    public function missingAction($missingActionId)
    {
        $this->render('index',array('page'=>$missingActionId));
    }
}