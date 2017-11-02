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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::stylesheet(JURI::root().'media/com_hierarchy/vendors/treant-js/Treant.css');
JHtml::script(JUri::root() . 'media/com_hierarchy/vendors/treant-js/Treant.js');
JHtml::script(JUri::root() . 'media/com_hierarchy/vendors/treant-js/vendor/raphael.js');

$JUriRoot = JUri::root();
$user = JFactory::getUser();
$userName = $user->name;
JLoader::import('components.com_hierarchy.models.hierarchys', JPATH_SITE);
$hierarchysModel = JModelLegacy::getInstance('Hierarchys', 'HierarchyModel');

foreach ($this->items as $item)
{
	$hierarchysRepo = $hierarchysModel->getReportsTo($item->reports_to);

	foreach ($hierarchysRepo as $res)
	{
		$user = JFactory::getUser($res->user_id);
		$res->repoToName = $user->name;
	}
}
?>
<div id="hierarchy_chart" style="width:335px; height: 160px"></div>
<script type="text/javascript">
	var JUriRoot = "<?php echo $JUriRoot; ?>";
	var userName = "<?php echo $userName; ?>";

	var childrenArrayObject = [];
	<?php foreach ($hierarchysRepo as $key => $data):?>
		var user_id = "<?php echo $data->user_id; ?>";

		/**CREATE A JAVASCRIPT OBJECT**/
		var tmpChild = {
				text: {
					name: '<?php echo $data->repoToName; ?>'
				},
				id: user_id,
				collapsed: true
			}

		childrenArrayObject.push(tmpChild);
	<?php endforeach; ?>
	hierarchySite.hierarchys.displayHierarchyChart();
</script>
