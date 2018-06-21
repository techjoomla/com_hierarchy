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
				'id', 'a.id',
				'name', 'a.name',
				'context', 'a.context'
			);
		}

		// Create a new query object.
		$this->db = JFactory::getDbo();

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
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . 'filter_search', 'filter_search');
		$this->setState('filter_search', $search);

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
		$user = JFactory::getUser();

		// Create a new query object.+
		$query = $this->db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
				$this->getState('list.select',
				'DISTINCT' . $this->db->quoteName('a.id', 'subuserId') . ',' . $this->db->quoteName('a.name') . ',' . $this->db->quoteName('a.username')
				)
				);
		$query->from($this->db->quoteName('#__users', 'a'));

		// Join over the user field 'user_id'
		$query->select(
				$this->db->quoteName(
					array('hu.id', 'hu.user_id', 'hu.reports_to', 'hu.context', 'hu.context_id', 'hu.state', 'hu.note')
							)
				);
		$query->join('LEFT', $this->db->quoteName('#__hierarchy_users', 'hu') . '
		ON (' . $this->db->quoteName('hu.user_id') . ' = ' . $this->db->quoteName('a.id') . ')');

		// Filter by search in title
		$search = $this->getState('filter_search');

		if ($user->id)
		{
			$query->where('hu.reports_to = ' . (int) $user->id);
		}

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $this->db->Quote('%' . $this->db->escape($search, true) . '%');
				$query->where('( a.name LIKE ' . $search . ' )');
			}
		}

		$query->where('a.block=0');

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($this->db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get reports_to
	 *
	 * @param   INT  $reportsTo  reportsTo
	 *
	 * @return  Array of data
	 *
	 * @since   1.0
	 */
	public function getReportsTo($reportsTo)
	{
		$query = $this->db->getQuery(true);
		$query->select('*');
		$query->from($this->db->quoteName('#__hierarchy_users'));
		$query->where($this->db->quoteName('reports_to') . ' = ' . $this->db->quote($reportsTo));
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		foreach ($results as $res)
		{
			$user = JFactory::getUser($res->user_id);
			$res->reportsToName = $user->name;
		}

		return $results;
	}

	/**
	 * Method to get reporting to users
	 *
	 * @param   INT  $userID  userID
	 *
	 * @return  Array of data
	 *
	 * @since   1.0
	 */
	public function getReportingTo($userID)
	{
		$query = $this->db->getQuery(true);
		$query->select('*');
		$query->from($this->db->quoteName('#__hierarchy_users'));
		$query->where($this->db->quoteName('user_id') . ' < ' . $this->db->quote($userID));
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		foreach ($results as $res)
		{
			$user = JFactory::getUser($res->user_id);
			$res->reportingTo = $user->name;
		}

		return $results;
	}
}
