<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Hierarchy Installer
 *
 * @since  1.0.0
 */
class Com_HierarchyInstallerScript
{
	/** @var array The list of extra modules and plugins to install */
	private $oldversion = "";

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   JInstaller  $type    type
	 * @param   JInstaller  $parent  parent
	 *
	 * @return void
	 */
	public function preflight($type, $parent)
	{
	}

	/**
	 * method to install the component
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  void
	 */
	public function install($parent)
	{
	}
}
