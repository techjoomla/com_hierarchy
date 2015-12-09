<?php
/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Hierarchys list controller class.
 */
class HierarchyControllerHierarchys extends HierarchyController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Hierarchys', $prefix = 'HierarchyModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	public function save()
	{
		// 1. update user confirmation 1 and reson
		$app   = JFactory::getApplication();
		$formfield = $app->input->post;
		$model = $this->getModel();
		if ($model->store($formfield))
		{
			// 2. send mail to bh and hr
			JFactory::getApplication()->enqueueMessage(JText::_('COM_HIERARCHY_CONFIRMATION_RESCHEDULES_SAVE'), 'message');
			$this->setRedirect('index.php?option=com_hierarchy&view=hierarchys&tmpl=component');
		}
		else
		{
			$tid = $formfield->get('ticketid');
			$tid = base64_encode($tid);
			JFactory::getApplication()->enqueueMessage(JText::_('COM_HIERARCHY_RESCHEDULES_SAVE_ERROR'), 'error');
			$this->setRedirect('index.php?option=com_hierarchy&view=hierarchys&tmpl=component&tid='.$tid);
		}
	}
}
