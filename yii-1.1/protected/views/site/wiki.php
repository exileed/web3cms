<?php MParams::setPageLabel(Yii::t('page','Application wiki')); ?>
<?php /*MUserFlash::setSidebarInfo(Yii::t('hint','We will try to explain this application here.'));*/ ?>
<?php $this->widget('application.components.WContentHeader',array(
    'breadcrumbs'=>array(
        array(
            'url'=>array($this->action->id),
            'active'=>true,
        ),
    ),
)); ?>

<div class="w3-widget">
We will try to explain this application here.

<h2>Member roles</h2>

<h3>All roles</h3>
Can view/edit his(her) own profile.<br/>
<br/>

<h3>Client</h3>
Can view his own company.<br/>
Can view projects associated with his companies.<br/>
Can view tasks associated with his companies.<br/>
Can view time records associated with his companies.<br/>
Can view company payments associated with his companies.<br/>
Can view expenses associated with his companies.<br/>
Can view invoices associated with his companies.<br/>
<i>TODO: Hide consultant/leader from the client's view.</i><br/>
<br/>

<h3>Consultant</h3>
Can view his tasks.<br/>
Can view unassigned tasks.<br/>
Can view his time records.<br/>
Can view projects associated with his tasks.<br/>
<i>TODO: Can add a time record.</i><br/>
<br/>

<h3>Manager</h3>
Can view all members.<br/>
Can view all companies.<br/>
Can view all projects.<br/>
Can view all tasks.<br/>
Can view all time records.<br/>
Can view all company payments.<br/>
Can view all expenses.<br/>
Can view all invoices.<br/>
Can add a time record.<br/>
Can edit/delete any time record that is not associated with an invoice yet.<br/>
<br/>

<h3>Administrator</h3>
Can do anything what manager does.<br/>
Can edit any member.<br/>
Can edit/delete any time record (even if it is associated with an invoice).<br/>
Can create invoices.<br/>
Can do anything!<br/>
<br/>


<!-- <h2>Notes</h2>
Customers may not edit time records.<br/>
Consultant can Edit hoursBilled on their own rows where invoice_id=0.<br/>
Manager can Edit hoursBilled on any row where invoice_id=0.<br/>
Admin can Edit hoursBilled & hoursSpent on any row.<br/>
<br/> -->


<!-- <h2>TODO:</h2> -->
<!-- Grid of time records: if consultant, do not display 'Consultant' column. -->
<!-- Grid of time records: if not manager and not administrator, make 'Actions' column narrowly. -->

</div><!-- w3-widget -->
