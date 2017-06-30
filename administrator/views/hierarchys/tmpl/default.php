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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHTML::_('behavior.modal', 'a.modal');
JHtml::_('behavior.keepalive');

// Import CSS
JHtml::stylesheet(JUri::root(). 'administrator/components/com_hierarchy/assets/css/hierarchy.css' );
JHtml::_('script', 'jui/fielduser.min.js', array('version' => 'auto', 'relative' => true));

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_hierarchy');
$saveOrder = $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_hierarchy&task=hierarchys.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'hierarchyList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function(){
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;

		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}

		Joomla.tableOrdering(order, dirn, '');
	}

	jQuery(document).ready(function () {
		jQuery('#import_append').attr('style','height:196px !important;');
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});

		jQuery('#export-submit').on('click', function () {
			document.getElementById('task').value = 'hierarchys.csvexport';
			document.adminForm.submit();
			document.getElementById('task').value = '';
		});
	});
</script>
<?php
// Joomla Component Creator code to allow adding non select list filters

if (!empty($this->extra_sidebar))
{
    $this->sidebar .= $this->extra_sidebar;
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&view=hierarchys'); ?>" method="post" name="adminForm" id="adminForm">

<?php if(!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">

			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
			</div>

			<div class="btn-group pull-left">
				<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="icon-search"></i>
				</button>
				<button class="btn hasTooltip" id="clear-search-button" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
					<i class="icon-remove"></i>
				</button>
			</div>

			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible">
					<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>
		<div class="clearfix"> </div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
		<table class="table table-striped" id="hierarchyList">
			<thead>
				<tr>
					<th class='center'>
						<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_HIERARCHYS_USER_ID', 'a.id', $listDirn, $listOrder); ?>
					</th>

					<th class='left'>
						<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_HIERARCHYS_USER_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>

					<th class='left'>
						<?php echo JText::_('COM_HIERARCHY_HIERARCHYS_REPORT_TO'); ?>
					</th>
				</tr>
			</thead>

			<tfoot><?php
					if(isset($this->items[0]))
					{
						$colspan = count(get_object_vars($this->items[0]));
					}
					else
					{
						$colspan = 10;
					}
				?>

				<tr>
					<td colspan="<?php echo $colspan ?>"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php // echo "<pre>"; print_r($this->items); echo "</pre>"; ?>

				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_hierarchy');
					$canEdit    = $user->authorise('core.edit', 'com_hierarchy');
					$canCheckin = $user->authorise('core.manage', 'com_hierarchy');
					$canChange  = $user->authorise('core.edit.state', 'com_hierarchy');

					$bossName = '';

					if ($item->bossId)
					{
						$user = JFactory::getUser($item->bossId);
						$bossName = $user->name;
						$bossId = $item->bossId;
					}
					else
					{
						$bossName = '';
						$bossId = '';
					}?>

					<tr class="row<?php echo $i % 2; ?>">
						<td  class='center'><?php echo $item->subuserId; ?></td>

						<td><?php  echo $item->name; ?></td>

						<td>
							<div class="controls">
								<div class="input-append">
									<input title="Report to id" type="text" class="resizedTextbox" id="jform_user_id_id_<?php echo $i;?>" readonly name="jform[user_id]" placeholder="Id" value="<?php echo $bossId;?>">
									<input title="Report to name"  type="text" id="jform_user_id_<?php echo $i;?>" value="<?php echo $bossName;?>" placeholder="Name" readonly>
									<a class="btn btn-primary modal_jform_user_id  modal" title="Select User." href="<?php echo JRoute::_('index.php?option=com_users&view=users&layout=modal&tmpl=component&field=jform_user_id_'.(int) $i); ?> " rel="{handler: 'iframe', size: {x: 800, y: 500}}">
										<i class="icon-user"></i>
									</a>
								</div>
							</div>
							<input type="hidden" id="subuser_id_<?php echo $i;?>" name="subuser_id_<?php echo $i;?>" value="<?php  echo $item->subuserId; ?>">
						</td>
					</tr><?php

					$content = 'function jSelectUser_jform_user_id_'.$i.'(id, title) {

								var old_id = document.getElementById("jform_user_id_id_'.$i.'").value;
								if (old_id != id) {
									document.getElementById("jform_user_id_id_'.$i.'").value = id;
									document.getElementById("jform_user_id_'.$i.'").value = title;
									document.getElementById("jform_user_id_'.$i.'").className = document.getElementById("jform_user_id_'.$i.'").className.replace(" invalid" , "");
								}
								jModalClose();

								var bossUserId = jQuery("#jform_user_id_id_'.$i.'").val();

								jQuery.ajax(
								{
									url:"index.php?option=com_hierarchy&task=hierarchys.setUser&subuserId='.$item->subuserId.'",
									data:{user_id:bossUserId},
									type:"POST",
									datatype : "json",
									success:function(resp)
									{
										// console.log(resp);
										// js("#order_html").html(data);
									}
								});
							}
							';

					$doc =JFactory::getDocument();
					$doc->addScriptDeclaration( $content );
				endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<div class="bs-callout bs-callout-info" id="callout-xref-input-group">
			<p><?php echo JText::_('COM_HIERARCHY_CSV_HELP_TEXT'); ?></p>
			<p><?php echo JText::_('COM_HIERARCHY_CSV_EXPORT_HELP_TEXT'); ?></p>
			<p><?php echo JText::_('COM_HIERARCHY_CSV_IMPORT_HELP_TEXT'); ?></p>
		</div>

		<input type="hidden" id="task" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<div style="display:none">
	<div id="import_append">
		<form action="<?php echo JUri::base(); ?>index.php?option=com_hierarchy&task=hierarchys.csvImport&tmpl=component&format=html" id="uploadForm" class="form-inline center"  name="uploadForm" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					&nbsp;
				</tr>
				<tr>
					<div id="uploadform">
						<fieldset id="upload-noflash" class="actions">
							<label for="upload-file" class="control-label"><?php echo JText::_('COM_HIERARCHY_UPLOADE_FILE'); ?></label>
							<input type="file" id="upload-file" name="csvfile" id="csvfile" />
							<button class="btn btn-primary" id="upload-submit">
								<i class="icon-upload icon-white"></i>
								<?php echo JText::_('COM_HIERARCHY_IMPORT_CSV'); ?>
							</button>
							<hr class="hr hr-condensed">
							<div class="alert alert-warning" role="alert"><i class="icon-info"></i>
									<?php
									$link = '<a href="' . JUri::root() . 'media/com_hierarchy/samplecsv/userImport.csv' . '">' . JText::_("COM_HIERARCHY_CSV_SAMPLE") . '</a>';
								echo JText::sprintf('COM_HIERARCHY_CSVHELP', $link);
								?>
							</div>
						</fieldset>
					</div>
				</tr>
			</table>
		</form>
	</div>
</div>
