<?php

/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Hierarchy records.
 */
class HierarchyModelHierarchys extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				                'id', 'a.id',
                'user_id', 'a.user_id',
                'subuser_id', 'a.subuser_id',
                'created_by', 'a.created_by',

			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{


		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
		{
			foreach ($list as $name => $value)
			{
				// Extra validations
				switch ($name)
				{
					case 'fullordering':
						$orderingParts = explode(' ', $value);

						if (count($orderingParts) >= 2)
						{
							// Latest part will be considered the direction
							$fullDirection = end($orderingParts);

							if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
							{
								$this->setState('list.direction', $fullDirection);
							}

							unset($orderingParts[count($orderingParts) - 1]);

							// The rest will be the ordering
							$fullOrdering = implode(' ', $orderingParts);

							if (in_array($fullOrdering, $this->filter_fields))
							{
								$this->setState('list.ordering', $fullOrdering);
							}
						}
						else
						{
							$this->setState('list.ordering', $ordering);
							$this->setState('list.direction', $direction);
						}
						break;

					case 'ordering':
						if (!in_array($value, $this->filter_fields))
						{
							$value = $ordering;
						}
						break;

					case 'direction':
						if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
						{
							$value = $direction;
						}
						break;

					case 'limit':
						$limit = $value;
						break;

					// Just to keep the default case
					default:
						$value = $value;
						break;
				}

				$this->setState('list.' . $name, $value);
			}
		}

		// Receive & set filters
		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
		{
			foreach ($filters as $name => $value)
			{
				$this->setState('filter.' . $name, $value);
			}
		}

		$ordering = $app->input->get('filter_order');
		if (!empty($ordering))
		{
			$list             = $app->getUserState($this->context . '.list');
			$list['ordering'] = $app->input->get('filter_order');
			$app->setUserState($this->context . '.list', $list);
		}

		$orderingDirection = $app->input->get('filter_order_Dir');
		if (!empty($orderingDirection))
		{
			$list              = $app->getUserState($this->context . '.list');
			$list['direction'] = $app->input->get('filter_order_Dir');
			$app->setUserState($this->context . '.list', $list);
		}

		$list = $app->getUserState($this->context . '.list');

		

		$this->setState('list.ordering', $list['ordering']);
		$this->setState('list.direction', $list['direction']);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*'
				)
			);

		$query->from('`#__hierarchy_users` AS a');

		
		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.subuser_id LIKE '.$search.' )');
			}
		}

		

		//Filtering user_id
		$filter_user_id = $this->state->get("filter.user_id");
		if ($filter_user_id) {
			$query->where("a.user_id = '".$db->escape($filter_user_id)."'");
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	public function getItems()
	{
		$items = parent::getItems();
		

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;
		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && !$this->isValidDate($value))
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}
		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_HIERARCHY_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in an specified format (YYYY-MM-DD)
	 *
	 * @param string Contains the date to be checked
	 *
	 */
	private function isValidDate($date)
	{
		return preg_match("/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/", $date) && date_create($date);
	}
	public function store($data)
    {
		//print_r($data);die;
		$db      = $this->getDbo();

		$insertObj             = new stdClass;
		$insertObj->ticket_id  = $data->get('ticketid');
		$insertObj->id  = $data->get('rid');
		$insertObj->reason    = $data->get('reason','','string');
		$insertObj->user       = 1;
		$insertObj->modified_on = JHtml::date($input = 'now', 'Y-m-d h:i:s', false);

		if(!$db->updateObject('#__hierarchy_reschedule', $insertObj, 'id'))
		{
			return false;
		}
		else
		{
			// mail to bh and hr head.
			require_once JPATH_COMPONENT_SITE . '/helpers/hierarchy.php';
			$path = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';

			if (!class_exists('jticketingmainhelper'))
			{
				JLoader::register('jticketingmainhelper', $path);
				JLoader::load('jticketingmainhelper');
			}
			$obj = new jticketingmainhelper();
			$tid = explode('-',$data->get('ticketid'));
			
			$t_data = $obj->getorderinfo($tid[1]);

			// Get ticket user id
			$ticketData = HierarchyFrontendHelper::getTicketData($data->get('ticketid'));
			$reschedule_cost = HierarchyFrontendHelper::getRescheduleData($data->get('rid'));

			$user_id = $ticketData->user_id;
			$user_data = JFactory::getUser($user_id);

			// BH Email Id Fetach
			$user_profile_data = HierarchyFrontendHelper::getUserProfileData($user_id);
			$manager = JFactory::getUser(HierarchyFrontendHelper::getUserManager($user_id));

			$bh_user_id = $user_profile_data->business_head;
			if ($bh_user_id)
			{
				$bh_user_data = JFactory::getUser($bh_user_id);
				$bh_name = $bh_user_data->name;
				$bh_email = $bh_user_data->email;
			}

			// Hr Head 
			$params = JComponentHelper::getParams('com_hierarchy');
			$hr_email = $params->get('hrhead', '');
			$reschedule_perfix = $params->get('reschedule_perfix', '');

			$db    = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__users');
			$query->where('email = '. $db->quote($hr_email));
			$db->setQuery($query);
			$hr_user_data = $db->loadObject();
			$hr_name = $hr_user_data->name;

			// Reschedule Details
			$mainframe  = JFactory::getApplication();
			$mailfrom = $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');
			$email = $data->get('email');
			$emailSubject =  JText::_('COM_HIERARCHY_RESCHEDULES_CONFIRM_EMAIL_BHHR_SUBJECT');
			$rowBody_bh = '
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Hi  {bh_head_name},</span></p><br>

<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">You have received a rescheduling request from the following employee.</span></p>
<br>

<div dir="ltr" >
<table style="border-collapse: collapse;font-size: 12px; font-family: "Open Sans"; ">
<tbody>

<tr style="height: 0px;">
<td style="padding: 7px;">Reschedule Request ID:</td>
<td style="padding: 7px;">{reschedule_no}</td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px;">Employee Name:</td>
<td style="padding: 7px;">{user_name}</td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px;">Manager Name:</td>
<td style="padding: 7px;">{manager_name}</td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px;">Department:</td>
<td style="padding: 7px;">{department}</td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px;">Sub-Department:</td>
<td style="padding: 7px;">{sub_department}</td>
</tr>

</tbody>
</table>
</div>
<p></p>

<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Details:</span></p>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"></p>
<p></p>

<div dir="ltr">
<table style="border: 1px solid #000; border-collapse: collapse; width: 624px;">
<tbody>
<tr style="border: 1px solid #000;">
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Code</span></td>
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Name</span></td>
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Date &amp; Time</span></td>
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Location</span></td>
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Cost (<span>&#8377;</span>)</span></td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px; border: 1px solid #000;">{batch_code}</td>
<td style="padding: 7px; border: 1px solid #000;">{batch_name}</td>
<td style="padding: 7px; border: 1px solid #000;">{batch_date_time}</td>
<td style="padding: 7px; border: 1px solid #000;">{batch_location}</td>
<td style="padding: 7px; border: 1px solid #000; color: #4683EA; font-size: 15px;">{cost}</td>
</tr>
</tbody>
</table>
</div>
<br>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Reason by Employee:</span></p>
<p><span style="color: #4683EA; font-size: 15px;">{reason}</span></p>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">You may approve or reject this request from the buttons given below.</span></p>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><a href="'.JURI::root().'index.php?option=com_hierarchy&view=hierarchys&layout=confirmation&tmpl=component&ticketid='.base64_encode($data->get('reschedule_id')).'&rid='.$data->get('rid').'&ad=2&user=bh">Approve</a>&nbsp; &nbsp; &nbsp;&nbsp;<a href="'.JURI::root().'index.php?option=com_hierarchy&view=hierarchys&layout=confirmation&tmpl=component&ticketid='.base64_encode($data->get('reschedule_id')).'&rid='.$data->get('rid').'&ad=1&user=bh">Decline</a></p>
<br><br>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Regards,</span></p>
<p><span id="docs-internal-guid-0ff25d6f-ba34-3c6c-8af8-89c7ed8c26cb"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">L&amp;D</span></span></p>';
			
$rowBody_hr = '
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Hi  {hr_head_name},</span></p><br>

<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">You have received a rescheduling request from the following employee.</span></p><br>


<div dir="ltr" >
<table style="border-collapse: collapse;font-size: 12px; font-family: "Open Sans"; ">
<tbody>

<tr style="height: 0px;">
<td style="padding: 7px;">Reschedule Request ID:</td>
<td style="padding: 7px;">{reschedule_no}</td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px;">Employee Name:</td>
<td style="padding: 7px;">{user_name}</td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px;">Manager Name:</td>
<td style="padding: 7px;">{manager_name}</td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px;">Department:</td>
<td style="padding: 7px;">{department}</td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px;">Sub-Department:</td>
<td style="padding: 7px;">{sub_department}</td>
</tr>

</tbody>
</table>
</div>
<p></p>

<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Details:</span></p>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"></p>
<p></p>

<div dir="ltr">
<table style="border: 1px solid #000; border-collapse: collapse; width: 624px;">
<tbody>
<tr style="border: 1px solid #000;">
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Code</span></td>
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Name</span></td>
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Date &amp; Time</span></td>
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Batch Location</span></td>
<td style="padding: 7px; border: 1px solid #000;"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Cost (<span>&#8377;</span>)</span></td>
</tr>
<tr style="height: 0px;">
<td style="padding: 7px; border: 1px solid #000;">{batch_code}</td>
<td style="padding: 7px; border: 1px solid #000;">{batch_name}</td>
<td style="padding: 7px; border: 1px solid #000;">{batch_date_time}</td>
<td style="padding: 7px; border: 1px solid #000;">{batch_location}</td>
<td style="padding: 7px; border: 1px solid #000; color: #4683EA; font-size: 15px;">{cost}</td>
</tr>
</tbody>
</table>
</div><br>

<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Reason by Employee:</span></p>
<p><span style="color: #4683EA; font-size: 15px;">{reason}</span></p>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">You may approve or reject this request from the buttons given below.</span></p>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><a href="'.JURI::root().'index.php?option=com_hierarchy&view=hierarchys&layout=confirmation&tmpl=component&ticketid='.base64_encode($data->get('reschedule_id')).'&rid='.$data->get('rid').'&ad=2&user=hr">Approve</a>&nbsp; &nbsp; &nbsp;&nbsp;<a href="'.JURI::root().'index.php?option=com_hierarchy&view=hierarchys&layout=confirmation&tmpl=component&ticketid='.base64_encode($data->get('reschedule_id')).'&rid='.$data->get('rid').'&ad=1&user=hr">Decline</a></p>
<br><br>
<p style="line-height: 1.2; margin-top: 0pt; margin-bottom: 0pt;" dir="ltr"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">Regards,</span></p>
<p><span id="docs-internal-guid-0ff25d6f-ba34-3c6c-8af8-89c7ed8c26cb"><span style="font-size: 12px; font-family: "Open Sans"; vertical-align: baseline; white-space: pre-wrap; background-color: transparent;">L&amp;D</span></span></p>';

$amt = $reschedule_cost->cost;
setlocale(LC_MONETARY, 'en_IN');
$amt = money_format('%!i', $amt);

			$replaceArr                    = new stdClass;
			$replaceArr->user_name         = $user_data->name ;
			$replaceArr->ticket_id         = $data->get('ticketid') ;
			$replaceArr->confirmation_link = '' ;
			$replaceArr->user_email = $user_data->email;
			$replaceArr->batch_name = $t_data['eventinfo']->title ;
			$replaceArr->cost = $amt;
			$replaceArr->reason = $data->get('reason','','string') ;
			$replaceArr->bh_head_name = $bh_name ;
			$replaceArr->hr_head_name = $hr_name ;
			$replaceArr->status = '' ;
			$replaceArr->manager_name = $manager->name;
			$replaceArr->department = $user_profile_data->department;
			$replaceArr->sub_department = $user_profile_data->sub_department;
			$replaceArr->batch_code = $t_data['eventinfo']->short_description;
			$replaceArr->batch_date_time = JFactory::getDate($t_data['eventinfo']->startdate)->Format('d-m-Y H:i:s') ;
			$replaceArr->batch_location = $t_data['eventinfo']->location ;
			$replaceArr->reschedule_no = $reschedule_perfix.$data->get('rid');
			$replaceArr->status_bh = '';
			$replaceArr->reason_bh = '';
			$replaceArr->status_hr = '';
			$replaceArr->reason_hr = '';

			$emailBody_bh = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody_bh);
			$emailBody_hr = HierarchyFrontendHelper::EmailBody($replaceArr, $rowBody_hr);
			
			$cc[] = $params->get('reschedule_manager_email');
			$cc[] = $manager->email;
			
			if ($bh_email === $hr_email)
			{
				$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $hr_email, $emailSubject, $emailBody_bh, $cc);
			}
			else
			{
				$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $bh_email, $emailSubject, $emailBody_bh, $cc);
				$email = HierarchyFrontendHelper::RescheduleEmail($mailfrom, $fromname, $hr_email, $emailSubject, $emailBody_hr, $cc);
			}

			return true;
		}
	}

}
