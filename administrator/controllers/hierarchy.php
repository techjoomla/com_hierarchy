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
 * The Hierarchy Controller
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

	/**
	 * Method to save a user's profile data.
	 *
	 * @param   string  $key     TO ADD
	 * @param   string  $urlVar  TO ADD
	 *
	 * @return    void
	 *
	 * @since    1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = JFactory::getApplication();
		$model = $this->getModel('hierarchy', 'HierarchyModel');

		// Get the user data.
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');

		// Validate the posted data.
		$form = $model->getForm();

		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		// Validate the posted data.
		if (!empty($form))
		{
			$data = $model->validate($form, $data);
		}

		$jinput = JFactory::getApplication()->input;
		$data['userid'] = $jinput->get('user_id', '', 'int');

		foreach ($data['users'] as $key => $val)
		{
			$data['users'] = $val;
			$return = $model->save($data);
		}

		$msg   = JText::_('COM_HIERARCHY_REPORTEES_SAVE_MSG');
		$input = JFactory::getApplication()->input;
		$id    = $input->get('id');

		if (empty($id))
		{
			$id = $return;
		}

		$task = $input->get('task');

		if ($task == 'apply')
		{
			$redirect = JRoute::_('index.php?option=com_hierarchy&view=hierarchy&layout=edit&id=' . $id, false);
			$app->redirect($redirect, $msg);
		}

		if ($task == 'save2new')
		{
			$redirect = JRoute::_('index.php?option=com_hierarchy&view=hierarchy&layout=edit', false);
			$app->redirect($redirect, $msg);
		}

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Redirect to the list screen.
		$redirect = JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false);
		$app->redirect($redirect, $msg);
	}
}
