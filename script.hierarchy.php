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

	/**
	 * method to update the component
	 *
	 * @param   JInstaller  $parent  Parent
	 *
	 * @return void
	 */
	public function update($parent)
	{
		$this->installSqlFiles($parent);

		$this->fixDbOnUpdate();
	}

	/**
	 * installSqlFiles
	 *
	 * @param   JInstaller  $parent  parent
	 *
	 * @return  void
	 */
	public function installSqlFiles($parent)
	{
		$db = JFactory::getDBO();

		// Obviously you may have to change the path and name if your installation SQL file ;)
		if (method_exists($parent, 'extension_root'))
		{
			$sqlfile = $parent->getPath('extension_root') . DS . 'admin' . DS . 'sql' . DS . 'install.mysql.utf8.sql';
		}
		else
		{
			$sqlfile = $parent->getParent()->getPath('extension_root') . DS . 'sql' . DS . 'install.mysql.utf8.sql';
		}

		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);

		if ($buffer !== false)
		{
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			if (count($queries) != 0)
			{
				foreach ($queries as $query)
				{
					$query = trim($query);

					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);

						if (!$db->query())
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

							return false;
						}
					}
				}
			}
		}
	}

	/**
	 * function to make necessary changes on update
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function fixDbOnUpdate()
	{
		$db = JFactory::getDBO();

		$query = "SHOW COLUMNS FROM #__hierarchy_users";
		$db->setQuery($query);
		$res = $db->loadColumn();

		if (!in_array('client', $res))
		{
			$query = "ALTER TABLE #__hierarchy_users add column client VARCHAR(255);";
			$db->setQuery($query);
			$db->execute();
		}

		if (!in_array('client_id', $res))
		{
			$query = "ALTER TABLE #__hierarchy_users add column client_id INT(11);";
			$db->setQuery($query);
			$db->execute();
		}

		$query = "ALTER TABLE #__hierarchy_users modify subuser_id int(11);";
		$db->setQuery($query);
		$db->execute();
	}
}
