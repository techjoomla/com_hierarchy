<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Hierarchys helper.
 *
 * @since  1.6
 */
class HierarchyFrontendHelper
{
	/**
	 * Check the Ticket
	 *
	 * @param   tid  $tid  uid of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function checkTicketJTicket($tid)
	{
		if ($tid)
		{
			$db    = JFactory::getDBO();
			echo $query = "SELECT CONCAT_WS('-', order_id, id) AS ticketid FROM `#__jticketing_order`
				as o WHERE CONCAT_WS('-', order_id, id) = '" . $tid . "'";
			$db->setQuery($query);

			return $managerId = $db->loadResult();
		}
	}

	/**
	 * Check the Ticket
	 *
	 * @param   tid  $tid  uid of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function checkTicketExit($tid)
	{
		if ($tid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT * FROM  `#__hierarchy_reschedule` WHERE  `ticket_id` = '" . $tid . "'";
			$db->setQuery($query);

			return $managerId = $db->loadObjectlist();
		}
	}

	/**
	 * Get the TicketStatus
	 *
	 * @param   tid  $tid  uid of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function checkTicketStatus($tid)
	{
		if ($tid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT * FROM  `#__hierarchy_reschedule` WHERE  `ticket_id` LIKE '%-" . $tid . "-%' ";
			$db->setQuery($query);

			return $managerId = $db->loadObjectlist();
		}
	}

	/**
	 * Get the TicketStatus
	 *
	 * @param   tid  $tid  uid of user
	 * @param   rid  $rid  rid of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function checkTicketStatusLast($tid,$rid)
	{
		if ($tid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT * FROM  `#__hierarchy_reschedule` WHERE  `ticket_id` LIKE '%-" . $tid . "-%' AND id= '" . $rid . "'";
			$db->setQuery($query);

			return $managerId = $db->loadObjectlist();
		}
	}

	/**
	 * Get the UserManager
	 *
	 * @param   uid  $uid  uid of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function getUserManager($uid)
	{
		if ($uid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT user_id FROM  `#__hierarchy_users` WHERE  `subuser_id` = " . $uid;
			$db->setQuery($query);

			return $managerId = $db->loadResult();
		}
	}

	/**
	 * Get the admin
	 *
	 * @param   uid  $uid  uid of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function getUserProfileData($uid)
	{
		if ($uid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT * FROM  `#__tjlms_user_xref` WHERE  `user_id` = " . $uid;
			$db->setQuery($query);

			return $managerId = $db->loadObject();
		}
	}

	/**
	 * Get the admin
	 *
	 * @param   uid       $uid       uid of user
	 * 
	 * @param   ticketid  $ticketid  ticketid of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function getTicketRescheduleData($uid, $ticketid)
	{
		if ($uid && $ticketid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT * FROM  `#__hierarchy_reschedule` WHERE  `ticket_id` = '" . $ticketid . "' 
					and user_id = '" . $uid . "' order by id  DESC LIMIT 0 , 10 ";
			$db->setQuery($query);

			return $managerId = $db->loadObjectlist();
		}
	}

	/**
	 * Get the admin
	 *
	 * @param   ticketid  $reschedule_id  reschedule_id of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function getRescheduleData($reschedule_id)
	{
		if ($reschedule_id)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT * FROM  `#__hierarchy_reschedule` WHERE  `id` = '" . $reschedule_id . "'";
			$db->setQuery($query);

			return $db->loadObject();
		}
	}

	/**
	 * Get the admin
	 *
	 * @param   ticketid  $ticketid  Id of user
	 * @param   ticketid  $rid       rid of user
	 * 
	 * @return  result format
	 *
	 * @since 1.0
	 */
	public static function getTicketData($ticketid, $rid =null)
	{
		if ($ticketid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT * FROM  `#__hierarchy_reschedule` WHERE  `ticket_id`='" . $ticketid . "'";

			if ($rid)
			{
				$query . = "AND `id` = '" . $rid . "'";
			}

			$db->setQuery($query);

			return $managerId = $db->loadObject();
		}
	}

	/**
	 * Get the admin
	 *
	 * @param   ticketid  $ticketid  Id of user
	 *
	 * @return result format
	 *
	 * @since 1.0
	 */
	public static function getUserId($ticketid)
	{
		if ($ticketid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT user_id FROM  `#__hierarchy_reschedule` WHERE  `ticket_id` = '" . $ticketid . "'";
			$db->setQuery($query);

			return $managerId = $db->loadResult();
		}
	}

	/**
	 * Get the admin
	 *
	 * @param   reschedule_id  $reschedule_id  Id of user
	 *
	 * @return result format
	 *
	 * @since 1.0
	 */
	public static function getTicketID($reschedule_id)
	{
		if ($reschedule_id)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT ticket_id FROM  `#__hierarchy_reschedule` WHERE  `id` = '" . $reschedule_id . "'";
			$db->setQuery($query);

			return $managerId = $db->loadResult();
		}
	}

	/**
	 * Get the admin
	 *
	 * @param   groupid  $groupid  Id of user
	 *
	 * @return result format
	 *
	 * @since 1.0
	 */
	public static function getTrainingAdmin($groupid)
	{
		if ($groupid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT user_id FROM `#__user_usergroup_map` WHERE  `group_id` = '" . $groupid . "'";
			$db->setQuery($query);

			return $managerId = $db->loadColumn();
		}
	}

/**
	* Email sendmail function.
	*
	* @param   string  $mailfrom      from mail address.
	* @param   string  $fromname      from user name.
	* @param   string  $email         to mail address.
	* @param   string  $emailSubject  subject of mail.
	* @param   string  $emailBody     body of mail.
	* 
	* @param   string  $cc            cc of mail.
	*
	* @return  boolan
	* 
	* @since   1.0
	*/
	static public function RescheduleEmail($mailfrom, $fromname, $email, $emailSubject, $emailBody,$cc = null)
	{
		$params = JComponentHelper::getParams('com_hierarchy');
		$fromname = $params->get('reschedule_email_from', '', 'STRING');
		$replyTo = $params->get('reschedule_email_Reply_To', '', 'RAW');
		$return = JFactory::getMailer()->
		sendMail($mailfrom, $fromname, $email, $emailSubject, $emailBody, $mode = 1, $cc, $bcc = null, $attachment = null, $replyTo, $replyToName = null);

		return $return;
	}

	/**
	 * Email Body function
	 *
	 * @param   array    $replaceData  replace array to search array.
	 * @param   boolean  $email_body   email body format.
	 * 
	 * @return  Body content for mail
	 * 
	 * @since   1.0
	 */
	static public function EmailBody($replaceData, $email_body)
	{
		$search = array(
			"{user_name}",
			"{ticket_id}",
			"{confirmation_link}",
			"{user_email}",
			"{batch_name}",
			"{cost}",
			"{reason}",
			"{bh_head_name}",
			"{hr_head_name}",
			"{status}",
			"{manager_name}",
			"{department}",
			"{sub_department}",
			"{batch_code}",
			"{batch_date_time}",
			"{batch_location}",
			"{reschedule_no}",
			"{status_bh}",
			"{reason_bh}",
			"{status_hr}",
			"{reason_hr}"
		);

		$replace = array(
			$replaceData->user_name,
			$replaceData->ticket_id,
			$replaceData->confirmation_link,
			$replaceData->user_email,
			$replaceData->batch_name,
			$replaceData->cost,
			$replaceData->reason,
			$replaceData->bh_head_name,
			$replaceData->hr_head_name,
			$replaceData->status,
			$replaceData->manager_name,
			$replaceData->department,
			$replaceData->sub_department,
			$replaceData->batch_code,
			$replaceData->batch_date_time,
			$replaceData->batch_location,
			$replaceData->reschedule_no,
			$replaceData->status_bh,
			$replaceData->reason_bh,
			$replaceData->status_hr,
			$replaceData->reason_hr
		);

		$EmailBody = str_replace($search, $replace, $email_body);

		return $EmailBody;
	}

	/**
	 * TrainingAdmin function
	 *
	 * @param   int  $uid  user Id.
	 * 
	 * @return  manager id
	 * 
	 * @since   1.0
	 */
	public static function getTrainingAdminExit($uid = null)
	{
		$params = JComponentHelper::getParams('com_hierarchy');
		$groupid = $params->get('groupid', '', 'INT');
		$user = JFactory::getUser();
		$uid = $user->id;

		if ($uid)
		{
			$db    = JFactory::getDBO();
			$query = "SELECT group_id FROM  `#__user_usergroup_map` WHERE `user_id` = '" . $uid . "' AND group_id = '" . $groupid . "'";
			$db->setQuery($query);

			return $managerId = $db->loadResult();
		}
	}

	/**
	 * Check the user is Course creator or not
	 *
	 * @param   user_Id  $user_Id  Id of user
	 *
	 * @return result format
	 *
	 * @since 1.0
	 */
	public function checkManager($user_Id = null)
	{
		if (empty($user_Id))
		{
			$user_Id = JFactory::getUser()->id;
		}

		if ($user_Id)
		{
			// Checking if the user is having subuser in hierarchy
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hierarchy/models', 'hierarchys');
			$HierarchyModelHierarchys = JModelLegacy::getInstance('Hierarchys', 'HierarchyModel');
			$HierarchyModelHierarchys->setState('filter.user_id', $user_Id);
			$isManager = $HierarchyModelHierarchys->getItems();

			return $isManager;
		}

		return false;
	}
}
