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
		require_once JPATH_COMPONENT . '/helpers/hierarchy.php';

		$this_reportTo = $this->get('ReportToList');
		$reportTo       = array();
		$reportTo[]     = JHtml::_('select.option', '0', JText::_('COM_HIERARCHY_FILTER_SELECT_LABEL1'));

		foreach ($this_reportTo as $k => $value)
		{
			$reportTo[] = JHtml::_('select.option', $value->value, $value->text);
		}

		$this->reportTo = $reportTo;

		$state = $this->get('State');
		$canDo = HierarchyHelper::getActions($state->get('filter.category_id'));

		JToolBarHelper::title(JText::_('COM_HIERARCHY_TITLE_HIERARCHYS'), 'hierarchys.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/hierarchy';

		$bar = JToolBar::getInstance('toolbar');
		$layout = JFactory::getApplication()->input->get('layout', 'default');

		if ($layout == 'default')
		{
			$button = "<a class='btn' class='button'
			type='submit' id='export-submit' href='#usersCsv'><span title='Export'
			class='icon-download icon-white'></span>" . JText::_('CSV_EXPORT') . "</a>";
			$bar->appendButton('Custom', $button);
		}

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

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_hierarchy&view=hierarchys');

		$this->extra_sidebar = '';

		$this->extra_sidebar .= '<hr><h4 class="page-header reportToWidthLabel">Filter';

		// Filter for the field user_id
		// $this->extra_sidebar .= '<label class="reportToWidthLabel" for="filter_user_id">Report to</label>';

		$v_att = 'onchange="this.form.submit();" class="reportToWidth"';
		$d = $this->state->get('filter.user_id');

		$this->extra_sidebar .= JHtml::_('select.genericlist', $this->reportTo, "filter_user_id", $v_att, "value", "text", $d);

		$this->extra_sidebar .= '</h4>';

		// $this->extra_sidebar .= JHtmlList::users('filter_user_id', $this->state->get('filter.user_id'), 1, 'onchange="this.form.submit();"');
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
