<?php

/**
 * _CController
 */
class _CController extends CController
{
    protected $metaDescription;
    protected $metaKeywords;
    protected $pageLabel;

    /**
    * Call initialization
    * 
    */
    public function init()
    {
        _init::fromController();
        parent::init();
    }

    /**
     * Add meta description.
     * 
     * @param string $value
     * @param string $delimiter
     */
    public function addMetaDescription($value='',$delimiter=' ')
    {
        $this->setMetaDescription($this->getMetaDescription().$delimiter.$value);
    }

    /**
     * Add meta keywords.
     * 
     * @param string $value
     * @param string $delimiter
     */
    public function addMetaKeywords($value='',$delimiter=', ')
    {
        $this->setMetaKeywords($this->getMetaKeywords().$delimiter.$value);
    }

    /**
     * Get description, the content for html head's meta name="description".
     * 
     * @return string
     */
    public function getMetaDescription()
    {
        if(is_null($this->metaDescription))
            return Yii::app()->params['description'];
        else
            return $this->metaDescription;
    }

    /**
     * Get keywords, the content for html head's meta name="keywords".
     * 
     * @return string
     */
    public function getMetaKeywords()
    {
        if(is_null($this->metaKeywords))
            return Yii::app()->params['keywords'];
        else
            return $this->metaKeywords;
    }

    /**
     * Get page label (wrapped in "w3-page-label" css class).
     * 
     * @return string
     */
    public function getPageLabel()
    {
        return $this->pageLabel;
    }

    /**
     * Set meta description.
     * 
     * @param string $value
     */
    public function setMetaDescription($value='')
    {
        $this->metaDescription=$value;
    }

    /**
     * Set meta keywords.
     * 
     * @param string $value
     */
    public function setMetaKeywords($value='')
    {
        $this->metaKeywords=$value;
    }

    /**
    * Set page label.
    * 
    * @param string $value
    */
    public function setPageLabel($value='')
    {
        $this->setPageTitle((empty($value)?'':$value.' - ').Yii::app()->name);
        $this->pageLabel=$value;
    }
}