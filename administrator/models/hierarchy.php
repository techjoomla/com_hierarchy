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

		return $item;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__hierarchy_users');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * get All users list.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function getHirUser()
	{
		$db = $this->getDbo();

		$db->setQuery('SELECT * FROM #__hierarchy_users');

		return $AllUser = $db->loadObjectList();
	}

	/**
	 * Import csv data for hierarchy user save in database.
	 *
	 * @param   array  $userData  csv file data.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function saveCSVdata($userData)
	{
		$db                 = $this->getDbo();
		$subuserid          = 0;
		$reportToId         = 0;
		$booking_start_date = 0;
		$useridnotfound     = 0;
		$reportToIdnotfound = 0;
		$bad                = 0;
		$new                = 0;
		$updated_records    = 0;
		$notexist           = 0;
		$totalUser          = count($userData);

		if (!empty($userData))
		{
			$data = array();

			foreach ($userData as $eachUser)
			{
				foreach ($eachUser as $key => $value)
				{
					switch ($key)
					{
						case 'User Id / Email Id' :
							$data['subuser_id'] = 0;

							if (!empty ($value))
							{
								$data['subuser_id'] = $value;
							}

						break;

						case 'Report To Id / Email Id' :
							$data['user_id'] = 0;

							if (!empty($value))
							{
								$data['user_id'] = $value;
							}

						break;

						default :
						break;
					}
				}

				if ($data['subuser_id'])
				{
					if ($data['user_id'])
					{
						if (filter_var($data['user_id'], FILTER_VALIDATE_EMAIL))
						{
						$query = "SELECT id FROM #__users WHERE email='" . $data['user_id'] . "'";
						$db->setQuery($query);
						$existUserID = $db->loadResult();
						}
						else
						{
						$query = "SELECT id FROM #__users WHERE id='" . $data['user_id'] . "'";
						$db->setQuery($query);
						$existUserID = $db->loadResult();
						}

						if (filter_var($data['subuser_id'], FILTER_VALIDATE_EMAIL))
						{
						$query = "SELECT id FROM #__users WHERE email='" . $data['subuser_id'] . "'";
						$db->setQuery($query);
						$existSubUserID = $db->loadResult();
						}
						else
						{
						$query = "SELECT id FROM #__users WHERE id='" . $data['subuser_id'] . "'";
						$db->setQuery($query);
						$existSubUserID = $db->loadResult();
						}

						if ($existSubUserID)
						{
							if ($existUserID)
							{
								$query = "DELETE FROM #__hierarchy_users WHERE subuser_id=" . $existSubUserID;
								$db->setQuery($query);
								$db->execute($query);

								$insert_obj             = new stdClass;
								$insert_obj->user_id    = $existUserID;
								$insert_obj->subuser_id = $existSubUserID;

								if ($db->insertObject('#__hierarchy_users', $insert_obj, 'id'))
								{
									$test[] = $insert_obj->id;
								}
								else
								{
									$bad++;
								}
							}
							else
							{
								$reportToIdnotfound ++;
							}
						}
						else
						{
							$useridnotfound ++;
						}
					}
					else
					{
						$reportToId ++;
					}
				}
				else
				{
					$subuserid ++;
				}
			}
		}

		$output['msg'] = JText::sprintf('COM_HIERARCHY_USER_IMPORT_SUCCESS_MSG', $totalUser, $subuserid, $useridnotfound);
		$output['msg1'] = JText::sprintf('COM_HIERARCHY_USER_IMPORT_SUCCESS_MSG1', $reportToId, $reportToIdnotfound);
		$output['msg2'] = JText::sprintf('COM_HIERARCHY_USER_IMPORT_SUCCESS_MSG2', count($test));

		return $output;
	}
}
