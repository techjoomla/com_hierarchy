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
$user = JFactory::getUser();
$JUriRoot = JUri::root();
?>
<div class="alert alert-info" role="alert">
<?php echo JText::_('COM_HIERARCHY_SHOW_LIST') . $user->name . '.'; ?>
</div>
<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&view=hierarchys'); ?>" method="post" name="adminForm" id="adminForm">
	<div class=" col-lg-3 col-md-6 col-sm-6 col-xs-12">
		<div class="input-group">
			<input type="text" placeholder="<?php echo JText::_('COM_HIERARCHY_ENTER_USER_NAME'); ?>" name="filter_search" id="filter_search" value="<?php echo $srch = ($this->lists['search'])?$this->lists['search']:''; ?>" class="form-control" onchange="document.adminForm.submit();" />
			<span class="input-group-btn">
				<button type="button" onclick="this.form.submit();" class="btn btn-success tip hasTooltip" data-original-title="Search"><i class="fa fa-search"></i></button>
				<button type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();" class="btn btn-primary tip hasTooltip" data-original-title="Clear"><i class="fa fa-remove"></i></button>
			</span>
		</div>
	</div>
	<div class=" col-lg-8 col-md-6 col-sm-6 col-xs-12">
		<div class="input-group pull-right">
			<?php echo JHtml::_('select.genericlist', $this->contextList, "filter_context", 'style="display:inline-block;" class="selectpicker" data-style="btn-primary" size="1" data-live-search="true"
				onchange="document.adminForm.submit();" name="filter_context"',"value", "text", $this->lists['contextList']);
			?>
		</div>
	</div>
	<div class=" col-lg-1 col-md-6 col-sm-6 col-xs-12">
		<div class="btn-group pull-right hidden-xs">
			<label for="limit" class="element-invisible">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
	<div class="clearfix"> </div>
	<?php
	if (empty($this->items ))
	{
		?>
		<div class="alert alert-info" role="alert">
			<?php echo JText::_('NODATA'); ?>
		</div>
		<?php
	}
	else
	{
		?>
		<table class="table table-striped table-hover" id="hierarchyList">
			<thead>
				<tr>
					<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_HIERARCHYS_USER_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
						<?php echo JText::_('COM_HIERARCHY_CONTEXT'); ?>
					</th>
					<th class='left'>
						<?php echo JText::_('COM_HIERARCHY_CONTEXT_ID'); ?>
					</th>
					<th class='left'>
						<?php echo JText::_('COM_HIERARCHY_HIERARCHYS_REPORT_TO'); ?>
					</th>
					<th class='right'>
						<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_HIERARCHYS_USER_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<body>
				<?php
				foreach ($this->items as $item)
				{
					JLoader::import('components.com_hierarchy.models.hierarchys', JPATH_SITE);
					$hierarchysModel = JModelLegacy::getInstance('Hierarchys', 'HierarchyModel');
					$hierarchys = $hierarchysModel->getReportsTo($item->reports_to);
				}

				$hierarchyFrontendHelper = new HierarchyFrontendHelper;
				$gravatar = $hierarchyFrontendHelper->getUserAvatar($item->subuserId);

				foreach ($hierarchys as $i => $hierarchy)
				{
					$user = JFactory::getUser($hierarchy->user_id);
					?>
					<tr class="row<?php echo $i % 2; ?> reports_to">
						<td>
							<img src="<?php echo $gravatar; ?>" class="img-rounded" alt="" width="30" height="30">
							<a href="#" title="<?php echo $user->name;?>"><?php  echo $user->name; ?>
								<i class="fa fa-angle-down" onclick="hierarchySite.hierarchys.drillUpDrillDownList()"; aria-hidden="true"></i>
							</a>
						</td>
						<td>
						<?php
							echo $hierarchy->context = !empty($hierarchy->context) ? $hierarchy->context : '-';
							?>
						</td>
						<td>
						<?php
							echo $hierarchy->context_id = !empty($hierarchy->context_id) ? $hierarchy->context_id : '-';
							?>
						</td>
						<td>
						<?php
							if ($hierarchy->user_id)
							{
								$name = array();

								$reportsTo = $hierarchysModel->getReportsTo($hierarchy->user_id);

								foreach($reportsTo as $reportTo)
								{
									$user = JFactory::getUser($reportTo->user_id);
									$name[] = $user->name;
								}

								$userName = implode(', ', $name);
								?>
								<span id="popover_<?php echo $i; ?>" data-toggle="popover" data-trigger="hover" data-placement="right"  data-content="<?php echo $userName; ?>"><?php echo $userName = strlen($userName) > 20 ? substr($userName, 0, 20) . "..." : $userName; ?></span>
								<?php
							}
							?>
						</td>
						<td><?php echo $hierarchy->user_id; ?></td>
					</tr>
					<?php 
				}
				?>
			</body>
		</table>
		<?php
	}
	?>
	<input type="hidden" id="task" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php
$hierarchys = json_encode($hierarchys);
?>
<script type="text/javascript">
	var JUriRoot = "<?php echo $JUriRoot; ?>";
	var gravatar = "<?php echo $gravatar; ?>";
	var hierarchys = '<?php echo $hierarchys; ?>';
	hierarchySite.hierarchys.showUserNames();
</script>
