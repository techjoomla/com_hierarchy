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
