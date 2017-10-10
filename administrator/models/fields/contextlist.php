<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Hierarchy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2016 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of courses
 *
 * @since  1.0.0
 */
class JFormFieldContextList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var   string
	 * @since 1.6
	 */
	protected $type = 'contextlist';

	/**
	 * Fiedd to decide if options are being loaded externally and from xml
	 *
	 * @var   integer
	 * @since 2.2
	 */
	protected $loadExternally = 0;

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array An array of JHtml options.
	 *
	 * @since   11.4
	 */
	protected function getOptions()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('DISTINCT id, user_id, context, context_id');
		$query->from('#__hierarchy_users');

		$db->setQuery($query);

		// Get all countries.
		$contextList = $db->loadObjectList();

		$options = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_HIERARCHY_SELECT_CONTEXT'));

		if (!empty($contextList))
		{
			foreach ($contextList as $context)
			{
				$context   = $context->context;
				$options[] = JHtml::_('select.option', $context, $context);
			}
		}

		$options = array_unique($options, SORT_REGULAR);

		if (!$this->loadExternally)
		{
			// Merge any additional options in the XML definition.
			$options = array_merge(parent::getOptions(), $options);
		}

		return $options;
	}
}
