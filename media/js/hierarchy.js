/**
 * @version    SVN: <svn_id>
 * @package    Com_Hierarchy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 **/
var hierarchyAdmin =
{
	hierarchy: {
		/** Initialize hierarchy js **/
		initHierarchyJs: function() {
			jQuery(document).ready(function() {
			});
			Joomla.submitbutton = function (task)
			{
				if (task == 'hierarchy.cancel') {
					Joomla.submitform(task, document.getElementById('hierarchy-form'));
				}
				else {
					if (task != 'hierarchy.cancel' && document.formvalidator.isValid(document.id('hierarchy-form'))) {
						Joomla.submitform(task, document.getElementById('hierarchy-form'));
					}
					else {
						alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
					}
				}
			}
		}
	},

	hierarchys: {
		/** Initialize hierarchys js **/
		initHierarchysJs: function() {
			Joomla.submitbutton = function (task) {
			if (task =='hierarchys.remove') {
				var result = confirm(Joomla.JText._('COM_HIERARCHY_HIERARCHY_DELETE_CONF'));
				if (result != true) {
					return false;
				}
				Joomla.submitform(task, document.getElementById('adminForm'));
				}
			}
		}
	}
}
