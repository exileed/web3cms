<?php

class Task extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'w3_task':
     * @var integer $id
     * @var integer $projectId
     * @var integer $companyId
     * @var string $title
     * @var string $excerpt
     * @var string $excerptMarkup
     * @var string $content
     * @var string $contentMarkup
     * @var string $isConfirmed
     * @var integer $confirmationTime
     * @var integer $priority
     * @var string $status
     * @var integer $estimateMinute
     * @var string $dueDate
     * @var integer $completePercent - delete this
     * @var string $completeDate - delete this
     * @var double $hourlyRate - is value from project
     * @var string $openDate
     * @var string $closeDate - is set when task is completed
     * @var string $report
     * @var string $reportMarkup
     * @var string $affectedPage
     * @var string $documentationUrl
     * @var string $reportingEmail
     * @var integer $createTime
     * @var integer $updateTime
     */
    public $estimateH;
    public $estimateM;

    /**
     * @var integer counters used in the sql queries
     */
    public $countTime;

    const IS_CONFIRMED='1';
    const IS_NOT_CONFIRMED='0';
    const PRIORITY_HIGHEST=1;
    const PRIORITY_HIGH=2;
    const PRIORITY_MEDIUM=3;
    const PRIORITY_LOW=4;
    const PRIORITY_LOWEST=5;
    const CANCELLED='cancelled';
    const COMPLETED='completed';
    const IN_PROGRESS='inProgress';
    const NOT_STARTED='notStarted';
    const READY_FOR_PRODUCTION='readyForProduction';
    const READY_TO_TEST='readyToTest';

    /**
     * Returns the static model of the specified AR class.
     * @return CActiveRecord the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name (without prefix)
     */
    protected function _tableName()
    {
        return 'task';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $retval=array();
        $retval[]=array('title', 'length', 'max'=>255);
        $retval[]=array('excerptMarkup', 'length', 'max'=>32);
        $retval[]=array('contentMarkup', 'length', 'max'=>32);
        $retval[]=array('status', 'length', 'max'=>32);
        $retval[]=array('reportMarkup', 'length', 'max'=>32);
        $retval[]=array('affectedPage', 'length', 'max'=>255);
        $retval[]=array('documentationUrl', 'length', 'max'=>255);
        $retval[]=array('reportingEmail', 'length', 'max'=>128);
        $retval[]=array('projectId, companyId, confirmationTime, priority, estimateMinute, completePercent, createTime, updateTime', 'numerical', 'integerOnly'=>true);
        $retval[]=array('hourlyRate', 'numerical');
        $retval[]=array('estimateH', 'numerical');
        $retval[]=array('estimateM', 'numerical', 'integerOnly'=>true);
        $retval[]=array('affectedPage, closeDate, companyId, completeDate, completePercent, content, documentationUrl, dueDate, estimateH, estimateM, estimateMinute, openDate, priority, projectId, report, reportingEmail, status, title', 'safe');
        if(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR))
            $retval[]=array('isConfirmed', 'safe');
        if(Yii::app()->user->checkAccess(User::ADMINISTRATOR))
            $retval[]=array('hourlyRate', 'safe');
        return $retval;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one task has one 'company' record associated
            'company' => array(self::BELONGS_TO,'Company','companyId',
                'alias'=>'Task_Company'
            ),
            // one task has one 'project' record associated
            'project' => array(self::BELONGS_TO,'Project','projectId',
                'alias'=>'Task_Project'
            ),
            // many task has many 'consultant' records associated
            'allConsultant' => array(self::MANY_MANY,'User',
                User2Task::model()->tableName().'(taskId,userId)',
                'on'=>"`allConsultant_Task_Consultant`.`role`='".User2Task::CONSULTANT."'",
                'order'=>"`allConsultant_Task_Consultant`.`userPriority` ASC, `allConsultant_Task_Consultant`.`id` ASC",
                'alias'=>'Task_Consultant'
            ),
            // one task has many 'consultant2task' records associated
            'allConsultant2Task' => array(self::HAS_MANY,'User2Task','taskId',
                'on'=>"`t`.`role`='".User2Task::CONSULTANT."'",
                'order'=>"`t`.`userPriority` ASC, `t`.`id` ASC",
                'alias'=>'Task_Consultant2Task'
            ),
            // many task has many 'manager' records associated
            'allManager' => array(self::MANY_MANY,'User',
                User2Task::model()->tableName().'(taskId,userId)',
                'on'=>"`allManager_Task_Manager`.`role`='".User2Task::MANAGER."'",
                'order'=>"`allManager_Task_Manager`.`userPriority` ASC, `allManager_Task_Manager`.`id` ASC",
                'alias'=>'Task_Manager'
            ),
            // one task has many 'manager2task' records associated
            'allManager2Task' => array(self::HAS_MANY,'User2Task','taskId',
                'on'=>"`t`.`role`='".User2Task::MANAGER."'",
                'order'=>"`t`.`userPriority` ASC, `t`.`id` ASC",
                'alias'=>'Task_Manager2Task'
            ),
            // one task has many 'time' records associated
            'allTime' => array(self::HAS_MANY,'Time','taskId',
                'order'=>"`t`.`id` ASC",
                'alias'=>'Task_Time'
            ),
            // one task has a number of 'time' records associated
            'timeCount' => array(self::STAT,'Time','timeId'),
            // many task has many 'user' records associated
            'allUser' => array(self::MANY_MANY,'User',
                User2Task::model()->tableName().'(taskId,userId)',
                'order'=>"`allUser_Task_User`.`userPriority` ASC, `allUser_Task_User`.`id` ASC",
                'alias'=>'Task_User'
            ),
            // one task has many 'user2task' records associated
            'allUser2Task' => array(self::HAS_MANY,'User2Task','taskId',
                'order'=>"`t`.`userPriority` ASC, `t`.`id` ASC",
                'alias'=>'Task_User2Task'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'affectedPage'=>Yii::t('t','Affected page'),
            'closeDate'=>Yii::t('t','Close date'),
            'companyId'=>Yii::t('t','Company'),
            'completeDate'=>Yii::t('t','Complete date'),
            'completePercent'=>Yii::t('t','Percents completed'),
            'confirmationTime'=>Yii::t('t','Confirmation date'),
            'content'=>Yii::t('t','Customer requirement'),
            'createTime'=>Yii::t('t','Record created'),
            'documentationUrl'=>Yii::t('t','Documentation URL'),
            'dueDate'=>Yii::t('t','Due date',array(0)),
            'Due'=>Yii::t('t','Due',array(0)),
            'estimateH'=>Yii::t('t','Estimated hours'),
            'estimateM'=>Yii::t('t','Estimated minutes'),
            'estimateMinute'=>Yii::t('t','Estimated time'),
            'hourlyRate'=>Yii::t('t','Hourly rate'),
            'id'=>Yii::t('t','ID'),
            'isConfirmed'=>Yii::t('t','Task is confirmed'),
            'openDate'=>Yii::t('t','Open date'),
            'priority'=>Yii::t('t','Priority'),
            'projectId'=>Yii::t('t','Project'),
            'Pr'=>Yii::t('t','Pr.[priority]'),
            'report'=>Yii::t('t','Work report'),
            'reportingEmail'=>Yii::t('t','Reporting email'),
            'status'=>Yii::t('t','Status'),
            'title'=>Yii::t('t','Title'),
            'updateTime'=>Yii::t('t','Update date'),
            'excerpt' => 'Excerpt',
            'excerptMarkup' => 'Excerpt Markup',
            'contentMarkup' => 'Content Markup',
            'reportMarkup' => 'Report Markup',
        );
    }

    /**
     * Prepares attributes before performing validation.
     */
    protected function beforeValidate()
    {
        $scenario=$this->getScenario();
        if(isset($_POST[__CLASS__]['isConfirmed']))
        {
            if($this->isConfirmed!==self::IS_CONFIRMED && $this->isConfirmed!==self::IS_NOT_CONFIRMED)
                // enum('0','1') null
                $this->isConfirmed=null;
            if($this->isConfirmed===self::IS_CONFIRMED && empty($this->confirmationTime))
                // set time on confirm
                $this->confirmationTime=time();
            else if($this->isConfirmed===self::IS_NOT_CONFIRMED && $this->confirmationTime!==null)
                // unset time on unconfirm
                $this->confirmationTime=null;
        }
        if(($this->status===self::CANCELLED || $this->status===self::COMPLETED) && empty($this->closeDate))
            // set close date on complete
            $this->closeDate=MDate::formatToDb(time(),'date');
        else if($this->status!==self::CANCELLED && $this->status!==self::COMPLETED && $this->closeDate!==null)
            // unset close date when task is re-opened
            $this->closeDate=null;
        $priority=(int)$this->priority;
        if(isset($_POST[__CLASS__]['priority']) && $priority!==self::PRIORITY_HIGHEST && $priority!==self::PRIORITY_HIGH
            && $priority!==self::PRIORITY_MEDIUM && $priority!==self::PRIORITY_LOW && $priority!==self::PRIORITY_LOWEST
        )
            // allow defined priorities only
            $this->priority=self::PRIORITY_MEDIUM;
        if(isset($_POST[__CLASS__]['status']) && $this->status!==self::CANCELLED && $this->status!==self::COMPLETED
            && $this->status!==self::IN_PROGRESS && $this->status!==self::NOT_STARTED
            && $this->status!==self::READY_FOR_PRODUCTION && $this->status!==self::READY_TO_TEST
        )
            // allow defined statuses only
            $this->status=self::NOT_STARTED;
        // number of minutes = number of minutes + number of hours * 60
        if($this->estimateH!==null || $this->estimateM!==null)
            $this->estimateMinute=(int)((float)$this->estimateH*60 + (int)$this->estimateM);
        // avoid sql error 'out of range value adjusted for column'
        if($this->estimateMinute>999999)
            $this->estimateMinute=999999;
        // avoid sql error 'incorrect date value'
        isset($_POST[__CLASS__]['dueDate']) && ($this->dueDate=MDate::formatToDb($this->dueDate,'date'));
        isset($_POST[__CLASS__]['openDate']) && ($this->openDate=MDate::formatToDb($this->openDate,'date'));
        // parent does all common work
        return parent::beforeValidate();
    }

    /**
     * Last model processing before save in db.
     */
    /*protected function beforeSave()
    {
        return true;
    }*/

    /**
     * Returns i18n (translated) representation of the attribute value for view.
     * @param string the attribute name
     * @return string the attribute value's translation
     */
    public function getAttributeView($attribute)
    {
        switch($attribute)
        {
            case 'estimateMinute':
                $round=floor($this->$attribute/60);
                return $round.':'.sprintf('%02u',fmod($this->$attribute,60));
            case 'isConfirmed':
                switch($this->isConfirmed)
                {
                    case self::IS_CONFIRMED:
                        return Yii::t('attr','Yes (The record is confirmed by the project manager)');
                    case self::IS_NOT_CONFIRMED:
                        return Yii::t('attr','No (The record is not confirmed by the project manager)');
                    case null:
                        return Yii::t('attr','By default (The record is not confirmed by the project manager)');
                    default:
                        return $this->isConfirmed;
                }
            case 'priority':
                switch($this->priority)
                {
                    case self::PRIORITY_HIGHEST:
                        return Yii::t('t','Highest[priority]');
                    case self::PRIORITY_HIGH:
                        return Yii::t('t','High[priority]');
                    /*case null:*/
                    case self::PRIORITY_MEDIUM:
                        return Yii::t('t','Medium[priority]');
                    case self::PRIORITY_LOW:
                        return Yii::t('t','Low[priority]');
                    case self::PRIORITY_LOWEST:
                        return Yii::t('t','Lowest[priority]');
                    default:
                        return $this->priority;
                }
            case 'status':
                switch($this->status)
                {
                    /*case null:*/
                    case self::NOT_STARTED:
                        return Yii::t('t','Not started[task]');
                    case self::IN_PROGRESS:
                        return Yii::t('t','In progress[task]');
                    case self::READY_TO_TEST:
                        return Yii::t('t','Ready to test[task]');
                    case self::READY_FOR_PRODUCTION:
                        return Yii::t('t','Ready for production[task]');
                    case self::COMPLETED:
                        return Yii::t('t','Completed[task]');
                    case self::CANCELLED:
                        return Yii::t('t','Cancelled[task]');
                    default:
                        return $this->status;
                }
            default:
                return $this->$attribute;
        }
    }

    /**
     * Returns data array of the attribute for create/update.
     * @param string the attribute name
     * @return array the attribute's data
     */
    public function getAttributeData($attribute)
    {
        switch($attribute)
        {
            case 'companyId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Company::model()->findAllActiveRecords(array($this->$attribute)),'id','title');
            case 'completePercent':
                return array(0=>0,5=>5,10=>10,15=>15,20=>20,25=>25,30=>30,35=>35,40=>40,45=>45,50=>50,55=>55,60=>60,65=>65,70=>70,75=>75,80=>80,85=>85,90=>90,95=>95,100=>100);
            case 'estimateM':
                $minutes=range(0,60,5);
                if(ctype_digit((string)$this->$attribute) && $this->$attribute>=0 && $this->$attribute<=60 && !in_array($this->$attribute,$minutes))
                {
                    $minutes[]=$this->$attribute;
                    sort($minutes);
                }
                $retval=array();
                foreach($minutes as $minute)
                    $retval[$minute]=sprintf('%02u',$minute);
                return $retval;
            case 'isConfirmed':
                return array(
                    null=>Yii::t('attr','By default (The record is not confirmed by the project manager)'),
                    self::IS_NOT_CONFIRMED=>Yii::t('attr','No (The record is not confirmed by the project manager)'),
                    self::IS_CONFIRMED=>Yii::t('attr','Yes (The record is confirmed by the project manager)'),
                );
            case 'priority':
                return array(
                    self::PRIORITY_HIGHEST=>Yii::t('t','Highest[priority]'),
                    self::PRIORITY_HIGH=>Yii::t('t','High[priority]'),
                    self::PRIORITY_MEDIUM=>Yii::t('t','Medium[priority]'),
                    self::PRIORITY_LOW=>Yii::t('t','Low[priority]'),
                    self::PRIORITY_LOWEST=>Yii::t('t','Lowest[priority]'),
                );
            case 'projectId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Project::model()->findAllOpenRecords(array($this->$attribute)),'id','title');
            case 'status':
                return array(
                    self::NOT_STARTED=>Yii::t('t','Not started[task]'),
                    self::IN_PROGRESS=>Yii::t('t','In progress[task]'),
                    self::READY_TO_TEST=>Yii::t('t','Ready to test[task]'),
                    self::READY_FOR_PRODUCTION=>Yii::t('t','Ready for production[task]'),
                    self::COMPLETED=>Yii::t('t','Completed[task]'),
                    self::CANCELLED=>Yii::t('t','Cancelled[task]'),
                );
            default:
                return $this->$attribute;
        }
    }

    /**
     * Find all open tasks.
     * @param array of additional conditions
     * @return array of Task objects
     */
    public function findAllOpenRecords($conditions=array())
    {
        $id=(isset($conditions[0]) && ctype_digit($conditions[0]) && $conditions[0]>=1) ? $conditions[0] : null;
        $criteria=new CDbCriteria;
        // close date is not set, or it is later than today
        $criteria->condition="((`t`.`closeDate` IS NULL OR TO_DAYS('".MDate::formatToDb(time(),'date')."') < TO_DAYS(`t`.`closeDate`)) AND `t`.`status`!='".self::CANCELLED."' AND `t`.`status`!='".self::COMPLETED."')";
        if($id)
            $criteria->condition.=" OR `t`.`id` = '$id'";
        return self::model()->findAll($criteria);
    }
}