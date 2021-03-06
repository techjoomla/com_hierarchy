<?php
/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
require_once JPATH_COMPONENT . '/helpers/hierarchy.php';
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$saveOrder  = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_tjlms&task=venues.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'venueList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$app = JFactory::getApplication();
$menulistChart = $app->getParams()->get('select_layout');
?>
<div class="container-fluid">
	<?php
	if ($this->params->get('show_page_heading', 1)):
		?>
		<div class="page-header">
			<h2><?php echo JText::_('COM_HIERARCHY_TITLE'); ?></h2>
		</div>
		<?php
	endif;
	?>
</div>
<?php
	if ($menulistChart == 0 || $menulistChart == '')
	{
		$hierarchyPath = $this->HierarchyFrontendHelper ->getViewPath('hierarchys', 'list');
	}
	else
	{
		$hierarchyPath = $this->HierarchyFrontendHelper ->getViewPath('hierarchys', 'chart');
	}

	ob_start();
	include $hierarchyPath;
	$html = ob_get_contents();
	ob_end_clean();

	echo $html;
?>
