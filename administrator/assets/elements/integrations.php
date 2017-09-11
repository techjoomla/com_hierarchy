<?php
/**
 * @version    SVN: <svn_id>
 * @package    Com_Hierarchy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
jimport('joomla.application.component.helper');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');

/**
 * JFormFieldIntegrations class
 *
 * @package     Com_Hierarchy
 * @subpackage  component
 * @since       1.0
 */
class JFormFieldIntegrations extends JFormField
{
	/**
	 * Method to get the field input markup.
	 *
	 * @since  1.6
	 *
	 * @return   string  The field input markup
	 */
	public function getInput ()
	{
		return $this->fetchElement($this->name, $this->value, $this->element, $this->options['controls']);
	}

	/**
	 * Method fetchElement
	 *
	 * @param   string  $name          name of element
	 * @param   string  $value         value of element
	 * @param   string  &$node         node
	 * @param   string  $control_name  control name
	 *
	 * @return  array country list
	 *
	 * @since   1.0
	 */
	public function fetchElement ($name, $value, &$node, $control_name)
	{
		$communityMainFile = JPATH_SITE . '/components/com_community/community.php';
		$jeventsMainFile   = JPATH_SITE . '/components/com_jevents/jevents.php';
		$esMainFile        = JPATH_SITE . '/components/com_easysocial/easysocial.php';
		$easyProMainFile   = JPATH_SITE . '/components/com_jsn/jsn.php';
		$cbMainFile        = JPATH_SITE . '/components/com_comprofiler/comprofiler.php';

		if ($name == 'jform[integration]')
		{
			$options = array();
			$options[] = JHTML::_('select.option', '2', JText::_('COM_HIERARCHY_NATIVE'));

			if (JFile::exists($communityMainFile))
			{
				$options[] = JHtml::_('select.option', '1', JText::_('COM_HIERARCHY_JOMSOCIAL'));
			}

			if (JFile::exists($jeventsMainFile))
			{
				$options[] = JHtml::_('select.option', '3', JText::_('COM_HIERARCHY_JEVENT'));
			}

			if (JFile::exists($esMainFile))
			{
				$options[] = JHtml::_('select.option', '4', JText::_('COM_HIERARCHY_EASYSOCIAL'));
			}

			if (JFile::exists($easyProMainFile))
			{
				$options[] = JHtml::_('select.option', '5', JText::_('COM_HIERARCHY_EASYPROFILE'));
			}

			if (JFile::exists($cbMainFile))
			{
				$options[] = JHtml::_('select.option', '6', JText::_('COM_HIERARCHY_COMMUNITY_BUILDER'));
			}

			$fieldName = $name;
		}

		$html = JHtml::_('select.genericlist',  $options, $fieldName, 'class="inputbox"', 'value', 'text', $value, $control_name . $name);

		return $html;
	}
}
