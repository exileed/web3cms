<?php
/**
 * _CController class file.
 * Add and redefine controller methods of Yii core class CController.
 * Main initialization - it's run before everything else.
 */
class _CController extends CController
{
    /**
     * @var object instance of {@link MVariable}
     * MVariable is a storage of variables to share across all classes.
     * access it using Yii::app()->controller->var (or $this->var in the views).
     */
    public $var;

    /**
     * @var int default page sizes
     */
    const LIST_PAGE_SIZE=10;
    const GRID_PAGE_SIZE=50;

    /**
     * This is where the system is being initialized from.
     */
    public function init()
    {
        // Yii initialization is a must
        parent::init();
        // universal storage 
        $this->var=new MVariable;
        // call our initialization class
        W3Init::controller();
        // set user preferences (interface, language, and so on)
        if(!Yii::app()->user->isGuest)
        {
            if(isset(Yii::app()->user->interface) && !empty(Yii::app()->user->interface))
                // set user preferred interface
                W3::setInterface(Yii::app()->user->interface);
            if(isset(Yii::app()->user->language) && !empty(Yii::app()->user->language))
                // set user preferred language
                W3::setLanguage(Yii::app()->user->language);
        }
        // parameters were loaded before language was set, now it needs to be translated
        MParams::i18n();
    }

    /**
     * Returns the URL where to go now to.
     * @return array action route
     */
    public function getGotoUrl()
    {
        $controller=$this->id;
        $action=$this->action->id;
        $defaultUrl=array('user/show');
        // return url based on the current controller and the action being executed
        if($controller==='site' && $action==='index')
            $retval=Yii::app()->user->isGuest ? Yii::app()->user->loginUrl : $defaultUrl;
        else if($controller==='expense' && $action==='delete')
            $retval=array($controller.'/');
        else if($controller==='expense' && $action==='update')
            $retval=array($controller.'/');
        else if($controller==='invoice' && $action==='create')
            $retval=array($controller.'/');
        else if($controller==='invoice' && $action==='update')
            $retval=array($controller.'/');
        else if($controller==='task' && $action==='grid')
            $retval=$defaultUrl;
        else if($controller==='time' && $action==='delete')
            $retval=array($controller.'/');
        else if($controller==='time' && $action==='update')
            $retval=array($controller.'/');
        else if($controller==='user' && $action==='create')
            $retval=array($controller.'/');
        else if($controller==='user' && $action==='login')
        {
            if(Yii::app()->user->returnUrl!==null)
                // got here via {@link CWebUser::loginRequired} - go to the previous url
                $retval=Yii::app()->user->returnUrl;
            else if(User::isConsultant() || User::isManager() || User::isAdministrator())
                // consultant, manager and administrator - go to the task page
                $retval=array('task/');
            else if(User::isClient())
                // client - go to view my company page
                $retval=array('company/show/my');
            else
                $retval=$defaultUrl;
        }
        else if($controller==='user' && $action==='logout')
            $retval=Yii::app()->user->loginUrl;
        else if($controller==='user' && $action==='register')
            $retval=Yii::app()->user->loginUrl;
        return isset($retval) ? $retval : Yii::app()->homeUrl;
    }

    /**
     * Returns jqGrid searching keyword formula array.
     * @return array
     */
    public function getJqGridKeywordFormulaArray()
    {
        return array(
            'eq'=>'keyword',
            'ne'=>'keyword',
            'lt'=>'keyword',
            'le'=>'keyword',
            'gt'=>'keyword',
            'ge'=>'keyword',
            'bw'=>'keyword%',
            'bn'=>'keyword%',
            'in'=>'%keyword%', //'keyword'
            'ni'=>'%keyword%', //'keyword'
            'ew'=>'%keyword',
            'en'=>'%keyword',
            'cn'=>'%keyword%',
            'nc'=>'%keyword%',
        );
    }

    /**
     * Returns jqGrid searching operation array.
     * @return array
     */
    public function getJqGridOperationArray()
    {
        return array(
            'eq'=>'=',
            'ne'=>'!=',
            'lt'=>'<',
            'le'=>'<=',
            'gt'=>'>',
            'ge'=>'>=',
            'bw'=>'LIKE',
            'bn'=>'NOT LIKE',
            'in'=>'LIKE', //'IS IN'
            'ni'=>'NOT LIKE', //'IS NOT IN'
            'ew'=>'LIKE',
            'en'=>'NOT LIKE',
            'cn'=>'LIKE',
            'nc'=>'NOT LIKE',
        );
    }

    /**
     * Print out the data in the json format.
     * @param array of data
     */
    public function printJson($data)
    {
        if(!headers_sent())
            header('Content-type: application/json');
        echo json_encode($data);
    }

    /**
     * Print out the data in the json format and exit.
     * @param array of data
     */
    public function printJsonExit($data)
    {
        $this->printJson($data);
        exit;
    }

    /**
     * Process jqGrid request. jqGrid is specifying query details using the POST request.
     * page - page number
     * rows - page size
     * sidx - field to sort by
     * sord - sorting direction (asc or desc)
     * searchField - field to search by (optional)
     * searchString - string to search for (optional)
     * searchOper - search operation (optional)
     * @return array
     */
    public function processJqGridRequest()
    {
        // create a bridge between jqGrid and Yii
        $jqGrid['page']=(isset($_POST['page']) && ctype_digit($_POST['page']) && $_POST['page']>=1) ? $_POST['page'] : null;
        $jqGrid['pageSize']=(isset($_POST['rows']) && ctype_digit($_POST['rows']) && $_POST['rows']>=1 && $_POST['rows']<=500) ? $_POST['rows'] : null;
        $jqGrid['sort']=isset($_POST['sidx']) ? $_POST['sidx'] : null;
        if($jqGrid['sort']!==null && isset($_POST['sord']) && $_POST['sord']==='desc')
            $jqGrid['sort'].='.desc';
        $jqGrid['searchField']=isset($_POST['searchField']) ? $_POST['searchField'] : null;
        $jqGrid['searchString']=isset($_POST['searchString']) ? $_POST['searchString'] : null;
        $jqGrid['searchOper']=isset($_POST['searchOper']) ? $_POST['searchOper'] : null;
        // port the jqGrid request parameters to the regular Yii variables
        if($jqGrid['page']!==null)
            $_GET['page']=$jqGrid['page'];
        if($jqGrid['sort']!==null)
            $_GET['sort']=$jqGrid['sort'];
        return $jqGrid;
    }
}