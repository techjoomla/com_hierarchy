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

jimport('joomla.application.component.view');

/**
 * View class for a list of Hierarchy.
 *
 * @since  1.6
 */
class HierarchyViewHierarchys extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');

		// $this->userlist = $this->get('UserList');
		$this->pagination = $this->get('Pagination');

		// Get filter form.
		$this->filterForm = $this->get('FilterForm');

		// Get active filters.
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		HierarchyHelper::addSubmenu('hierarchys');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		// Import Csv export button
		jimport('techjoomla.tjtoolbar.button.csvexport');

		require_once JPATH_COMPONENT . '/helpers/hierarchy.php';

		$state = $this->get('State');
		$canDo = HierarchyHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_HIERARCHY_TITLE_HIERARCHYS'), 'hierarchys.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/hierarchy';

		$bar = JToolBar::getInstance('toolbar');

		$message = array();
		$message['success'] = JText::_("COM_HIERARCHY_EXPORT_FILE_SUCCESS");
		$message['error'] = JText::_("COM_HIERARCHY_EXPORT_FILE_ERROR");
		$message['inprogress'] = JText::_("COM_HIERARCHY_EXPORT_FILE_NOTICE");

		$buttonImport = '<a href="#import_append" class="btn button modal" rel="{size: {x: 800, y: 200}, ajaxOptions: {method: &quot;get&quot;}}">
		<span class="icon-upload icon-white"></span>' . JText::_('COM_HIERARCHY_IMPORT_CSV') . '</a>';
		$bar->appendButton('Custom', $buttonImport);

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('hierarchys.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('hierarchys.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_hierarchy');
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.user_id' => JText::_('COM_HIERARCHY_HIERARCHYS_USER_ID'),
			'a.subuser_id' => JText::_('COM_HIERARCHY_HIERARCHYS_SUBUSER_ID')
		);
	}
}
