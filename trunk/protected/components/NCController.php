<?php

/**
 * NCController
 */
class NCController extends CController
{
    public $_frameTitle;

    /**
    * Call initialization
    * 
    */
    public function init()
    {
        _init::all();
        parent::init();
    }

    /**
     * Frame title
     */
    public function getFrameTitle()
    {
        return $this->_frameTitle;
    }
    public function setFrameTitle($value='')
    {
        $this->setPageTitle((empty($value)?'':$value.' - ').Yii::app()->params['title']);
        $this->_frameTitle=$value;
    }
}