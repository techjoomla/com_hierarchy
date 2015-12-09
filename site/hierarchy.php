<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');
JHtml::_('bootstrap.loadcss');
JHtml::_('bootstrap.framework');

// Execute the task.
$controller = JControllerLegacy::getInstance('Hierarchy');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
