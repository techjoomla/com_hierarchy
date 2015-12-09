jQuery(document).ready(function() {
        jQuery('#pcode').keypress(function (event) {
            
            return isNumber(event, this)

        });

    });
function isNumber(evt, element) {

        var charCode = (evt.which) ? evt.which : event.keyCode

        if (
            (charCode != 44 ) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 8 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;

    }    
function getValue()
{
	var ticketId = jQuery('#ticket_id').val();
	var showbtn = 1;
	if (ticketId)
	{
		jQuery("tbody").empty();
		jQuery.ajax(
		{
			url:'index.php?option=com_hierarchy&task=reschedules.getTicketData&ticketid='+ticketId,
			type:'POST',
			datatype : 'json',
			success:function(data)
			{
					var data = jQuery.parseJSON(data);
					if (data == 1)
					{
						jQuery('#ajaxdiv').hide();
						jQuery('#alredy_div').hide();
						jQuery('#not_found').hide();
						jQuery('#status').hide();
						jQuery("#newTicket").show();
					}
					else if (data == 3)
					{
						jQuery('#ajaxdiv').hide();
						jQuery('#alredy_div').hide();
						jQuery('#not_found').hide();
						jQuery('#status').hide();
						jQuery("#newTicket").hide();
						jQuery("#doneTicket").show();
					}
					else if (data == 2)
					{
						jQuery('#ajaxdiv').hide();
						jQuery('#alredy_div').hide();
						jQuery('#not_found').show();
						jQuery('#status').hide();
						jQuery("#newTicket").hide();
						jQuery("#doneTicket").hide();
					}
					else
					{
						jQuery("#newTicket").hide();
						jQuery("#doneTicket").hide();
						jQuery('#not_found').hide();
					jQuery.each(data, function(k, v) {
						jQuery("#"+k+"_label").html('');
						jQuery("#"+k+"_label").html(v);
						jQuery('input[name="'+k +'"]').val('');
						jQuery('input[name="'+k +'"]').val(v);

						if (k === 'daysleft')
						{
							if(v >= 0)
							{
								jQuery( "#days" ).html( v );
								jQuery("#positive").show();
								jQuery("#reschedule_btn").show();
								//...Do stuff for +ve num
								jQuery("#negative").hide();
							}else{
								//console.log(2);
								showbtn = 0;
								jQuery("#reschedule_btn").hide();
								   ///...Do stuff -ve num
								jQuery( "#days" ).html( '' );
								jQuery("#positive").hide();
								jQuery("#negative").show();
								
							}  
						}
						});

						if (data['show_reschedule_btn'] == '0')
						{
							jQuery("#reschedule_btn").hide();
							jQuery('#status').show();
							jQuery('#ajaxdiv').hide();
							jQuery("#newTicket").hide();
							jQuery("#doneTicket").hide();
						}
						else
						{
							jQuery('#ajaxdiv').show();
							jQuery('#status').hide();
						}
						if (data['bh'])
						{
							jQuery("#reschedule_btn").show();
							jQuery("#bhnot").hide();
						}
						else
						{
							jQuery("#reschedule_btn").hide();
							jQuery("#bhnot").show()
						}
						if (showbtn == 0)
						{
							jQuery("#reschedule_btn").hide();
						}
						jQuery('#alredy_div').hide();
						jQuery('#not_found').hide();
						if (data['already'] == '1')
						{
							jQuery('#alredy_div').show();
							//jQuery('#ajaxdiv').hide();
							jQuery.each(data, function(k, v) {
							if (k === 'tr')
								{
									if (v == 0)
									{
										jQuery("#table_header").hide();
										jQuery('.table').hide();
									}
									else
									{
										jQuery('.table').append(v);
									}
								}
							});
							jQuery("#newTicket").hide();
						}
					}
			}
		});
	}
	else
	{
		alert('Please enter ticket id.');
	}
}
