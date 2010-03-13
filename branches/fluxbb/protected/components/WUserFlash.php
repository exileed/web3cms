<?php
/**
 * WUserFlash class file.
 * WUserFlash is a widget displaying user flash (feedback messages).
 */
class WUserFlash extends CWidget
{
    /**
     * @var string type of this widget, it's position.
     */
    public $type;
    /**
     * @var string in which container is displaying,
     * 'sidebar1' or 'sidebar2'.
     */
    public $in;

    /**
     * When widget is called, following function is run.
     */
    public function run()
    {
        if($this->type=='contentSummary')
            $this->contentSummary();
        else if($this->type=='sidebarSummary')
            $this->sidebarSummary();
        else if($this->type=='sidebar1Summary')
            $this->sidebar1Summary();
        else if($this->type=='sidebar2Summary')
            $this->sidebar2Summary();
        else if($this->type=='topSummary')
            $this->topSummary();
    }

    /**
     * User flash messages displayed in the 'content' part of the page.
     */
    public function contentSummary()
    {
        if(MUserFlash::hasContentSuccess() || MUserFlash::hasContentInfo() || MUserFlash::hasContentError())
        {
            $data=array(
                'success'=>MUserFlash::getContentSuccess(),
                'info'=>MUserFlash::getContentInfo(),
                'error'=>MUserFlash::getContentError()
            );
            $this->render('wUserFlash/contentSummary',$data);
        }
    }

    /**
     * User flash messages displayed in the sidebar part of the page.
     */
    public function sidebarSummary()
    {
        if(MUserFlash::hasSidebarSuccess() || MUserFlash::hasSidebarInfo() || MUserFlash::hasSidebarError())
        {
            $data=array(
                'in'=>$this->in=='sidebar1'?'sidebar1':'sidebar2',
                'success'=>MUserFlash::getSidebarSuccess(),
                'info'=>MUserFlash::getSidebarInfo(),
                'error'=>MUserFlash::getSidebarError()
            );
            $this->render('wUserFlash/sidebarSummary',$data);
        }
    }

    /**
     * User flash messages displayed in the 'sidebar1' part of the page.
     */
    public function sidebar1Summary()
    {
        if(MUserFlash::hasSidebar1Success() || MUserFlash::hasSidebar1Info() || MUserFlash::hasSidebar1Error())
        {
            $data=array(
                'success'=>MUserFlash::getSidebar1Success(),
                'info'=>MUserFlash::getSidebar1Info(),
                'error'=>MUserFlash::getSidebar1Error()
            );
            $this->render('wUserFlash/sidebar1Summary',$data);
        }
    }

    /**
     * User flash messages displayed in the 'sidebar2' part of the page.
     */
    public function sidebar2Summary()
    {
        if(MUserFlash::hasSidebar2Success() || MUserFlash::hasSidebar2Info() || MUserFlash::hasSidebar2Error())
        {
            $data=array(
                'success'=>MUserFlash::getSidebar2Success(),
                'info'=>MUserFlash::getSidebar2Info(),
                'error'=>MUserFlash::getSidebar2Error()
            );
            $this->render('wUserFlash/sidebar2Summary',$data);
        }
    }

    /**
     * User flash messages displayed in the 'top' part of the page,
     * between header and the main part.
     */
    public function topSummary()
    {
        if(MUserFlash::hasTopSuccess() || MUserFlash::hasTopInfo() || MUserFlash::hasTopError())
        {
            $data=array(
                'success'=>MUserFlash::getTopSuccess(),
                'info'=>MUserFlash::getTopInfo(),
                'error'=>MUserFlash::getTopError()
            );
            $this->render('wUserFlash/topSummary',$data);
        }
    }
}