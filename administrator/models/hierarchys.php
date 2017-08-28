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

		$user_id = $this->getUserStateFromRequest($this->context . '.user_id', 'user_id', null, 'int');
		$this->setState('user_id', $user_id);

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
					array('hu.id', 'hu.user_id', 'hu.reports_to', 'hu.context', 'hu.context_id', 'hu.state', 'hu.note')
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
	 * Delete order
	 *
	 * @param   integer  $hierarchyID  id of jticketing_order table to delete
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function delete($hierarchyID)
	{
		$db = JFactory::getDBO();
		$id = implode(',', $hierarchyID);

		// Delete the order item
		$db = JFactory::getDbo();
		$deleteHierarchy = $db->getQuery(true);
		$deleteHierarchy->delete($db->quoteName('#__hierarchy_users'));
		$deleteHierarchy->where('id IN (' . $id . ')');
		$db->setQuery($deleteHierarchy);
		$confrim = $db->execute();

		if ($confrim)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
