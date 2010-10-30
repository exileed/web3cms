<?php
/**
 * _CController class file.
 * Add and redefine controller methods of Yii core class CController.
 * Main initialization - it's run before everything else.
 */
class _CController extends CController
{
    /**
     * @var string specifies the default action to be 'grid'.
     * Note: Yii default action is 'index'.
     */
    public $defaultAction='grid';

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
     * @param CAction the action to be executed.
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
                // define route and other variables
                if(!isset($a['route']) &&
                    (isset($a['moduleId']) || isset($a['controllerId']) || isset($a['actionId']))
                )
                {
                    $routeA=array();
                    if(isset($a['moduleId']) || $this->module)
                        $routeA['moduleId']=isset($a['moduleId'])?$a['moduleId']:$this->module->id;
                    $routeA['controllerId']=isset($a['controllerId'])?$a['controllerId']:$this->id;
                    $routeA['actionId']=isset($a['actionId'])?$a['actionId']:$this->action->id;
                    $route=implode('/',$routeA);
                }
                else
                    $route=isset($a['route'])?$a['route']:$this->route;
                if(!isset($routeA))
                {
                    // attempt to generate access route
                    $routeT=explode('/',$route);
                    if(count($routeT)==2)
                        $routeA=array('controllerId'=>$routeT[0],'actionId'=>$routeT[1]);
                    else if(count($routeT)==3)
                        $routeA=array('moduleId'=>$routeT[0],'controllerId'=>$routeT[1],'actionId'=>$routeT[2]);
                    unset($routeT);
                }
                $params=(isset($a['params'])&&is_array($a['params']))?$a['params']:array();
                foreach($params as $key=>$value)
                {
                    if(isset($value['call_user_func']))
                        $params[$key]=call_user_func(array($this,$value['call_user_func']));
                }
                // use power of rbac. see {@link _CUserIdentity::authorize} for assignment
                if(!Yii::app()->user->checkAccess($route,$params))
                {
                    // access denied
                    // define error message variable
                    if(!isset($a['messageRoute']) &&
                        (isset($a['messageModuleId']) || isset($a['messageControllerId']) || isset($a['messageActionId']))
                    )
                    {
                        $routeM=array();
                        if(isset($a['messageModuleId']) || $this->module)
                            $routeM['messageModuleId']=isset($a['messageModuleId'])?$a['messageModuleId']:(isset($routeA['moduleId'])?$routeA['moduleId']:$this->module->id);
                        $routeM['messageControllerId']=isset($a['messageControllerId'])?$a['messageControllerId']:(isset($routeA['controllerId'])?$routeA['controllerId']:$this->id);
                        if(isset($a['messageActionId']))
                        {
                            if(is_array($a['messageActionId']))
                            {
                                foreach($a['messageActionId'] as $id=>$bizRule)
                                {
                                    if(empty($bizRule) || @eval($bizRule)!=0)
                                        $routeM['messageActionId']=$id;
                                }
                            }
                            else
                                $routeM['messageActionId']=$a['messageActionId'];
                        }
                        if(!isset($routeM['messageActionId']))
                            $routeM['messageActionId']=isset($routeA['actionId'])?$routeA['actionId']:$this->action->id;
                        $messageRoute=implode('/',$routeM);
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
                        // FIXME
                        MUserFlash::setSidebarInfo(Yii::t('hint','Hint: Check <tt>components/_CUserIdentity:authorize()</tt> to change allowed actions.'));
                        // redirect now to user/login, user/show or to a more appropriate page
                        $this->redirect($this->getGotoUrl());
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
            'ajaxDelete'=>array('request'=>'ajax','actionId'=>'delete','do'=>'json',
            	'params'=>array('model'=>array('call_user_func'=>'loadModel')),
            	'messageActionId'=>array('deleteWhenInvoiceIsSet'=>'return ($params["model"] instanceof Expense || $params["model"] instanceof Time) && $params["model"]->invoiceId>=1;'),
            ),
            'create'=>'',
            'delete'=>array('params'=>array('model'=>array('call_user_func'=>'loadModel')),
            	'messageActionId'=>array('deleteWhenInvoiceIsSet'=>'return ($params["model"] instanceof Expense || $params["model"] instanceof Time) && $params["model"]->invoiceId>=1;'),
            ),
            'grid'=>'',
            'gridData'=>array('actionId'=>'grid','do'=>'exit'),
            'list'=>'',
            'update'=>array('params'=>array('model'=>array('call_user_func'=>'loadModel')),
            	'messageActionId'=>array('updateWhenInvoiceIsSet'=>'return ($params["model"] instanceof Expense || $params["model"] instanceof Time) && $params["model"]->invoiceId>=1;'),
            ),
        );
        return $retval;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param array of parameters
     * @param boolean whether throw exception if model is not found
     */
    public function loadModel($params=array(),$throwException=null)
    {
        if($this->_model===null)
        {
            // processing parameters
            if(ctype_digit($params))
                $id=$params;
            else if(isset($params['id']))
                $id=$params['id'];
            else
                $id=$this->loadModelId();
            $with=(isset($params['with'])&&$params['with']!==null) ? $params['with'] : $this->loadModelWith();
            if($throwException===null)
                $throwException=$this->loadModelThrowException();
            // load the model
            if($id!==null)
            {
                // calculate model name. e.g. for 'UserController' model should be 'User'
                $class=get_class($this);
                $modelName=isset($this->modelName) ? $this->modelName : null;
                if($modelName===null)
                    $modelName=substr($class,-10)==='Controller' ? substr($class,0,strlen($class)-10) : $class;
                if(file_exists(Yii::app()->basePath.'/models/'.$modelName.'.php'))
                {
                    // find model by primary key
                    $ar=call_user_func(array($modelName,'model'));
                    if($with===array())
                        $this->_model=$ar->findByPk($id);
                    else
                        $this->_model=$ar->with($with)->findByPk($id);
                }
                else
                    // error - model class file is missing
                    Yii::log(W3::t('system',
                        'Class {class} does not exist. Method called: {method}.',
                        array(
                            '{class}'=>$modelName,
                            '{method}'=>get_class($this).'->'.__FUNCTION__.'()'
                        )
                    ),'warning','w3');
            }
            if($this->_model===null && $throwException)
                // if model is not found - throw 404
                throw new CHttpException(404,'The requested page does not exist.');
        }
        return $this->_model;
    }

    /**
     * Returns id of the model that should be now loaded.
     * Is invoked by {@link loadModel}.
     * @return mixed int or numeric string of the model to be loaded or null.
     */
    public function loadModelId()
    {
        if(isset($_GET['id']))
            $id=$_GET['id'];
        else if(isset($_POST['id']))
            $id=$_POST['id'];
        else
            $id=null;
        return $id;
    }

    /**
     * Returns array of associated records which this model should be loaded with.
     * Is invoked by {@link loadModel}.
     * @return array of models to load this model with. 
     */
    public function loadModelWith()
    {
        return array();
    }

    /**
     * Returns boolean whether throw 404 http exception if model is not loaded.
     * Is invoked by {@link loadModel}.
     * @return boolean whether throw exception on model not loaded. 
     */
    public function loadModelThrowException()
    {
        return false;
    }

    /**
     * Returns the URL of where now to go.
     * @return array action route or string url.
     */
    public function getGotoUrl($params=array())
    {
        // set variables
        $controllerId=$this->id;
        $actionId=$this->action->id;
        $defaultUrl=array('user/show');
        $controllerUrl=array($controllerId.'/');
        // return url based on the current controller and the action being executed
        if($controllerId==='site' && $actionId==='index')
            $retval=Yii::app()->user->isGuest ? Yii::app()->user->loginUrl : $defaultUrl;
        else if($controllerId==='company' && $actionId==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='company' && $actionId==='show')
        {
            if(!Yii::app()->user->isGuest)
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='companyPayment' && $actionId==='show')
        {
            if(!Yii::app()->user->isGuest)
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='expense' && $actionId==='delete')
            // is there a way to replace 'grid' with $defaultAction?
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='expense' && $actionId==='show')
        {
            if(!Yii::app()->user->isGuest)
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='expense' && $actionId==='update')
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='invoice' && $actionId==='create')
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='invoice' && $actionId==='show')
        {
            if(!Yii::app()->user->isGuest)
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='invoice' && $actionId==='update')
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='project' && $actionId==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='project' && $actionId==='show')
        {
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='task' && $actionId==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='task' && $actionId==='show')
        {
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='time' && $actionId==='delete')
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='time' && $actionId==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='time' && $actionId==='show')
        {
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='time' && $actionId==='update')
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='user' && $actionId==='create')
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='user' && $actionId==='grid')
            $retval=$this->_getGotoUrl($defaultUrl);
        else if($controllerId==='user' && $actionId==='login')
        {
            if(Yii::app()->user->returnUrl!==null &&
                // hmm... {@link CWebUser::getReturnUrl} returns scriptUrl if returnUrl is not set
                // hopefully this will be changed in the Yii 1.1 branch
                Yii::app()->user->returnUrl!==Yii::app()->request->scriptUrl
            )
                // got here via {@link CWebUser::loginRequired} - go to the previous url
                $retval=Yii::app()->user->returnUrl;
            else if((Yii::app()->user->checkAccess(User::CONSULTANT) || Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR)) &&
                file_exists(Yii::app()->basePath.'/models/Task.php') &&
                file_exists(Yii::app()->basePath.'/controllers/TaskController.php')
            )
                // consultant, manager and administrator - go to the task page
                $retval=array('task/');
            else if(Yii::app()->user->checkAccess(User::CLIENT) &&
                file_exists(Yii::app()->basePath.'/models/Company.php') &&
                file_exists(Yii::app()->basePath.'/controllers/CompanyController.php')
            )
                // client - go to view my company page
                $retval=array('company/show/my');
            else
                $retval=$defaultUrl;
        }
        else if($controllerId==='user' && $actionId==='logout')
            $retval=Yii::app()->user->loginUrl;
        else if($controllerId==='user' && $actionId==='register')
            $retval=Yii::app()->user->loginUrl;
        else if($controllerId==='user' && $actionId==='show')
        {
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='user' && $actionId==='update')
        {
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
                $retval=$controllerUrl;
            else
                $retval=$this->_getGotoUrl($defaultUrl);
        }
        else if($controllerId==='user' && $actionId==='updateInterface')
        {
            if(Yii::app()->user->checkAccess($controllerId.'/grid'))
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
}