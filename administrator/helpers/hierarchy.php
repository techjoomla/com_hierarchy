<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Hierarchy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
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

	/** Get all jtext for javascript
	 *
	 * @return   void
	 *
	 * @since   1.0
	 */
	public static function getLanguageConstant()
	{
		JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
		JText::script('COM_HIERARCHY_HIERARCHY_DELETE_CONF');
		JText::script('COM_HIERARCHY_USERNAMES_DESC');
	}
}
