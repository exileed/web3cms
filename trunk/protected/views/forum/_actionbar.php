<?php
$arr = array(
    'links'=>array(
        array(
            'text'=>'New Topic',
            'url'=>array('forum/newtopic','sid'=>$sid),
            'options'=>array('title'=>Yii::t('link','New Topic'))
        )));
if ($this->action->id != 'section') {
    array_push($arr['links'], array(
            'text'=>'Reply',
            'url'=>array('forum/reply','tid'=>$tid,'sid'=>$sid),
            'options'=>array('title'=>Yii::t('link','Reply'))
        ));
}
$this->widget('application.components.WPreItemActionBar',$arr); ?>