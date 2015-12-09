<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hierarchy
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
		$uploads_dir = JPATH_COMPONENT_ADMINISTRATOR . '/csv/' . $fname;
		move_uploaded_file($_FILES['csvfile']['tmp_name'], $uploads_dir);

		$file      = fopen($uploads_dir, "r");
		$contentsc = "";
		$info      = pathinfo($uploads_dir);
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
		$resultx = $model->saveCSVdata($userData);

		$return = $resultx['return'];
		$msg = $resultx['msg'];
		$msg .= $resultx['msg1'];
		$msg .= $resultx['msg2'];
		$mainframe->redirect(JRoute::_('index.php?option=com_hierarchy&view=hierarchys', false), "<b>" . $msg . "</b>");

		return;
	}

	/**
	 * Method to csv export
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function csvexport()
	{
		$input      = Jfactory::getApplication()->input;
		$post       = $input->post;
		$model      = $this->getModel('hierarchys');
		$DATA       = $model->getItems();
		$db         = JFactory::getDBO();

		// Create CSV headers
		$csvData       = null;
		$csvData_arr[] = JText::_('User Id');
		$csvData_arr[] = JText::_('User Name');
		$csvData_arr[] = JText::_('Report To Id');
		$csvData_arr[] = JText::_('Report To Name');

		$filename = "UserDataExport_" . date("Y-m-d_H-i", time());

		// Set CSV headers
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=" . $filename . ".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		$csvData .= implode(',', $csvData_arr);
		$csvData .= "\n";
		echo $csvData;

		$csvData      = '';

		foreach ($DATA as $data)
		{
			$csvData_arr1 = array();

			// #if ($data->bossId)
			{
				$csvData_arr1[] = $data->subuserId;
				$csvData_arr1[] = JFactory::getUser($data->subuserId)->name;

				if ($data->bossId)
				{
					$csvData_arr1[] = $data->bossId;
					$csvData_arr1[] = JFactory::getUser($data->bossId)->name;
				}
				else
				{
					$csvData_arr1[] = '';
					$csvData_arr1[] = '';
				}

				$csvData = implode(',', $csvData_arr1);
				echo $csvData . "\n";
			}
		}

		jexit();
	}

	/**
	 * Method to set user.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function setUser()
	{
		$jinput     = JFactory::getApplication()->input;
		$subuserId  = $jinput->get->get('subuserId', '', 'int');
		$bossuserId = $jinput->post->get('user_id', '', 'int');

		// Get the model
		$model  = $this->getModel();
		$return = $model->saveUserHier($bossuserId, $subuserId);

		jexit();
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}
