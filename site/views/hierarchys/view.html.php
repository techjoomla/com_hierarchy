<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Hierarchy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
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

	protected $params;

	/**
	 * Method to display event
	 *
	 * @param   object  $tpl  template name
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		// Validate user login.
		if (empty($user->id))
		{
			$msg = JText::_('COM_HIERARCHY_MESSAGE_LOGIN_FIRST');

			// Get current url.
			$current = JUri::getInstance()->toString();
			$url     = base64_encode($current);
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return=' . $url, false), $msg);
		}

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Filter by Context
		$contextName = $app->getUserStateFromRequest($this->context . 'filter_context', 'filter_context', '', 'string');

		$contextList = array();
		$contextList[] = JHtml::_('select.option', '0', JText::_('Select context'));

		if (!empty($this->items))
		{
			foreach ($this->items as $item)
			{
				$context   = $item->context;
				$contextList[] = JHtml::_('select.option', $context, $context);
			}
		}

		$this->contextList    = array_unique($contextList, SORT_REGULAR);
		$lists['contextList'] = $contextName;

		// Search filter
		$search = $app->getUserStateFromRequest($this->context . 'filter_search', 'filter_search');
		$lists['search'] = $search;
		$this->lists          = $lists;

		$this->HierarchyFrontendHelper     = new HierarchyFrontendHelper;

		// Get component params
		$this->params     = JComponentHelper::getParams('com_jticketing');
		parent::display($tpl);
	}

	/**
	 * Method to prepare document
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_HIERARCHY_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
