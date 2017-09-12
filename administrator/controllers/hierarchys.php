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
		$mainframe = JFactory::getApplication();
		$rs1       = @mkdir(JPATH_COMPONENT_ADMINISTRATOR . '/csv', 0777);

		// Start file heandling functionality *
		$fname       = $_FILES['csvfile']['name'];
		$uploadsDir = JPATH_COMPONENT_ADMINISTRATOR . '/csv/' . $fname;
		move_uploaded_file($_FILES['csvfile']['tmp_name'], $uploadsDir);

		$file      = fopen($uploadsDir, "r");
		$contentsc = "";
		$info      = pathinfo($uploadsDir);
		$rowNum    = 0;

		if ($info['extension'] != 'csv')
		{
			$msg = JText::_('NOT_CSV_MSG');
			$mainframe->redirect(JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false), "<b>" . $msg . "</b>");

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

		$model = $this->getModel();

		$data = array();

		foreach ($userData as $key => $val)
		{
			$data['context'] = $val['Context'];
			$data['context_id'] = $val['Context_id'];
			$data['userid'] = $val['SubuserId'];
			$data['users'] = $val['Reports_to'];

			$resultx = $model->save($data);
		}

		$return = $resultx['return'];
		$msg = $resultx['msg'];
		$msg .= $resultx['msg1'];
		$msg .= $resultx['msg2'];
		$mainframe->redirect(JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false), "<b>" . $msg . "</b>");

		return;
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
