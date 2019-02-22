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
		parent::display();
	}
}
