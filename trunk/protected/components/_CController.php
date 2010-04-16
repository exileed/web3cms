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
     * @var CActiveRecord the currently loaded data model instance.
     */
    private $_model;

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
        // parameters were loaded before language was set, now they need to be translated
        MParams::i18n();
    }

    /**
     * This method is invoked right before an action is to be executed (after all possible filters).
     * @param CAction the action to be executed.
     */
    protected function beforeAction($action)
    {
        return $this->checkAccessBeforeAction();
    }

    /**
     * Performs access check before action is executed.
     * If access is denied - redirect to an appropriate page or display an empty screen.
     * See {@link getCheckAccessOnActions} for the list of actions to check access for.
     */
    public function checkAccessBeforeAction()
    {
        $actionId=$this->action->id;
        $checkAccessOnActions=$this->getCheckAccessOnActions();
        if(array_key_exists($actionId,$checkAccessOnActions))
        {
            $a=$checkAccessOnActions[$actionId];
            // first check whether it should be an ajax or a post request
            if(!isset($a['request']) ||
                ($a['request']==='ajax' && Yii::app()->request->isAjaxRequest) ||
                ($a['request']==='post' && Yii::app()->request->isPostRequest)
            )
            {
                // define route variable
                if(!isset($a['route']) &&
                    (isset($a['moduleId']) || isset($a['controllerId']) || isset($a['actionId']))
                )
                {
                    $routeA=array();
                    if(isset($a['moduleId']) || $this->module)
                        $routeA[]=isset($a['moduleId'])?$a['moduleId']:$this->module->id;
                    $routeA[]=isset($a['controllerId'])?$a['controllerId']:$this->id;
                    $routeA[]=isset($a['actionId'])?$a['actionId']:$this->action->id;
                    $route=implode('/',$routeA);
                }
                else
                    $route=isset($a['route'])?$a['route']:$this->route;
                // use power of rbac. see {@link _CUserIdentity::authorize} for assignment
                if(!Yii::app()->user->checkAccess($route))
                {
                    // access denied
                    // define error message variable
                    if(!isset($a['messageRoute']) &&
                        (isset($a['messageModuleId']) || isset($a['messageControllerId']) || isset($a['messageActionId']))
                    )
                    {
                        $routeA=array();
                        if(isset($a['messageModuleId']) || $this->module)
                            $routeA[]=isset($a['messageModuleId'])?$a['messageModuleId']:$this->module->id;
                        $routeA[]=isset($a['messageControllerId'])?$a['messageControllerId']:$this->id;
                        $routeA[]=isset($a['messageActionId'])?$a['messageActionId']:$this->action->id;
                        $messageRoute=implode('/',$routeA);
                    }
                    else
                        $messageRoute=isset($a['messageRoute'])?$a['messageRoute']:$route;
                    $message=Yii::t('accessDenied',$messageRoute);
                    // do what expected: exit or print json or redirect
                    if($a==='exit' || (isset($a['do']) && $a['do']==='exit'))
                        // this results in an empty screen. good for simple ajax requests
                        return false;
                    else if($a==='json' || (isset($a['do']) && $a['do']==='json'))
                    {
                        // print out json document with error message.
                        // ideal for ajax requests that expect a status and a message to be returned
                        $this->printJsonExit(array('status'=>'error','message'=>$message));
                        return false;
                    }
                    else
                    {
                        // set error message. should be displayed when redirect will be completed
                        MUserFlash::setTopError($message);
                        // redirect now to either user/show or to a more appropriate page
                        Yii::app()->request->redirect($this->getGotoUrl());
                    }
                }
            }
        }
        return true;
    }

    /**
     * Returns array of actions that must be checked for access using rbac.
     * Check {@link checkAccessBeforeAction} to see how is it being used.
     */
    public function getCheckAccessOnActions()
    {
        $retval=array(
            'ajaxDelete'=>array('request'=>'ajax','actionId'=>'delete','do'=>'json'),
            'delete'=>'',
            'grid'=>'',
            'gridData'=>array('actionId'=>'grid','do'=>'exit'),
            'list'=>'',
        );
        return $retval;
    }

    /**
     * Returns the URL of where now to go.
     * @return array action route.
     */
    public function getGotoUrl()
    {
        $controller=$this->id;
        $action=$this->action->id;
        $defaultUrl=array('user/show');
        $controllerUrl=array($controller.'/');
        // return url based on the current controller and the action being executed
        if($controller==='site' && $action==='index')
            $retval=Yii::app()->user->isGuest ? Yii::app()->user->loginUrl : $defaultUrl;
        else if($controller==='company' && $action==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controller==='company' && $action==='show')
        {
            if(!Yii::app()->user->isGuest)
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='companyPayment' && $action==='show')
        {
            if(!Yii::app()->user->isGuest)
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='expense' && $action==='delete')
            $retval=$controllerUrl;
        else if($controller==='expense' && $action==='show')
        {
            if(!Yii::app()->user->isGuest)
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='expense' && $action==='update')
            $retval=$controllerUrl;
        else if($controller==='invoice' && $action==='create')
            $retval=$controllerUrl;
        else if($controller==='invoice' && $action==='show')
        {
            if(!Yii::app()->user->isGuest)
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='invoice' && $action==='update')
            $retval=$controllerUrl;
        else if($controller==='project' && $action==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controller==='project' && $action==='show')
        {
            if(User::isClient() || User::isConsultant() || User::isManager() || User::isAdministrator())
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='task' && $action==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controller==='task' && $action==='show')
        {
            if(User::isClient() || User::isConsultant() || User::isManager() || User::isAdministrator())
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='time' && $action==='delete')
            $retval=$controllerUrl;
        else if($controller==='time' && $action==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controller==='time' && $action==='show')
        {
            if(User::isClient() || User::isConsultant() || User::isManager() || User::isAdministrator())
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='time' && $action==='update')
            $retval=$controllerUrl;
        else if($controller==='user' && $action==='create')
            $retval=$controllerUrl;
        else if($controller==='user' && $action==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controller==='user' && $action==='login')
        {
            if(Yii::app()->user->returnUrl!==null &&
                // hmm... {@link CWebUser::getReturnUrl} returns scriptUrl if returnUrl is not set
                // hopefully this will be changed in the Yii 1.1 branch
                Yii::app()->user->returnUrl!==Yii::app()->request->scriptUrl
            )
                // got here via {@link CWebUser::loginRequired} - go to the previous url
                $retval=Yii::app()->user->returnUrl;
            else if((User::isConsultant() || User::isManager() || User::isAdministrator()) &&
                file_exists(Yii::app()->basePath.'/models/Task.php') &&
                file_exists(Yii::app()->basePath.'/controllers/TaskController.php')
            )
                // consultant, manager and administrator - go to the task page
                $retval=array('task/');
            else if(User::isClient() &&
                file_exists(Yii::app()->basePath.'/models/Company.php') &&
                file_exists(Yii::app()->basePath.'/controllers/CompanyController.php')
            )
                // client - go to view my company page
                $retval=array('company/show/my');
            else
                $retval=$defaultUrl;
        }
        else if($controller==='user' && $action==='logout')
            $retval=Yii::app()->user->loginUrl;
        else if($controller==='user' && $action==='register')
            $retval=Yii::app()->user->loginUrl;
        else if($controller==='user' && $action==='show')
        {
            if(User::isManager() || User::isAdministrator())
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='user' && $action==='update')
        {
            if(User::isManager() || User::isAdministrator())
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controller==='user' && $action==='updateInterface')
        {
            if(User::isManager() || User::isAdministrator())
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        return isset($retval) ? $retval : Yii::app()->homeUrl;
    }

    /**
     * Return the default URL if user is logged in.
     * Otherwise, set user return url via {@link CWebUser::setReturnUrl} and return login URL.
     * @param string default URL
     * @return string goto URL
     */
    protected function _getGotoUrl($defaultUrl)
    {
        if(Yii::app()->user->isGuest)
        {
            Yii::app()->user->setReturnUrl(Yii::app()->request->url);
            $retval=Yii::app()->user->loginUrl;
        }
        else
            $retval=$defaultUrl;
        return $retval;
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
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param array of parameters
     * @param boolean whether throw exception if model is not found
     */
    public function loadModel($parameters=array(),$throwException=true)
    {
        if($this->_model===null)
        {
            // processing parameters
            if(ctype_digit($parameters))
                $id=$parameters;
            else if(isset($parameters['id']))
                $id=$parameters['id'];
            else if(isset($_GET['id']))
                $id=$_GET['id'];
            else
                $id=null;
            $with=isset($parameters['with']) ? $parameters['with'] : null;
            // load the model
            if($id!==null)
            {
                $modelName=isset($this->modelName)?$this->modelName:str_replace('Controller','',get_class($this));
                if(file_exists(Yii::app()->basePath.'/models/'.$modelName.'.php'))
                {
                    $ar=call_user_func(array($modelName,'model'));
                    if($with===null)
                        $this->_model=$ar->findByPk($id);
                    else
                        $this->_model=$ar->with($with)->findByPk($id);
                }
                else
                    Yii::log(W3::t('system',
                        'Class {class} does not exist. Method called: {method}.',
                        array(
                            '{class}'=>$modelName,
                            '{method}'=>get_class($this).'->'.__FUNCTION__.'()'
                        )
                    ),'warning','w3');
            }
            if($throwException && $this->_model===null)
                // if model is not found - throw 404
                throw new CHttpException(404,'The requested page does not exist.');
        }
        return $this->_model;
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