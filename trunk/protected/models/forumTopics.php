<?php

class forumTopics extends _CActiveRecord
{
	/**
	 * The followings are the available columns in table 'w3_forum_topics':
	 * @var integer $id
	 * @var integer $postedBy
	 * @var integer $sectionId
	 * @var integer $replyCount
	 * @var integer $viewCount
	 * @var integer $closed
	 * @var integer $sticky
	 * @var integer $hasPoll
	 * @var integer $isActive
	 * @var integer $accessLevel
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
			array('sectionId','required','message'=>'The section id must be defined.'),
                        array('replyCount, viewCount, closed, sticky, hasPoll, isActive, accessLevel', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('postedBy, sectionId, replyCount, viewCount, closed, sticky, hasPoll, isActive, accessLevel', 'safe', 'on'=>'search'),
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
                    'user'=>array(self::BELONGS_TO, 'User','postedBy'),
                    'post'=>array(self::HAS_MANY, 'forumPosts','topicId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'postedBy' => 'Posted By',
			'sectionId' => 'Section',
			'replyCount' => 'Reply Count',
			'viewCount' => 'View Count',
			'closed' => 'Closed',
			'sticky' => 'Sticky',
			'hasPoll' => 'Has Poll',
			'isActive' => 'Is Active',
			'accessLevel' => 'Access Level',
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

		$criteria->compare('postedBy',$this->postedBy);

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