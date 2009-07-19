<?php

/**
 * _CController
 * Add and redefine controller methods to Yii core class CController
 */
class _CController extends CController
{
    /**
    * Call cms initialization
    * 
    */
    public function init()
    {
        __init::fromController();
        parent::init();
    }
}