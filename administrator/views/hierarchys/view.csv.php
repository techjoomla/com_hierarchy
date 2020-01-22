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

// Import CSV library view
jimport('techjoomla.view.csv');

/**
 * Hierarchy View class that extends TjExportCsv class
 *
 * @since  0.0.1
 */
class HierarchyViewHierarchys extends TjExportCsv
{
	/**
	 * Display the Hierarchy view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$user  = JFactory::getUser();
		$userAuthorisedExport = $user->authorise('core.create', 'com_hierarchy');

		if ($userAuthorisedExport != 1 || !$user)
		{
			// Redirect to the list screen.
			$redirect = JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false);
			JFactory::getApplication()->redirect($redirect, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}
		else
		{
			if ($input->get('task') == 'download')
			{
				$fileName = $input->get('file_name');
				$this->download($fileName);
				JFactory::getApplication()->close();
			}
			else
			{
				parent::display();
			}
		}
	}
}
