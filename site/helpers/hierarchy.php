<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Hierarchys helper.
 *
 * @since  1.6
 */
class HierarchyFrontendHelper
{
	/**
	 * Get layout html
	 *
	 * @param   string  $viewName       name of view
	 * @param   string  $layout         layout of view
	 * @param   string  $searchTmpPath  site/admin template
	 * @param   string  $useViewpath    site/admin view
	 *
	 * @return  [type]                  description
	 */
	public function getViewPath($viewName, $layout = "", $searchTmpPath = 'SITE', $useViewpath = 'SITE')
	{
		$searchTmpPath = ($searchTmpPath == 'SITE') ? JPATH_SITE : JPATH_ADMINISTRATOR;
		$useViewpath   = ($useViewpath == 'SITE') ? JPATH_SITE : JPATH_ADMINISTRATOR;
		$app           = JFactory::getApplication();

		if (!empty($layout))
		{
			$layoutName = $layout . '.php';
		}
		else
		{
			$layoutName = "default.php";
		}

		// Get templates from override folder

		if ($searchTmpPath == JPATH_SITE)
		{
			$defTemplate = $this->getSiteDefaultTemplate(0);
		}
		else
		{
			$defTemplate = $this->getSiteDefaultTemplate(0);
		}

		$override = $searchTmpPath . '/templates/' . $defTemplate . '/html/com_hierarchy/' . $viewName . '/' . $layoutName;

		if (JFile::exists($override))
		{
			return $view = $override;
		}
		else
		{
			return $view = $useViewpath . '/components/com_hierarchy/views/' . $viewName . '/tmpl/' . $layoutName;
		}
	}

	/**
	 * Get sites/administrator default template
	 *
	 * @param   mixed  $client  0 for site and 1 for admin template
	 *
	 * @return  json
	 *
	 * @since   1.5
	 */
	public function getSiteDefaultTemplate($client = 0)
	{
		try
		{
			$db = JFactory::getDBO();

			// Get current status for Unset previous template from being default
			// For front end => client_id=0
			$query = $db->getQuery(true)->select('template')->from($db->quoteName('#__template_styles'))->where('client_id=' . $client)->where('home=1');
			$db->setQuery($query);

			return $db->loadResult();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return '';
		}
	}

	/**
	 * Get user Avatar
	 *
	 * @param   integer  $userid  userid
	 *
	 * @return  string  profile url
	 *
	 * @since   1.0
	 */
	public function getUserAvatar($userid)
	{
		$user        = JFactory::getUser($userid);
		$params      = JComponentHelper::getParams('com_hierarchy');
		$integration = $params->get('integration') ? $params->get('integration') : $params->get('integration');
		$uimage      = '';

		if ($integration == "2")
		{
			$user     = JFactory::getUser($userid);
			$usermail = $user->get('email');

			// Refer https://en.gravatar.com/site/implement/images/php/
			$hash     = md5(strtolower(trim($usermail)));
			$uimage   = 'http://www.gravatar.com/avatar/' . $hash . '?s=32';

			return $uimage;
		}
	}
}
