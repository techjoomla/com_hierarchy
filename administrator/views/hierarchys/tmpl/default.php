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
<?php
	if(!empty($this->sidebar)):
		?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php
	else :
		?>
		<div id="j-main-container">
		<?php
	endif;

	// Search tools bar
	echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
	?>
	<div class="clearfix"> </div>
	<?php
		if (empty($this->items)) :
			?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
			<?php
		else :
		?>
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
						<?php echo JText::_('COM_HIERARCHY_CONTEXT'); ?>
					</th>
					<th class='left'>
						<?php echo JText::_('COM_HIERARCHY_HIERARCHYS_REPORT_TO'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
			<?php
				if (isset($this->items[0]))
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
			<?php
				foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.+ordering');
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
					}
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td  class='center'><?php echo $item->subuserId; ?></td>
						<td><?php  echo $item->name; ?></td>
						<td>
						<?php
							if (!empty($item->context))
							{
								echo $item->context;
							}
							else
							{
								echo '-';
							}
							?></td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_hierarchy&view=hierarchy&layout=edit');?>" class="btn button btn-success modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}">
								<span class="icon icon-users"></span>
								<?php echo JText::_('Set Managers');?>
							</a>
						</td>
					</tr>
					<?php
				endforeach;
				?>
			</tbody>
		</table>
		<?php
		endif;
		?>
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
							<label for="upload-file" class="control-label">
								<?php echo JText::_('COM_HIERARCHY_UPLOADE_FILE'); ?>
							</label>
							<input type="file" id="upload-file" name="csvfile" id="csvfile" />
							<button class="btn btn-primary" id="upload-submit">
								<i class="icon-upload icon-white"></i>
								<?php echo JText::_('COM_HIERARCHY_IMPORT_CSV'); ?>
							</button>
							<hr class="hr hr-condensed">
							<div class="alert alert-warning" role="alert">
								<i class="icon-info"></i>
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
<script>
jQuery("[name='jform[user_id]']").on("change",function(){
	var bossUserId = jQuery(this).val();
	var subuserId = jQuery(this).parents('tr').find('input[id^="subuser_id_"]').val();
	jQuery.ajax(
	{
		url:"<?php echo Juri::base();?>index.php?option=com_hierarchy&task=hierarchys.setUser&subuserId=" + subuserId,
		data:{user_id:bossUserId},
		type:"POST",
		datatype : "json",
		success:function(resp)
		{
			console.log('Manager Set');
		},
		error: function(XMLHttpRequest, textStatus, errorThrown)
		{
			console.log('Something went wrong.');
		}
	});
});
</script>
