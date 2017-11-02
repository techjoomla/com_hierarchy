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

		$this->db = JFactory::getDbo();
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_hierarchy/tables');
		$this->hierarchyTableObj = JTable::getInstance('Hierarchy', 'HierarchyTable', array('dbo', $this->db));

		$this->msg   = JText::_('COM_HIERARCHY_REPORTEES_SAVE_MSG');
		parent::__construct();
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @param   string  $key  TO ADD
	 *
	 * @return    void
	 *
	 * @since    1.6
	 */
	public function save($key = null)
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
		$data['user_id']  = $jinput->get('user_id', '', 'int');
		$data['created_by']  = $jinput->get('created_by', '', 'int');
		$data['modified_by'] = $jinput->get('modified_by', '', 'int');

		// To add new or remove manager from the selected managers list.
		$hierarchysData = $model->getReportsTo($data['user_id']);

		$userIDs = array();

		foreach ($hierarchysData as $hierarchy)
		{
			$userIDs[] = $hierarchy->reports_to;
		}

		$deleteUser = array_diff($userIDs, $data['reports_to']);

		// Delete user from the existing list
		foreach ($deleteUser as $key => $val)
		{
			$this->hierarchyTableObj->load(array('reports_to' => (int) $val));
			$id = $this->hierarchyTableObj->id;
			$return = $model->delete($id);
		}

		foreach ($data['reports_to'] as $key => $val)
		{
			$data['reports_to'] = $val;
			$return = $model->save($data);
		}

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
			$app->redirect($redirect, $this->msg);
		}

		// Clear the profile id from the session.
		$app->setUserState('com_hierarchy.edit.hierarchy.id', null);

		// Check in the profile.
		if ($return)
		{
			$model->checkin($return);
		}

		// Redirect to the list screen.
		$redirect = JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false);
		$app->redirect($redirect, $this->msg);

		// Flush the data from the session.
		$app->setUserState('com_hierarchy.edit.hierarchy.data', null);
	}

	/**
	 * Method to get users to manage hierarchy.
	 *
	 * @return  array
	 *
	 * @since    1.6
	 */
	public function getUsersToManageHierarchy()
	{
		$jinput = JFactory::getApplication()->input;
		$userId = $jinput->get('user_id', '', 'int');

		// Get the model.
		$model = $this->getModel('Hierarchy', 'HierarchyModel');

		// Get the list
		$list = $model->getUsersToManageHierarchy($userId);

		// Output json response
		header('Content-type: application/json');
		echo json_encode($list);
		jexit();
	}
}
