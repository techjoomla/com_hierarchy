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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

// Include dependancies
jimport('joomla.application.component.controller');
JHtml::_('bootstrap.loadcss');
JHtml::_('bootstrap.framework');

// Execute the task.
$controller = BaseController::getInstance('Hierarchy');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();

// Initialize hierarchy js
$document = Factory::getDocument();
$document->addScript(Uri::root(true) . '/media/com_hierarchy/js/hierarchy.js');
