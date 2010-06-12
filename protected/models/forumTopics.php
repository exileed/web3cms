<?php

class forumTopics extends _CActiveRecord
{
	/**
	 * The followings are the available columns in table 'w3_forum_topics':
	 * @var integer $id
	 * @var integer $userId
	 * @var integer $userName
	 * @var integer $sectionId
	 * @var integer $replyCount
	 * @var integer $viewCount
	 * @var integer $closed
	 * @var integer $sticky
	 * @var integer $hasPoll
	 * @var integer $isActive
	 * @var integer $accessLevel
	 * @var string $title
	 * @var string $summary
	 * @var integer $createTime
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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'w3_forum_topics';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sectionId, title','required','message'=>'{attribute} must be defined.'),
                        array('replyCount, viewCount, closed, sticky, hasPoll, isActive, accessLevel', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('userId, userName, sectionId, replyCount, viewCount, closed, sticky, hasPoll, isActive, accessLevel, title, summary', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
                    'section'=>array(self::BELONGS_TO, 'forumSections', 'sectionId'),
                    'user'=>array(self::BELONGS_TO, 'User','userId'),
                    'post'=>array(self::HAS_MANY, 'forumPosts','topicId','order'=>'`post`.`createTime` DESC'),
		);
	}

        protected function beforeValidate() {
            $this->userId = (!empty($this->userId) ? $this->userId : Yii::app()->user->id);
            $this->userName = (!empty($this->userName) ? $this->userName : Yii::app()->user->name);
            $this->createTime = (!empty($this->createTime) ? $this->createTime : time());
            return true;
        }

        /**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'userId' => 'Posted By',
			'sectionId' => 'Section',
			'replyCount' => 'Reply Count',
			'viewCount' => 'View Count',
			'closed' => 'Closed',
			'sticky' => 'Sticky',
			'hasPoll' => 'Has Poll',
			'isActive' => 'Is Active',
			'accessLevel' => 'Access Level',
                        'title' => 'Title',
                        'summary' => 'Summary',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('userId',$this->userId);

		$criteria->compare('sectionId',$this->sectionId);

		$criteria->compare('replyCount',$this->replyCount);

		$criteria->compare('viewCount',$this->viewCount);

		$criteria->compare('closed',$this->closed);

		$criteria->compare('sticky',$this->sticky);

		$criteria->compare('hasPoll',$this->hasPoll);

		$criteria->compare('isActive',$this->isActive);

		$criteria->compare('accessLevel',$this->accessLevel);

		return new CActiveDataProvider('w3_forum_topics', array(
			'criteria'=>$criteria,
		));
	}
}