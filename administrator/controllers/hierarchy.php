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

jimport('joomla.application.component.controllerform');

/**
 * The Categories List Controller
 *
 * @since  1.6
 */
class HierarchyControllerHierarchy extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @since   1.6
	 * 
	 * @see     JController
	 */
	public function __construct()
	{
		$this->view_list = 'hierarchys';

		parent::__construct();
	}
}
