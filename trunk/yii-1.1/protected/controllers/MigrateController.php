<?php

class MigrateController extends _CController
{
    /**
     * Migrate from old project management system.
     */
    public function actionIndex()
    {
        // check rights
        if(!Yii::app()->user->checkAccess(User::ADMINISTRATOR))
            throw new CHttpException(403,Yii::t('Yii','You are not authorized to perform this action.'));
        // models to migrate
        $migrate=array(
            'User'=>false,
            'Company'=>false,
            'CompanyPayment'=>false,
            'Project'=>false,
            'Task'=>false,
            'Time'=>true,
            'Invoice'=>false,
            'Expense'=>false,
        );
        // start
        $message='';
        // we won't migrate unless form is submitted
        if(Yii::app()->request->isPostRequest)
        {
            // default criteria
            $findAllCriteria=new CDbCriteria;
            $findAllCriteria->order="`id` ASC";
            if($migrate['User'])
            {
                // user
                $mUsers=MUser::model()->findAll($findAllCriteria);
                if(is_array($mUsers))
                {
                    $i=$j=$c=0;
                    $accessType=array('customer'=>'client','consultant'=>'consultant','manager'=>'manager','admin'=>'administrator');
                    $accessLevel=array('customer'=>2,'consultant'=>3,'manager'=>4,'admin'=>5);
                    foreach($mUsers as $oldModel)
                    {
                        if(($model=User::model()->findByPk($oldModel->id))!==null)
                            $model->delete();
                        // old model validation
                        if(User::model()->findByAttributes(array('email'=>$oldModel->email)))
                            $oldModel->email=rand(10,99).$oldModel->email;
                        $closeTime=strtotime($oldModel->close_date);
                        $isActive=empty($oldModel->close_date) || $oldModel->close_date==='0000-00-00' || $closeTime===false;
                        // new model
                        $model=new User('migrate');
                        $model->username=$oldModel->email;
                        $model->password=md5($oldModel->password);
                        $model->email=$oldModel->email;
                        $model->screenName=$oldModel->name;
                        $model->accessType=isset($accessType[$oldModel->class]) ? $accessType[$oldModel->class] : 'member';
                        $model->accessLevel=isset($accessLevel[$oldModel->class]) ? $accessLevel[$oldModel->class] : 1;
                        $model->isActive=$isActive ? '1' : '0';
                        $model->createTime=strtotime($oldModel->last);
                        $model->id=$oldModel->id;
                        if($model->save())
                        {
                            $i++;
                            if(($userDetails=UserDetails::model()->findByPk($model->id))!==null)
                                $userDetails->delete();
                            $userDetails=new UserDetails('migrate');
                            $userDetails->emailConfirmationKey=md5(uniqid(rand(),true));
                            $userDetails->initials=$oldModel->inits;
                            $userDetails->occupation=$oldModel->title;
                            $userDetails->deactivationTime=$isActive ? null : $closeTime;
                            $userDetails->administratorNote='[from migration]';
                            $userDetails->userId=$model->id;
                            $userDetails->save();
                            // relation between user and company
                            if($oldModel->customer_id>=1)
                            {
                                $user2Company=new User2Company('migrate');
                                $user2Company->userId=$model->id;
                                $user2Company->companyId=$oldModel->customer_id;
                                $user2Company->position='owner';//$oldModel->title
                                if($user2Company->save())
                                    $c++;
                            }
                        }
                        $j++;
                    }
                    $message.=$i.' of '.$j.' users'.($i===$c?'':' with '.$c.' company (relations)').' have been migrated.<br/>';
                }
            }
            if($migrate['Company'])
            {
                // company
                $mCustomers=MCustomer::model()->findAll($findAllCriteria);
                if(is_array($mCustomers))
                {
                    $i=$j=$l=0;
                    foreach($mCustomers as $oldModel)
                    {
                        if(($model=Company::model()->findByPk($oldModel->id))!==null)
                            $model->delete();
                        $closeTime=strtotime($oldModel->close_date);
                        $isActive=empty($oldModel->close_date) || $oldModel->close_date==='0000-00-00' || $closeTime===false;
                        $model=new Company('migrate');
                        $model->title=$oldModel->name;
                        $model->titleAbbr=$oldModel->inits;
                        $model->contactName=$oldModel->contact;
                        $model->contactEmail=$oldModel->contact_email;
                        $model->content='[from migration]';
                        $model->contentMarkup='text';
                        $model->invoiceDueDay=$oldModel->terms_days;
                        $model->isActive=$isActive ? '1' : '0';
                        $model->deactivationTime=$isActive ? null : $closeTime;
                        $model->createTime=strtotime($oldModel->last);
                        $model->id=$oldModel->id;
                        if($model->save())
                        {
                            $i++;
                            // associated location
                            $location=new Location('migrate');
                            $location->address1=$oldModel->addr;
                            $location->address2=$oldModel->addr2;
                            $location->city=$oldModel->city;
                            $location->state=$oldModel->state;
                            $location->zipcode=$oldModel->zip;
                            $location->content='[from migration]';
                            $location->contentMarkup='text';
                            $location->createTime=strtotime($oldModel->last);
                            if($location->save())
                            {
                                // relation between company and location
                                $location2Record=new Location2Record('migrate');
                                $location2Record->locationId=$location->id;
                                $location2Record->record=get_class($model);
                                $location2Record->recordId=$model->id;
                                if($location2Record->save())
                                    $l++;
                            }
                        }
                        $j++;
                    }
                    $message.=$i.' of '.$j.' companies'.($i===$l?'':' with '.$l.' locations').' have been migrated.<br/>';
                }
            }
            if($migrate['CompanyPayment'])
            {
                // company payment
                $mCustomerPayments=MCustomerPayment::model()->findAll($findAllCriteria);
                if(is_array($mCustomerPayments))
                {
                    $i=$j=0;
                    $paymentMethod=array('cash'=>'cash','check'=>'check','credit card'=>'creditCard');
                    foreach($mCustomerPayments as $oldModel)
                    {
                        if(($model=CompanyPayment::model()->findByPk($oldModel->id))!==null)
                            $model->delete();
                        $model=new CompanyPayment('migrate');
                        $model->companyId=$oldModel->id;
                        $model->paymentDate=MDate::formatToDb($oldModel->payment_date,'date');
                        $model->amount=$oldModel->amount;
                        $model->paymentMethod=$paymentMethod[$oldModel->payment_method];
                        $model->paymentNumber=$oldModel->payment_number;
                        $model->content=$oldModel->note."\n".'[from migration]';
                        $model->contentMarkup='text';
                        $model->id=$oldModel->id;
                        if($model->save())
                            $i++;
                        $j++;
                    }
                    $message.=$i.' of '.$j.' company payments have been migrated.<br/>';
                }
            }
            if($migrate['Project'])
            {
                // project
                $mProjects=MProject::model()->findAll($findAllCriteria);
                if(is_array($mProjects))
                {
                    $i=$j=$c=$u=0;
                    foreach($mProjects as $oldModel)
                    {
                        if(($model=Project::model()->findByPk($oldModel->id))!==null)
                            $model->delete();
                        $openDateNotSet=empty($oldModel->open_date) || $oldModel->open_date==='0000-00-00' || strtotime($oldModel->open_date)===false;
                        $closeDateNotSet=empty($oldModel->close_date) || $oldModel->close_date==='0000-00-00' || strtotime($oldModel->close_date)===false;
                        $model=new Project('migrate');
                        $model->title=$oldModel->name;
                        $model->content=$oldModel->description."\n".'[from migration]';
                        $model->contentMarkup='text';
                        $model->hourlyRate=$oldModel->rate;
                        $model->openDate=$openDateNotSet ? null : MDate::formatToDb($oldModel->open_date,'date');
                        $model->closeDate=$closeDateNotSet ? null : MDate::formatToDb($oldModel->close_date,'date');
                        $model->createTime=strtotime($oldModel->last);
                        $model->id=$oldModel->id;
                        if($model->save())
                        {
                            $i++;
                            // relation between project and company
                            if($oldModel->customer_id>=1)
                            {
                                $company2Project=new Company2Project('migrate');
                                $company2Project->companyId=$oldModel->customer_id;
                                $company2Project->projectId=$model->id;
                                if($company2Project->save())
                                    $c++;
                            }
                            // relation between project and manager
                            if($oldModel->manager_id>=1)
                            {
                                $user2Project=new User2Project('migrate');
                                $user2Project->userId=$oldModel->manager_id;
                                $user2Project->projectId=$model->id;
                                $user2Project->role='manager';
                                if($user2Project->save())
                                    $u++;
                            }
                        }
                        $j++;
                    }
                    $message.=$i.' of '.$j.' projects'.($i===$c?'':' with '.$c.' company (relations)').($i===$u?'':' with '.$u.' manager (relations)').' have been migrated.<br/>';
                }
            }
            if($migrate['Task'])
            {
                // task
                $mTasks=MTask::model()->findAll($findAllCriteria);
                if(is_array($mTasks))
                {
                    $i=$j=$u=$m=0;
                    $priority=array('A'=>2,'B'=>3,'C'=>4,''=>3);
                    $status=array(''=>'completed','Open'=>'completed','0'=>'notStarted','New'=>'notStarted','Done'=>'completed','In Progress'=>'inProgress','Ready to Test'=>'readyToTest');
                    foreach($mTasks as $oldModel)
                    {
                        if(($model=Task::model()->findByPk($oldModel->id))!==null)
                            $model->delete();
                        $hourlyRate=null;
                        if($oldModel->project_id>=1 && ($project=Project::model()->findByPk($oldModel->project_id))!==null)
                            $hourlyRate=$project->hourlyRate;
                        $dueDateNotSet=empty($oldModel->due_date) || $oldModel->due_date==='0000-00-00' || strtotime($oldModel->due_date)===false;
                        $openDateNotSet=empty($oldModel->open_date) || $oldModel->open_date==='0000-00-00' || strtotime($oldModel->open_date)===false;
                        $closeDateNotSet=empty($oldModel->close_date) || $oldModel->close_date==='0000-00-00' || strtotime($oldModel->close_date)===false;
                        $model=new Task('migrate');
                        $model->title=$oldModel->name;
                        $model->content=$oldModel->description."\n".'[from migration]';
                        $model->contentMarkup='text';
                        $model->companyId=$oldModel->customer_id;
                        $model->projectId=$oldModel->project_id;
                        $model->estimateMinute=(int)$oldModel->hours_estimate*60;
                        $model->dueDate=$dueDateNotSet ? null : MDate::formatToDb($oldModel->due_date,'date');
                        $model->priority=$priority[$oldModel->priority];
                        $model->openDate=$openDateNotSet ? null : MDate::formatToDb($oldModel->open_date,'date');
                        $model->closeDate=$closeDateNotSet ? null : MDate::formatToDb($oldModel->close_date,'date');
                        $model->status=$status[$oldModel->task_status];
                        $model->report=$oldModel->work_report;
                        $model->reportMarkup='text';
                        $model->hourlyRate=$hourlyRate;
                        $model->isConfirmed='1';
                        $model->confirmationTime=strtotime($oldModel->last);
                        $model->createTime=strtotime($oldModel->last);
                        $model->id=$oldModel->id;
                        if($model->save())
                        {
                            $i++;
                            // relation between task and consultant
                            if($oldModel->leader_id>=1)
                            {
                                $user2Task=new User2Task('migrate');
                                $user2Task->userId=$oldModel->leader_id;
                                $user2Task->taskId=$model->id;
                                $user2Task->role=User2Task::CONSULTANT;
                                if($user2Task->save())
                                    $u++;
                            }
                            // relation between task and manager
                            if($model->projectId>=1)
                            {
                                $criteria=new CDbCriteria;
                                $criteria->order="`".User2Project::model()->tableName()."`.`userPriority` ASC";
                                $criteria->order.=",`".User2Project::model()->tableName()."`.`id` ASC";
                                if(($user2Project=User2Project::model()->findByAttributes(array('projectId'=>$model->projectId,'role'=>'manager'),$criteria))!==null)
                                {
                                    $user2Task=new User2Task('migrate');
                                    $user2Task->userId=$user2Project->userId;
                                    $user2Task->taskId=$model->id;
                                    $user2Task->role='manager';
                                    if($user2Task->save())
                                        $m++;
                                }
                            }
                        }
                        $j++;
                    }
                    $message.=$i.' of '.$j.' tasks'.($i===$u?'':' with '.$u.' consultant (relations)').($i===$m?'':' with '.$m.' manager (relations)').' have been migrated.<br/>';
                }
            }
            if($migrate['Time'])
            {
                // time
                $mTime=MTime::model()->findAll($findAllCriteria);
                if(is_array($mTime))
                {
                    $i=$j=$t=0;
                    foreach($mTime as $oldModel)
                    {
                        if(($model=Time::model()->findByPk($oldModel->id))!==null)
                            $model->delete();
                        $taskId=$oldModel->task_id;
                        if(empty($taskId) && $oldModel->project_id>=1)
                        {
                            $criteria=new CDbCriteria;
                            $criteria->order="`".Task::model()->tableName()."`.`id` ASC";
                            if(($task=Task::model()->findByAttributes(array('projectId'=>$oldModel->project_id),$criteria))!==null)
                                $taskId=$task->id;
                            else
                            {
                                // auto-generate a task
                                $companyId=0;
                                $criteria=new CDbCriteria;
                                $criteria->order="`".Company2Project::model()->tableName()."`.`companyPriority` ASC";
                                $criteria->order.=", `".Company2Project::model()->tableName()."`.`id` ASC";
                                if(($company2Project=Company2Project::model()->findByAttributes(array('projectId'=>$oldModel->project_id),$criteria))!==null)
                                    $companyId=$company2Project->companyId;
                                $hourlyRate=null;
                                if(($project=Project::model()->findByPk($oldModel->project_id))!==null)
                                    $hourlyRate=$project->hourlyRate;
                                $task=new Task('migrate');
                                $task->projectId=$oldModel->project_id;
                                $task->companyId=$companyId;
                                $task->title='[Auto Generated]';
                                $task->status='completed';
                                $task->dueDate=MDate::formatToDb($project===null?1234567890:$project->createTime,'date');
                                $task->openDate=MDate::formatToDb($project===null?1234567890:$project->createTime,'date');
                                $task->closeDate=MDate::formatToDb($project===null?1234567890:$project->createTime,'date');
                                $task->hourlyRate=$hourlyRate;
                                $task->isConfirmed=1;
                                $task->confirmationTime=$project===null?1234567890:$project->createTime;
                                if($task->save())
                                {
                                    $t++;
                                    $taskId=$task->id;
                                    // assigned consultant
                                    if($oldModel->user_id>=1)
                                    {
                                        $consultant2Task=new User2Task('migrate');
                                        $consultant2Task->userId=$oldModel->user_id;
                                        $consultant2Task->taskId=$task->id;
                                        $consultant2Task->role=User2Task::CONSULTANT;
                                        $consultant2Task->save();
                                    }
                                    // assigned manager
                                    $criteria=new CDbCriteria;
                                    $criteria->order="`".User2Project::model()->tableName()."`.`userPriority` ASC";
                                    $criteria->order.=", `".User2Project::model()->tableName()."`.`id` ASC";
                                    if(($user2Project=User2Project::model()->findByAttributes(array('projectId'=>$oldModel->project_id,'role'=>'manager'),$criteria))!==null)
                                    {
                                        $manager2Task=new User2Task('migrate');
                                        $manager2Task->userId=$user2Project->userId;
                                        $manager2Task->taskId=$task->id;
                                        $manager2Task->role=User2Task::MANAGER;
                                        $manager2Task->save();
                                    }
                                }
                            }
                        }
                        $managerId=null;
                        if(!empty($taskId))
                        {
                            $criteria=new CDbCriteria;
                            $criteria->order="`".User2Task::model()->tableName()."`.`userPriority` ASC";
                            $criteria->order.=",`".User2Task::model()->tableName()."`.`id` ASC";
                            if(($user2Task=User2Task::model()->findByAttributes(array('taskId'=>$taskId,'role'=>'manager'),$criteria))!==null)
                                $managerId=$user2Task->userId;
                        }
                        $timeDateNotSet=empty($oldModel->time_date) || $oldModel->time_date==='0000-00-00' || strtotime($oldModel->time_date)===false;
                        $model=new Time('migrate');
                        $model->consultantId=$oldModel->user_id;
                        $model->taskId=$taskId;
                        $model->spentMinute=(int)$oldModel->hours_spent*60;
                        $model->timeDate=$timeDateNotSet ? null : MDate::formatToDb($oldModel->time_date,'date');
                        $model->title=$oldModel->note;
                        $model->content=$oldModel->details."\n".'[from migration]';
                        $model->contentMarkup='text';
                        $model->managerId=$managerId;
                        $model->billedMinute=(int)$oldModel->hours_billed*60;
                        $model->invoiceId=$oldModel->invoice_id;
                        $model->invoiceAmount=$oldModel->invoice_amount;
                        $model->isConfirmed='1';
                        $model->confirmationTime=strtotime($oldModel->last);
                        $model->createTime=strtotime($oldModel->last);
                        $model->id=$oldModel->id;
                        if($model->save())
                            $i++;
                        $j++;
                    }
                    $message.=$i.' of '.$j.' time records'.($i===$t?'':' with '.$t.' tasks').' have been migrated.<br/>';
                }
            }
            if($migrate['Invoice'])
            {
                // invoice
                $mInvoices=MInvoice::model()->findAll($findAllCriteria);
                if(is_array($mInvoices))
                {
                    $i=$j=0;
                    foreach($mInvoices as $oldModel)
                    {
                        if(($model=Invoice::model()->findByPk($oldModel->id))!==null)
                            $model->delete();
                        $invoiceDateNotSet=empty($oldModel->invoice_date) || $oldModel->invoice_date==='0000-00-00' || strtotime($oldModel->invoice_date)===false;
                        $startDateNotSet=empty($oldModel->start_date) || $oldModel->start_date==='0000-00-00' || strtotime($oldModel->start_date)===false;
                        $endDateNotSet=empty($oldModel->end_date) || $oldModel->end_date==='0000-00-00' || strtotime($oldModel->end_date)===false;
                        $dueDateNotSet=empty($oldModel->due_date) || $oldModel->due_date==='0000-00-00' || strtotime($oldModel->due_date)===false;
                        $model=new Invoice('migrate');
                        $model->invoiceDate=$invoiceDateNotSet ? null : MDate::formatToDb($oldModel->invoice_date,'date');
                        $model->companyId=$oldModel->customer_id;
                        $model->billedMinute=(int)$oldModel->hours_billed*60;
                        $model->amountTotal=number_format($oldModel->total,2,'.','');
                        $model->startDate=$startDateNotSet ? null : MDate::formatToDb($oldModel->start_date,'date');
                        $model->endDate=$endDateNotSet ? null : MDate::formatToDb($oldModel->end_date,'date');
                        $model->dueDate=$dueDateNotSet ? null : MDate::formatToDb($oldModel->due_date,'date');
                        $model->amountTime=number_format($oldModel->total,2,'.','');
                        $model->amountExpense=0;
                        $model->content='[from migration]';
                        $model->contentMarkup='text';
                        $model->createTime=$invoiceDateNotSet ? null : strtotime($oldModel->invoice_date);
                        $model->id=$oldModel->id;
                        if($model->save())
                            $i++;
                        $j++;
                    }
                    $message.=$i.' of '.$j.' invoices have been migrated.<br/>';
                }
            }
            if($migrate['Expense'])
            {
                // expense
                $mExpenses=MExpense::model()->findAll($findAllCriteria);
                if(is_array($mExpenses))
                {
                    $i=$j=0;
                    $billToCompany=array('Yes'=>'1','No'=>'0');
                    foreach($mExpenses as $oldModel)
                    {
                        if(($model=Expense::model()->findByPk($oldModel->id))!==null)
                            $model->delete();
                        $expenseDateNotSet=empty($oldModel->expense_date) || $oldModel->expense_date==='0000-00-00' || strtotime($oldModel->expense_date)===false;
                        $model=new Expense('migrate');
                        $model->managerId=$oldModel->user_id;
                        $model->companyId=$oldModel->customer_id;
                        $model->projectId=$oldModel->project_id;
                        $model->invoiceId=$oldModel->invoice_id;
                        $model->expenseDate=$expenseDateNotSet ? null : MDate::formatToDb($oldModel->expense_date,'date');
                        $model->amount=$oldModel->amount;
                        $model->billToCompany=$billToCompany[$oldModel->bill_to_customer];
                        $model->content=$oldModel->note."\n".'[from migration]';
                        $model->contentMarkup='text';
                        $model->createTime=strtotime($oldModel->last);
                        $model->id=$oldModel->id;
                        if($model->save())
                            $i++;
                        $j++;
                    }
                    $message.=$i.' of '.$j.' expenses have been migrated.<br/>';
                }
            }
            // last message line
            $message.='done';
        }
        $this->render($this->action->id,array('message'=>$message));
    }

    /**
     * Change pk of some active record.
     * @param string table name
     * @param integer row old pk
     * @param integer row new pk
     * @return integer number of rows affected by the execution.
     */
    public function arChangePk($table,$oldId,$newId)
    {
        $command=Yii::app()->db->createCommand("UPDATE `$table` SET id=:newId WHERE id=:oldId");
        $command->bindValue(':oldId',$oldId);
        $command->bindValue(':newId',$newId);
        $updatedNum=$command->execute();
        if($updatedNum && $newId>$oldId)
        {
            // we might need to fix table's auto_increment value
            $command=Yii::app()->db->createCommand("ALTER TABLE `$table` AUTO_INCREMENT = 1");
            $command->execute();
        }
        return true;
    }
}