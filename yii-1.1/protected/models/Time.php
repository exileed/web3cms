<?php

class Time extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'Time':
     * @var integer $id
     * @var integer $taskId
     * @var integer $managerId
     * @var integer $consultantId
     * @var string $title
     * @var string $excerpt
     * @var string $excerptMarkup
     * @var string $content
     * @var string $contentMarkup
     * @var string $isConfirmed
     * @var integer $confirmationTime
     * @var string $timeDate
     * @var integer $spentMinute
     * @var integer $billedMinute
     * @var integer $invoiceId
     * @var double $invoiceAmount
     * @var integer $createTime
     * @var integer $updateTime
     */
    public $spentH;
    public $spentM;
    public $billedH;
    public $billedM;

    const IS_CONFIRMED='1';
    const IS_NOT_CONFIRMED='0';

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
        return 'time';
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
        $retval[]=array('taskId, managerId, consultantId, invoiceId, confirmationTime, spentMinute, billedMinute, createTime, updateTime', 'numerical', 'integerOnly'=>true);
        $retval[]=array('invoiceAmount', 'numerical');
        $retval[]=array('spentH, billedH', 'numerical');
        $retval[]=array('spentM, billedM', 'numerical', 'integerOnly'=>true);
        $retval[]=array('consultantId, content, contentMarkup, managerId, spentH, spentM, spentMinute, taskId, timeDate, title', 'safe');
        if(Yii::app()->user->checkAccess(User::MANAGER) || Yii::app()->user->checkAccess(User::ADMINISTRATOR))
            $retval[]=array('billedH, billedM, billedMinute, invoiceAmount, invoiceId, isConfirmed', 'safe');
        return $retval;
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one time belongs to one 'consultant' record associated
            'consultant' => array(self::BELONGS_TO,'User','consultantId',
                'alias'=>'Time_Consultant'
            ),
            // one time belongs to one 'invoice' record associated
            'invoice' => array(self::BELONGS_TO,'Invoice','invoiceId',
                'alias'=>'Time_Invoice'
            ),
            // one time belongs to one 'manager' record associated
            'manager' => array(self::BELONGS_TO,'User','managerId',
                'alias'=>'Time_Manager'
            ),
            // one time belongs to one 'task' record associated
            'task' => array(self::BELONGS_TO,'Task','taskId',
                'alias'=>'Time_Task'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'billedH'=>Yii::t('t','Billed hours'),
            'billedM'=>Yii::t('t','Billed minutes'),
            'billedMinute'=>Yii::t('t','Billed time'),
            'Billed'=>Yii::t('t','Billed'),
            'Bld'=>Yii::t('t','Bld.[billed]'),
            'confirmationTime'=>Yii::t('t','Confirmation date'),
            'consultantId'=>Yii::t('t','Consultant'),
            'content'=>Yii::t('t','Work report'),
            'createTime'=>Yii::t('t','Record created'),
            'Date'=>Yii::t('t','Date'),
            'id'=>Yii::t('t','Number'),
            'invoiceAmount'=>Yii::t('t','Invoice amount'),
            'invoiceId'=>Yii::t('t','Invoice'),
            'isConfirmed'=>Yii::t('t','Time is confirmed'),
            'managerId'=>Yii::t('t','Manager'),
            'spentH'=>Yii::t('t','Spent hours'),
            'spentM'=>Yii::t('t','Spent minutes'),
            'spentMinute'=>Yii::t('t','Spent time'),
            'Spent'=>Yii::t('t','Spent'),
            'Spt'=>Yii::t('t','Spt.[spent]'),
            'taskId'=>Yii::t('t','Task'),
            'timeDate'=>Yii::t('t','Time date'),
            'title'=>Yii::t('t','Note'),
            'updateTime'=>Yii::t('t','Update date'),
            'excerpt' => 'Excerpt',
            'excerptMarkup' => 'Excerpt Markup',
            'contentMarkup' => 'Content Markup',
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
        // number of minutes = number of minutes + number of hours * 60
        if($this->spentH!==null || $this->spentM!==null)
            $this->spentMinute=(int)((float)$this->spentH*60 + (int)$this->spentM);
        if($this->billedH!==null || $this->billedM!==null)
            $this->billedMinute=(int)((float)$this->billedH*60 + (int)$this->billedM);
        // avoid sql error 'out of range value adjusted for column'
        if($this->spentMinute>999999)
            $this->spentMinute=999999;
        if($this->billedMinute>999999)
            $this->billedMinute=999999;
        // avoid sql error 'incorrect date value'
        isset($_POST[__CLASS__]['timeDate']) && ($this->timeDate=MDate::formatToDb($this->timeDate,'date'));
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
     * @param mixed value
     * @return string the attribute value's translation
     */
    public function getAttributeView($attribute,$value=null)
    {
        if($value===null)
            $value=$this->$attribute;
        switch($attribute)
        {
            case 'billedMinute':
            case 'spentMinute':
                $round=floor($value/60);
                return $round.':'.sprintf('%02u',fmod($value,60));
            case 'isConfirmed':
                switch($value)
                {
                    case self::IS_CONFIRMED:
                        return Yii::t('attr','Yes (The record is confirmed by the project manager)');
                    case self::IS_NOT_CONFIRMED:
                        return Yii::t('attr','No (The record is not confirmed by the project manager)');
                    case null:
                        return Yii::t('attr','By default (The record is not confirmed by the project manager)');
                    default:
                        return $value;
                }
            default:
                return $value;
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
            case 'billedM':
            case 'spentM':
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
            case 'consultantId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(User::model()->findAllActiveRecords(array($this->$attribute)),'id','screenName');
            case 'isConfirmed':
                return array(
                    null=>Yii::t('attr','By default (The record is not confirmed by the project manager)'),
                    self::IS_NOT_CONFIRMED=>Yii::t('attr','No (The record is not confirmed by the project manager)'),
                    self::IS_CONFIRMED=>Yii::t('attr','Yes (The record is confirmed by the project manager)'),
                );
            case 'managerId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(User::model()->findAllActiveRecords(array($this->$attribute)),'id','screenName');
            case 'taskId':
                return array(null=>Yii::t('t','- Please select -')) + CHtml::listData(Task::model()->findAllOpenRecords(array($this->$attribute)),'id','title');
            default:
                return $this->$attribute;
        }
    }

    public function spentHour()
    {
        
    }
}