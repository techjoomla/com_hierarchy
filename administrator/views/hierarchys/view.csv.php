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
	 * @param   STRING  $tpl  template name
	 *
	 * @return  Object|Boolean in case of success instance and failure - boolean
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$user  = JFactory::getUser();
		$userAuthorisedExport = $user->authorise('core.export', 'com_hierarchy');

		if ($userAuthorisedExport !== true || !$user->id)
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
