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
				if (client){
					jQuery('#jform_context').prop('readonly', true);
				}
				if (clientID){
					jQuery('#jform_context_id').prop('readonly', true);
				}
			});
			Joomla.submitbutton = function (task) {
				if (task == 'hierarchy.cancel') {
					Joomla.submitform(task, document.getElementById('hierarchy-form'));
				}
				else {
					if (task != 'hierarchy.cancel' && document.formvalidator.isValid(document.id('hierarchy-form'))) {
						Joomla.submitform(task, document.getElementById('hierarchy-form'));
					}
					else {
						alert(Joomla.JText._('JGLOBAL_VALIDATION_FORM_FAILED'));
						return false;
					}
				}
				if (task == 'hierarchy.apply') {
					var validData = document.formvalidator.isValid(document.getElementById('hierarchy-form'));
					if(validData == true) {
						Joomla.submitform(task, document.getElementById('hierarchy-form'));
					}
					window.parent.location.reload();
				}
			}
		},

		getUsersToManageHierarchy: function() {
			/** Invite user field tokenfield **/
			inviteTaskUrl = JUriRoot + 'index.php?option=com_hierarchy&task=hierarchy.getUsersToManageHierarchy&user_id=' + userID;

			jQuery('#jform_user_id').tokenize({
				placeholder: Joomla.JText._('COM_HIERARCHY_USERNAMES_DESC'),
				newElements: false,
				searchMinLength: 1,
				datas: inviteTaskUrl
			});
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
		},
		showUserNames: function() {
			var maxRow = jQuery(".reports_to").length;
			for(var i = 0; i < maxRow ; i++) {
				jQuery("#popover_"+i).popover({ trigger: "hover" });
			}
		}
	}
}

var hierarchySite =
{
	hierarchys: {
		showUserNames: function() {
			var maxRow = jQuery(".reports_to").length;
			for(var i = 0; i < maxRow ; i++) {
				jQuery("#popover_"+i).popover({ trigger: "hover" });
			}
		},
		drillUpDrillDownList: function() {

			var hierarchysData = JSON.parse(hierarchys);

			jQuery.each(hierarchysData, function (key, val) {

				reportsUrl = JUriRoot + 'index.php?option=com_hierarchy&task=hierarchys.getAlsoReportsTo&user_id=' + val.user_id;

				jQuery.ajax({
					type:'POST',
					url:reportsUrl,
					data:hierarchys,
					dataType: 'json',
					success:function(data)
					{
						jQuery.each(data, function (dataKey, DataVal) {

							var alsoRepoToList = new Array();
							jQuery.each(DataVal.also, function(index, value) {
								alsoRepoToList.push(value.reportsToName);
							});

							jQuery('#hierarchyList').append('<tr><td><img src="' + gravatar + '" class="img-rounded" alt="" width="30" height="30"><a href="#">' + DataVal.name +' </i><i class="fa fa-angle-down" onclick="hierarchySite.hierarchys.drillUpDrillDownList()"; aria-hidden="true"></i> </td><td>' + DataVal.context + '</td><td>' + DataVal.context_id + '</td><td><span id="popover" data-content="'+alsoRepoToList+'">'+alsoRepoToList+'</span></td><td>' + DataVal.user_id + '</td></tr>');

							jQuery("#popover").popover({ trigger: "hover" });
						});
					}
				});
			});
		}
	}
}
