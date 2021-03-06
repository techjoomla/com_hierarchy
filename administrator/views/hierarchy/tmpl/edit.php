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
JHtml::_('behavior.keepalive');
$input   = JFactory::getApplication()->input;
$userId = $input->get('user_id', 0);
$JUriRoot = JUri::root(true) . '/administrator/';

// Tokenize
$document = JFactory::getDocument();
$document->addScript(JUri::root(true) . '/media/com_hierarchy/vendors/tokenize/jquery.tokenize.js');
$document->addStylesheet(JUri::root(true) . '/media/com_hierarchy/vendors/tokenize/jquery.tokenize.css');

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
						echo $this->form->renderField('reports_to');
						echo $this->form->renderField('context');
						echo $this->form->renderField('context_id');
					?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab');?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" id="user_id" name="user_id" value="<?php  echo $userId; ?>">
		<input type="hidden" id="created_by" name="created_by" value="<?php  echo $userId; ?>">
		<input type="hidden" id="modified_by" name="modified_by" value="<?php  echo $userId; ?>">
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
	var JUriRoot = "<?php echo $JUriRoot; ?>";
	var client = "<?php echo $this->client; ?>";
	var userID = "<?php echo $userId; ?>";
	var clientID = "<?php echo $this->clientID; ?>";
	hierarchyAdmin.hierarchy.initHierarchyJs();
	hierarchyAdmin.hierarchy.getAutoSuggestUsers();
</script>
