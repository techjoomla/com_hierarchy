<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Hierarchy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Hierarchy records.
 *
 * @since  1.6
 */
class HierarchyModelHierarchys extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name', 'a.name',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$UserNames = $app->getUserStateFromRequest($this->context . '.filter.hierarchy_users', 'filter_hierarchy_users', '', 'string');
		$this->setState('filter.hierarchy_users', $UserNames);

		$contextName = $app->getUserStateFromRequest($this->context . '.filter.context', 'filter_context', '', 'string');
		$this->setState('filter.context', $contextName);

		// Filtering usergroup
		$groupId = $this->getUserStateFromRequest($this->context . '.usergroup', 'usergroup', null, 'int');
		$this->setState('usergroup', $groupId);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_hierarchy');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
				$this->getState('list.select',
				'DISTINCT' . $db->quoteName('a.id', 'subuserId') . ',' . $db->quoteName('a.name') . ',' . $db->quoteName('a.username')
				)
				);
		$query->from($db->quoteName('#__users', 'a'));

		// Join over the user field 'user_id'
		$query->select(
				$db->quoteName(
					array('hu.id', 'hu.user_id', 'hu.reports_to', 'hu.context', 'hu.context_id', 'hu.state', 'hu.note'),
					array(null, 'bossId', 'empId', null, null, null, null)
							)
				);
		$query->join('LEFT', $db->quoteName('#__hierarchy_users', 'hu') . ' ON (' . $db->quoteName('hu.reports_to') . ' = ' . $db->quoteName('a.id') . ')');

		// Filter by search in title
		$search = $this->getState('filter.search');
		$UserNames = $this->getState('filter.hierarchy_users');
		$contextName = $this->getState('filter.context');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.name LIKE ' . $search . ' )');
			}
		}

		// Filter by user name
		if (!empty($UserNames))
		{
			$UserNames = $db->Quote('%' . $db->escape($UserNames, true) . '%');
			$query->where('( a.id LIKE ' . $UserNames . ' )');
		}

		// Filter by context
		if (!empty($contextName))
		{
			$contextName = $db->Quote('%' . $db->escape($contextName, true) . '%');
			$query->where('( hu.context LIKE ' . $contextName . ' )');
		}

		$query->where('a.block=0');

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		// Filter the items over the group id if set.
		$groupId = $this->getState('usergroup');

		if ($groupId)
		{
			$query->join('LEFT', '#__user_usergroup_map AS map2 ON map2.user_id = a.id');

			if ($groupId)
			{
				$query->where('map2.group_id = ' . (int) $groupId);
			}
		}

		return $query;
	}

	/**
	 * Method to get a list of users.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}

	/**
	 * Method to save the hierachty for the user
	 *
	 * @param   integer  $data  Array of userid and managers
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function saveUserManagers($data)
	{
		if ($data['userId'])
		{
			if (!empty($data['managerIds']))
			{
				JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hierarchy/tables');

				foreach ($data['managerIds'] as $mangerId)
				{
					$hierTable  = JTable::getInstance('hierarchy', 'HierarchyTable');
					$hierTable->load(array("subuser_id" => $data['userId']));
					$hierTable->user_id = $mangerId;
					$hierTable->subuser_id = $data['userId'];
					$hierTable->store();
				}
			}
		}

		return true;
	}

	/**
	 * Method to save the hierachty for the user
	 *
	 * @param   Array  $data  user id and manager ids
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function deleteUserManagers($data)
	{
		if ($data['userId'])
		{
			if (!empty($data['managerIds']))
			{
				$data['managerIds'] = (array) $data['managerIds'];

				JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_hierarchy/tables');
				$hierTable  = JTable::getInstance('hierarchy', 'HierarchyTable');

				foreach ($data['managerIds'] as $mangerId)
				{
					$hierTable->load(array("user_id" => $mangerId, "subuser_id" => $data['userId']));

					if ($hirTable->id)
					{
						$hierTable->delete($hirTable->id);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to get sub users
	 *
	 * @param   INT  $userId  Userid whose managers to get
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getSubusers($userId = null)
	{
		if ($userId === null)
		{
			$user 	= JFactory::getUser();
			$userId	= $user->get('id');
		}

		$db = $this->getDbo();

		$query = $db->getQuery(true);

		$conditions = array(
			$db->quoteName('hu.user_id') . " = " . (int) $userId,
			$db->quoteName('u.block') . " = 0",
		);

		if ($this->getState('filter.client'))
		{
			$conditions['client'] = $this->getState('filter.client');
		}

		if ($this->getState('filter.client_d'))
		{
			$conditions['client_id'] = $this->getState('filter.client_d');
		}

		$query->select($db->quoteName('hu.subuser_id'));
		$query->from($db->quoteName('#__hierarchy_users', 'hu'));
		$query->join('inner', $db->quoteName('#__users') . 'as u ON u.id=hu.subuser_id');
		$query->where($conditions);
		$db->setQuery($query);

		$result = $db->loadColumn();

		return $result;
	}

	/**
	 * Method to get sub users
	 *
	 * @param   INT  $userId  Userid whose managers to get
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getManagers($userId)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		$conditions = array(
			$db->quoteName('hu.subuser_id') . " = " . $userId,
			$db->quoteName('u.block') . " = 0"
		);

		if ($this->getState('filter.client'))
		{
			$conditions['client'] = $this->getState('filter.client');
		}

		if ($this->getState('filter.client_d'))
		{
			$conditions['client_id'] = $this->getState('filter.client_d');
		}

		$query->select($db->quoteName('user_id'));
		$query->from($db->quoteName('#__hierarchy_users'));
		$query->join('inner', $db->quoteName('#__users') . 'as u ON u.id=hu.user_id');
		$query->where($conditions);
		$db->setQuery($query);

		return $db->loadColumn();
	}

	/**
	 * Method to get a list of reporting users.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getReportToList()
	{
		$db = $this->getDbo();
		$query = "SELECT a.id AS value,a.name AS text
		FROM #__users AS a, #__hierarchy_users AS hu
		WHERE a.id = hu.user_id AND a.block=0
		GROUP BY user_id";
		$db->setQuery($query);

		return $res = $db->loadObjectList();
	}
}
