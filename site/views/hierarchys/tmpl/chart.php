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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::stylesheet(Uri::root() . 'media/com_hierarchy/vendors/treant-js/Treant.css');
JHtml::stylesheet(Uri::root() . 'media/com_hierarchy/vendors/treant-js/collapsable.css');
JHtml::script(Uri::root() . 'media/com_hierarchy/vendors/treant-js/vendor/raphael.js');
JHtml::script(Uri::root() . 'media/com_hierarchy/vendors/treant-js/Treant.js');
$UriRoot = Uri::root();
$user = Factory::getUser();
$userName = $user->name;

foreach ($this->items as $hierarchy)
{
	$user = Factory::getUser($hierarchy->user_id);
	$hierarchy->repoToName = $user->name;
}
?>
<div class="alert alert-info" role="alert">
	<?php echo Text::_('COM_HIERARCHY_SHOW_CHART');?><b><?php echo $userName . '.'; ?></b>
</div>
<div id="hierarchy_chart"></div>
<script type="text/javascript">
	var UriRoot = "<?php echo $UriRoot; ?>";
	var userName = "<?php echo $userName; ?>";

	/** Show people directly reporting to logged in user **/
	var childrenArrayObject = [];
	<?php foreach ($this->items as $key => $data):?>
		var gravatar = "<?php echo $this->gravatar;?>";

			/** Get sub-user names and create node structure **/
			<?php $hierarchys = $this->hierarchysModel->getReportsTo($data->user_id);
				foreach ($hierarchys as $hierarchy) {
					$user = Factory::getUser($hierarchy->user_id);
					$hierarchy->subUserName = $user->name;
				} ?>
				var subChildArrObj = [];
				<?php foreach ($hierarchys as $subKey => $subData):?>
					var tmpSubChild = {
						text: {
							name: '<?php echo $subData->subUserName; ?>'
						},
						image: gravatar,
						collapsed: true
					}
					subChildArrObj.push(tmpSubChild);
			<?php endforeach; ?>

		/**CREATE A JAVASCRIPT OBJECT**/
		var tmpChild = {
				text: {
					name: '<?php echo $data->repoToName; ?>'
				},
				image: gravatar,
				collapsed: true,
				children:subChildArrObj
			}
		childrenArrayObject.push(tmpChild);
	<?php endforeach; ?>
	hierarchySite.hierarchys.displayHierarchyChart();
</script>
