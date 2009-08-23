<?php
/**
 * Widget User Flash
 */
class WUserFlash extends CWidget
{
    public $type;
    public $in;

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