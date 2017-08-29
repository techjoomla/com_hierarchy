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

jimport('joomla.application.component.modeladmin');
jimport('joomla.database.table');

/**
 * Methods supporting a list of Hierarchy records.
 *
 * @since  1.6
 */
class HierarchyModelHierarchy extends JModelAdmin
{
	/**
	 * @var string The prefix to use with controller messages.
	 *
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_HIERARCHY';

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'hierarchy', $prefix = 'HierarchyTable', $config = array())
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hierarchy/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_hierarchy.hierarchy', 'hierarchy', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_hierarchy.edit.hierarchy.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
		}

		if ($item->user_id)
		{
			JLoader::import('components.com_hierarchy.models.hierarchys', JPATH_ADMINISTRATOR);
			$HierarchysModel = JModelLegacy::getInstance('Hierarchys', 'HierarchyModel');
			$HierarchysModel->getState('user_id', $item->user_id);
			$hierarchyData = $HierarchysModel->getItems();

			$item->users = array();

			foreach ($hierarchyData as $hierarchy)
			{
				if ($item->user_id == $hierarchy->user_id)
				{
					$item->users[] = $hierarchy->reports_to;
				}
			}
		}

		return $item;
	}

	/**
	 * Method to save an event data.
	 *
	 * @param   array  $data  data
	 *
	 * @return  void
	 *
	 * @since    1.6
	 */
	public function save($data)
	{
		$data['user_id']    = $data['userid'];
		$data['reports_to'] = $data['users'];

		$date = JFactory::getDate();

		if ($data['id'])
		{
			$data['modified_date'] = $date->toSql(true);
		}
		else
		{
			$data['created_date'] = $date->toSql(true);
		}

		$table = $this->getTable();

		// Bind data
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		if (parent::save($data))
		{
			$id = (int) $this->getState($this->getName() . '.id');

			return $id;
		}
		else
		{
			return false;
		}
	}
}
