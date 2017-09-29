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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$input   = JFactory::getApplication()->input;
$user_id = $input->get('user_id', 0);

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_hierarchy/assets/css/hierarchy.css');
$rootUrl = JUri::root() . '/administrator/';

// Call helper function
HierarchyHelper::getLanguageConstant();
?>
<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="hierarchy-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
					<?php
						echo $this->form->renderField('user_id');
						echo $this->form->renderField('context');
						echo $this->form->renderField('context_id');
					?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab');?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" id="user_id" name="user_id" value="<?php  echo $user_id; ?>">
		<input type="hidden" id="created_by" name="created_by" value="<?php  echo $user_id; ?>">
		<input type="hidden" id="modified_by" name="modified_by" value="<?php  echo $user_id; ?>">
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
	var rootUrl = "<?php echo $rootUrl; ?>";
	var client = "<?php echo $this->client; ?>";
	var clientID = "<?php echo $this->clientID; ?>";
	hierarchyAdmin.hierarchy.initHierarchyJs();
</script>
