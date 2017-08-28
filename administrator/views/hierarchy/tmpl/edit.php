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
?>
<script type="text/javascript">
	js = jQuery.noConflict();
		js(document).ready(function() {
	});
	Joomla.submitbutton = function(task)
	{
		if (task == 'hierarchy.cancel') {
			Joomla.submitform(task, document.getElementById('hierarchy-form'));
		}
		else {
			if (task != 'hierarchy.cancel' && document.formvalidator.isValid(document.id('hierarchy-form'))) {
				Joomla.submitform(task, document.getElementById('hierarchy-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="hierarchy-form" class="form-validate">
	<div class="form-horizontal">
		<?php
			echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general'));
			echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_HIERARCHY_TITLE_HIERARCHY', true));
		?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
					<?php
						echo $this->form->renderField('users');
						echo $this->form->renderField('context');
						echo $this->form->renderField('context_id');
					?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab');?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" id="user_id" name="user_id" value="<?php  echo $user_id; ?>">
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
