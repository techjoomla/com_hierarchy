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

// Include dependancies
jimport('joomla.application.component.controller');
JHtml::_('bootstrap.loadcss');
JHtml::_('bootstrap.framework');

// Execute the task.
$controller = JControllerLegacy::getInstance('Hierarchy');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
