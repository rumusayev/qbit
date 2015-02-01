<?php 
/* There is  4 types of alert messages can be used
 alert-success (green), alert-info (blue), alert-warning (orange) , alert-danger (red) 
 alert-success must be specified as id= alert-succ , alert-info as id = alert-inf.  They will be closed automatically (4 sec. see main.js)
  Please use this alerts
*/
if (isset($WARNING)) {
?>
<div class="page-alerts">
 <div class="alert alert-warning page-alert" id="alert-warn">
    <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <?php echo $WARNING ;?>
 </div>
</div> 

<?php 
}

if (isset($ERROR))
{
?>
<div class="page-alerts">
 <div class="alert alert-danger page-alert" id="alert-error">
    <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <?php echo $ERROR ;?>
 </div>
</div> 
<?php
}

if (isset($INFO)) {
?>
<div class="page-alerts">
 <div class="alert alert-info page-alert" id="alert-inf">
    <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <?php echo $INFO ;?>
 </div>
</div> 
<?php
}

if (isset($SUCCESS)) {
?>
<div class="page-alerts">
 <div class="alert alert-success page-alert" id="alert-succ">
    <button type="button" class="close"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
    <?php echo $SUCCESS ;?>
 </div>
</div> 
<?php
}
?>