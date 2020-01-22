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
				$this->items = $this->get('Items');

				foreach ($this->items as $key => $item)
				{
					$this->data[$key]                = new stdClass;
					$this->data[$key]->subuserId     = $item->subuserId;
					$this->data[$key]->name          = $item->name;
					$this->data[$key]->username      = $item->username;
					$this->data[$key]->user_email    = $item->user_email;
					$this->data[$key]->id            = $item->id;
					$this->data[$key]->user_id       = $item->user_id;
					$this->data[$key]->context       = $item->context;
					$this->data[$key]->context_id    = $item->context_id;
					$this->data[$key]->created_by    = $item->created_by;
					$this->data[$key]->modified_by   = $item->modified_by;
					$this->data[$key]->created_date  = $item->created_date;
					$this->data[$key]->modified_date = $item->modified_date;
					$this->data[$key]->state         = $item->state;
					$this->data[$key]->note          = $item->note;

					// Report to username formatting
					if (!empty($item->ReportsToUserName))
					{
						$this->data[$key]->ReportsToUserName = implode(", ", $item->ReportsToUserName);
					}
					else
					{
						$this->data[$key]->ReportsToUserName = '';
					}

					// Report to formatting
					if (!empty($item->ReportsTo))
					{
						$this->data[$key]->ReportsTo = implode(", ", array_column($item->ReportsTo, 'reports_to'));
					}
					else
					{
						$this->data[$key]->ReportsTo = '';
					}
				}

				parent::display();
			}
		}
	}
}
