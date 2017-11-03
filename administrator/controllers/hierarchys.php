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

		foreach ($userData as $key => $val)
		{
			$data['id']         = '';
			$data['user_id']    = !empty($val['User_id']) ? $val['User_id'] : $this->getUserId($val['User_email']);
			$data['reports_to'] = !empty($val['Reports_to']) ? $val['Reports_to'] : $this->getUserId($val['Reports_to_email']);
			$data['context']    = $val['Context'];
			$data['context_id'] = $val['Context_id'];
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
	public static function getUserId($email)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('email') . ' = ' . $db->quote($email));
		$db->setQuery($query, 0, 1);

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
		$model = $this->getModel('hierarchys');
		$input = JFactory::getApplication()->input;
		$post  = $input->post;
		$hierarchyID = $post->get('cid', '', 'ARRAY');

		if ($model->delete($hierarchyID))
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
