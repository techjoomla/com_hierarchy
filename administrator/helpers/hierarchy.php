<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Hierarchys helper.
 *
 * @since  1.6
 */
class HierarchyHelper
{
	/**
	 * Configure the Submenu links.
	 *
	 * @param   string  $vName  The extension being used for the categories.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(JText::_('COM_HIERARCHY_TITLE_HIERARCHYS'), 'index.php?option=com_hierarchy&view=hierarchys', $vName == 'hierarchys');
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 *
	 * @since   1.6
	 * @deprecated  3.2  Use JHelperContent::getActions() instead
	 */
	public static function getActions()
	{
		$user = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_hierarchy';

		$actions = array('core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete');

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

/**
 * Check the user is Course creator or not
 * 
 * @param   user_Id  $user_Id  Id of user*
 *
 * @return  result  format
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
