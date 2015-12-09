function setDeclineReason(rid,status,ticketidcoded,user)
{
	var reason = jQuery('#reason').val();
	
	if (reason)
	{
		jQuery("#submit").attr("disabled", true);
		jQuery.ajax(
		{
			url:pathroot+'index.php?option=com_hierarchy&task=reschedules.setDeclineReason',
			data:{reason:reason,status:status,rid:rid,ticketidcoded:ticketidcoded,user:user},
			type:'POST',
			success:function(data)
			{
				if (data == 0)
				{
					jQuery("#submit").attr("disabled", false);
					jQuery('#sameemail_error').hide();
				}
				else
				{
					jQuery('#sameemail').hide();
					jQuery('#sameemail_save').show();
					
				}	
			}
		});
	}
	else
	{
		alert('Please enter text.');
	}
}
