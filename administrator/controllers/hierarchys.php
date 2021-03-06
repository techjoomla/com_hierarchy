<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Hierarchy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('joomla.filesystem.file');

/**
 * The Hierarchys List Controller
 *
 * @since  1.6
 */
class HierarchyControllerHierarchys extends JControllerAdmin
{
	/**
	 * Proxy for getModel
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'hierarchy', $prefix = 'HierarchyModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to csv Import
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function csvImport()
	{
		jimport('joomla.filesystem.file');
		$app = JFactory::getApplication();

		// Start file heandling functionality
		$fname       = $_FILES['csvfile']['name'];
		$rowNum      = 0;
		$uploadsDir = JFactory::getApplication()->getCfg('tmp_path') . '/' . $fname;
		JFile::upload($_FILES['csvfile']['tmp_name'], $uploadsDir);

		if ($file = fopen($uploadsDir, "r"))
		{
			$ext = JFile::getExt($uploadsDir);

			if ($ext != 'csv')
			{
				$app->enqueueMessage(JText::_('COM_HIERARCHY_CSV_FORMAT_ERROR'), 'Message');
				$app->redirect(JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false));

				return;
			}

			while (($data = fgetcsv($file)) !== false)
			{
				if ($rowNum == 0)
				{
					// Parsing the CSV header
					$headers = array();

					foreach ($data as $d)
					{
						$headers[] = $d;
					}
				}
				else
				{
					// Parsing the data rows
					$rowData = array();

					foreach ($data as $d)
					{
						$rowData[] = $d;
					}

					$userData[] = array_combine($headers, $rowData);
				}

				$rowNum++;
			}

			fclose($file);
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_HIERARCHY_IMPORT_CSV_ERROR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false));

			return;
		}

		$model = $this->getModel();
		$userID = JFactory::getUser()->id;

		$data = array();

		foreach ($userData as $user)
		{
			$data['id']         = '';
			$data['user_id']    = !empty($user['User_id']) ? $user['User_id'] : $this->getUserId($user['User_email']);
			$data['reports_to'] = !empty($user['Reports_to']) ? $user['Reports_to'] : $this->getUserId($user['Reports_to_email']);
			$data['context']    = $user['Context'];
			$data['context_id'] = $user['Context_id'];
			$data['created_by'] = $userID;

			$result = $model->save($data);
		}

		$msg = JText::_('COM_HIERARCHY_IMPORT_CSV_SUCCESS_MSG');
		$app->redirect(JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false), $msg);

		return;
	}

	/**
	 * Get user id from user email
	 *
	 * @param   string  $email  email.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function getUserId($email)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('email') . ' = ' . $db->quote($email));
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Delete hierarchy from the list
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function remove()
	{
		$model = $this->getModel();
		$input = JFactory::getApplication()->input;
		$post  = $input->post;
		$userIds = $post->get('cid', '', 'ARRAY');

		$records = array();

		foreach ($userIds as $userId)
		{
			$hierarchyData = $model->getReportsTo($userId);
			$records = array_merge($records, $hierarchyData);
		}

		$hierarchyIds = array();

		foreach ($records as $record)
		{
			$hierarchyIds[] = $record->id;
		}

		if ($model->delete($hierarchyIds))
		{
			$msg = JText::_('COM_HIERARCHY_HIERARCHY_DELETED_SCUSS');
		}
		else
		{
			$msg = JText::_('COM_HIERARCHY_HIERARCHY_DELETED_ERROR');
		}

		$this->setRedirect("index.php?option=com_hierarchy&view=hierarchys", $msg);
	}
}
