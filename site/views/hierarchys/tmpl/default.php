<?php
/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once JPATH_COMPONENT . '/helpers/hierarchy.php';
JHtml::script(JURI::root().'media/jui/js/jquery.min.js');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::stylesheet(JURI::root().'components/com_hierarchy/assets/css/confirmation.css');
$id = '';
$jinput = JFactory::getApplication()->input;
$reschedule_id = $jinput->get('tid', '', 'RAW');
$rid = $jinput->get('rid', '', 'RAW');

$params = JComponentHelper::getParams('com_hierarchy');
$reschedule_perfix = $params->get('reschedule_perfix', '');
require_once JPATH_COMPONENT . '/helpers/hierarchy.php';
$ticketid = '';
if ($reschedule_id)
{
	$reschedule_id = base64_decode($reschedule_id);
	$reschedule_id = str_replace($reschedule_perfix,"",$reschedule_id);
	$ticketidcoded = HierarchyFrontendHelper::getTicketID($reschedule_id);

	$ticketid = explode('-',$ticketidcoded);
	$ticketid = $ticketid[1];

	// Check ticket in-process or completed
	$checkTicketExit = HierarchyFrontendHelper::checkTicketExit($ticketidcoded);
	if (!$checkTicketExit )
	{
		$ticketid = '';
		echo '<p><span class="alert alert-warning  middle">'.JText::sprintf("COM_HIERARCHY_RESCHEDULES_TICKET_NOT_FOUND",$ticketidcoded).'</span></p>';
		exit;
	}
	$checkTicketStatus = HierarchyFrontendHelper::checkTicketStatusLast($ticketid,$rid);

	if ($checkTicketStatus[0]->user != 0 )
	{
		if ($checkTicketStatus[0]->bh !=0 && $checkTicketStatus[0]->hr !=0)
		{
			$rescheduledata = HierarchyFrontendHelper::getTicketData($ticketidcoded, $id);

			if ($checkTicketStatus[0]->bh == 2 && $checkTicketStatus[0]->hr == 2)
			{
				$ticketid = '';
				echo '<p><span class="alert alert-warning  middle">'.JText::sprintf("COM_HIERARCHY_RESCHEDULES_CONFIRMATION_MSG",'Approve').$reschedule_perfix.$rescheduledata->id.'</span></p>';
			}
			else 
			{
				$ticketid = '';
				echo '<p><span class="alert alert-error middle">'.JText::sprintf("COM_HIERARCHY_RESCHEDULES_CONFIRMATION_MSG",'decline').$reschedule_perfix.$rescheduledata->id.'</span></p>';
			}
		}
		else
		{
			$ticketid = '';
			echo '<p><span class="alert alert-error middle">'.JText::_("COM_HIERARCHY_RESCHEDULES_ALREDY_MSG").'</span></p>';
		}
	}
}
if ($ticketid)
{
	$path = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';

	if (!class_exists('jticketingmainhelper'))
	{
		JLoader::register('jticketingmainhelper', $path);
		JLoader::load('jticketingmainhelper');
	}
	$obj = new jticketingmainhelper();
	$t_data = $obj->getorderinfo($ticketid);

	$reschedule_data = HierarchyFrontendHelper::getTicketData($ticketidcoded, $rid);

}



?>

<script>
function submitForm()
{
	var re = jQuery('#reason').val();
		if (re.length == 0)
		{
			alert('<?php echo JText::_('COM_HIERARCHY_RESCHEDULES_RESOON_REQUIRED');?>');
		}
		else
		{
			jQuery('.submit').attr('disabled','disabled');
			jQuery('#adminForm').submit();
		}
}
</script>
<?php if ($ticketid)
{
	?>
<div class="panel well">
  <h4><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRM_HEADER');?></h4>


  <div class="tabbable"> <!-- Only required for left/right tabs -->
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_DETAIL'); ?></a></li>
  </ul>
  <div class="tab-content well">
    <div class="tab-pane active" id="tab1">
     <div class="row-fluid">
			  <div class="span12">
				<div class="row-fluid">
				  <div class="span10">
					<div class="row-fluid">
					  <div class="span6"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_TICKET_ID');?></div>
					  <div class="span6"><?php echo $reschedule_perfix.$reschedule_data->id;?></div>
					</div>
				  </div>
				</div>
			  </div>
		</div>
		<!--row-->
     <div class="row-fluid">
			  <div class="span12">
				<div class="row-fluid">
				  <div class="span10">
					<div class="row-fluid">
					  <div class="span6"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_BATCH_NAME');?></div>
					  <div class="span6"><?php echo $t_data['eventinfo']->title; ?></div>
					</div>
				  </div>
				</div>
			  </div>
		</div>
		<!--row-->
     <div class="row-fluid">
			  <div class="span12">
				<div class="row-fluid">
				  <div class="span10">
					<div class="row-fluid">
					  <div class="span6"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_BATCH_CODE');?></div>
					  <div class="span6"><?php echo $t_data['eventinfo']->short_description; ?></div>
					</div>
				  </div>
				</div>
			  </div>
		</div>
		<!--row-->
     <div class="row-fluid">
			  <div class="span12">
				<div class="row-fluid">
				  <div class="span10">
					<div class="row-fluid">
					  <div class="span6"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_BATCH_LOCATION');?></div>
					  <div class="span6"><?php echo $t_data['eventinfo']->location; ?></div>
					</div>
				  </div>
				</div>
			  </div>
		</div>
		<!--row-->
     <div class="row-fluid">
			  <div class="span12">
				<div class="row-fluid">
				  <div class="span10">
					<div class="row-fluid">
					  <div class="span6"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_BATCH_DATE_TIME');?></div>
					  <div class="span6"><?php echo JFactory::getDate($t_data['eventinfo']->startdate)->Format('d-m-Y H:i:s'); ?></div>
					</div>
				  </div>
				</div>
			  </div>
		</div>
		<!--row-->
     <div class="row-fluid">
			  <div class="span12">
				<div class="row-fluid">
				  <div class="span10">
					<div class="row-fluid">
					  <div class="span6"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRMATION_COST');?></div>
					  <div class="span6"><?php
						$amount = $reschedule_data->cost;
						setlocale(LC_MONETARY, 'en_IN');
						$amount = money_format('%!i', $amount);
						echo $amount;?></div>
					</div>
				  </div>
				</div>
			  </div>
		</div>
		<!--row-->
    </div>
   
  </div>
</div>
 <!-- Only required for left/right tabs -->

 <form method="post" name="adminForm" id="adminForm">
  <fieldset>
    <label><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_REASON').'*';?></label>
    <span class="alert alert-error help-block"><?php echo JText::_('COM_HIERARCHY_RESCHEDULES_WARNING_MSG');?></span>
    <textarea cols="100" rows="4" name="reason" id="reason" ></textarea>
    <input type="hidden" name="task" value="hierarchys.save" />
	<input type="hidden" name="ticketid" value="<?php echo $ticketidcoded;?>" />
	<input type="hidden" name="reschedule_id" value="<?php echo $reschedule_perfix.$reschedule_data->id;?>" />
	<input type="hidden" name="rid" value="<?php echo $rid;?>" />

    <input type="button" class="btn btn-primary submit center" value="Submit" onclick="submitForm()"/>
  </fieldset>
</form>

</div>
 <?php } ?>
