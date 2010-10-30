<?php

class Invoice extends _CActiveRecord
{
    /**
     * The followings are the available columns in table 'w3invoice':
     * @var integer $id
     * @var integer $companyId
     * @var string $title
     * @var string $excerpt
     * @var string $excerptMarkup
     * @var string $content
     * @var string $contentMarkup
     * @var string $invoiceDate
     * @var double $amountTotal
     * @var string $dueDate
     * @var integer $billedMinute
     * @var double $amountTime
     * @var double $amountExpense
     * @var string $startDate
     * @var string $endDate
     * @var integer $createTime
     * @var integer $updateTime
     */

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
        return 'invoice';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('title', 'length', 'max'=>255),
            array('excerptMarkup', 'length', 'max'=>32),
            array('contentMarkup', 'length', 'max'=>32),
            array('companyId, billedMinute, createTime, updateTime', 'numerical', 'integerOnly'=>true),
            array('amountTotal, amountTime, amountExpense', 'numerical'),
            array('amountExpense, amountTime, amountTotal, billedMinute, companyId, content, contentMarkup, dueDate, endDate, invoiceDate, startDate, title', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            // one invoice has one 'company' record associated
            'company' => array(self::BELONGS_TO,'Company','companyId',
                'alias'=>'Invoice_Company'
            ),
            // one invoice has many 'expense' records associated
            'allExpense' => array(self::HAS_MANY,'Expense','invoiceId',
                'order'=>"`t`.`id` ASC",
                'alias'=>'Invoice_Expense'
            ),
            // one invoice has many 'time' records associated
            'allTime' => array(self::HAS_MANY,'Time','invoiceId',
                'order'=>"`t`.`id` ASC",
                'alias'=>'Invoice_Time'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'amountExpense'=>Yii::t('t','Amount (expense)'),
            'amountTime'=>Yii::t('t','Amount (time)'),
            'amountTotal'=>Yii::t('t','Amount (total)'),
            'billedMinute'=>Yii::t('t','Billed time'),
            'companyId'=>Yii::t('t','Company'),
            'content'=>Yii::t('t','Details'),
            'createTime'=>Yii::t('t','Record created'),
            'Date'=>Yii::t('t','Date'),
            'dueDate'=>Yii::t('t','Due date',array(1)),
            'endDate'=>Yii::t('t','End date'),
            'id'=>Yii::t('t','ID'),
            'invoiceDate'=>Yii::t('t','Invoice date'),
            'startDate'=>Yii::t('t','Start date'),
            'title'=>Yii::t('t','Note'),
            'Total'=>Yii::t('math','Total'),
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
        // avoid sql error 'incorrect date value'
        isset($_POST[__CLASS__]['invoiceDate']) && ($this->invoiceDate=MDate::formatToDb($this->invoiceDate,'date'));
        isset($_POST[__CLASS__]['dueDate']) && ($this->dueDate=MDate::formatToDb($this->dueDate,'date'));
        isset($_POST[__CLASS__]['startDate']) && ($this->startDate=MDate::formatToDb($this->startDate,'date'));
        isset($_POST[__CLASS__]['endDate']) && ($this->endDate=MDate::formatToDb($this->endDate,'date'));
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
            case 'billedMinute':
                $round=floor($this->$attribute/60);
                return $round.':'.sprintf('%02u',fmod($this->$attribute,60));
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
            default:
                return $this->$attribute;
        }
    }
}