<?php

class forumPosts extends _CActiveRecord
{
	/**
	 * The followings are the available columns in table 'w3_forum_posts':
	 * @var integer $id
	 * @var integer $topicId
	 * @var integer $postedBy
	 * @var integer $sectionId
	 * @var string $title
	 * @var string $shortContent
	 * @var string $content
	 * @var integer $postTime
	 */
    
        public $isReply = false;

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
		return 'w3_forum_posts';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, content, postTime', 'required'),
			array('postTime', 'numerical', 'integerOnly'=>true),
			array('shortContent', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, topicId, postedBy, sectionId, title, shortContent, content, postTime', 'safe', 'on'=>'search'),
		);
	}

        /**
         * @return array relational rules.
         */
        public function relations() {
            // NOTE: you may need to adjust the relation name and the related
            // class name for the relations automatically generated below.
            return array(
                    'topic'=>array(self::BELONGS_TO, 'forumTopics','topicId'),
                    'user'=>array(self::BELONGS_TO, 'User','postedBy'),
                    'section'=>array(self::BELONGS_TO, 'forumSections','sectionId')
            );
        }

        protected function beforeValidate() {
            $this->postedBy = (!empty($this->postedBy) ? $this->postedBy : Yii::app()->user->id);
            $this->postTime = (!empty($this->postTime) ? $this->postTime : time());
            return true;
        }
        
        protected function afterSave() {
            if ($this->isReply == true) {
                forumTopics::model()->updateCounters(array('replyCount'=>1),array('condition'=>'`id`='.$this->topicId));
            }
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels() {
            return array(
                    'topicId' => 'Topic',
                    'postedBy' => 'Author',
                    'sectionId' => 'Section',
                    'title' => 'Title',
                    'shortContent' => 'Short Content',
                    'content' => 'Message',
                    'postTime' => 'Posted on',
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

		$criteria->compare('topicId',$this->topicId);

		$criteria->compare('postedBy',$this->postedBy);

		$criteria->compare('sectionId',$this->sectionId);

		$criteria->compare('title',$this->title,true);

		$criteria->compare('shortContent',$this->shortContent,true);

		$criteria->compare('content',$this->content,true);

		$criteria->compare('postTime',$this->postTime);

		return new CActiveDataProvider('w3_forum_posts', array(
			'criteria'=>$criteria,
		));
	}
}