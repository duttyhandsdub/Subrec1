jQuery('body').on('change', '#template_id', function(e){
    var first_name = jQuery(this).data('first_name');
    var term = jQuery(this).data('subscription_term');
    var renewal = jQuery(this).data('subscription_renewal_date');
    var password = jQuery(this).data('password');
    var amount = jQuery(this).data('subscription_amount');
	get_template_content(jQuery(this).val(), first_name, term, renewal, password, amount);
});
function get_template_content(id, first_name, term, renewal, password, amount){
	var data = {
        'action': 'wp_mailgun_get_email_template_content',
        'id': id
    }
    var res = null;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: 'POST',
        success:function(response){
            res = JSON.parse(response)[0];
            if(typeof first_name != "undefined"){
                var body = res.body.replace("{first_name}", first_name);
                body = body.replace("{subscription_term}", term);
                body = body.replace("{subscription_renewal_date}", renewal);
                body = body.replace("{subscription_amount}", amount);
                body = body.replace("{vpn_password}", password);
            } else {
                var body = res.body;
            }
            jQuery('#subject').val(res.subject);
            jQuery('#name').val(res.name);
            tinymce.get('txtMessage').setContent(body);
        }
    });
}
replaceAll = function(string, omit, place, prevstring) {
	if (prevstring && string === prevstring)
		return string;
	prevstring = string.replace(omit, place);
	return replaceAll(prevstring, omit, place, string)
}

jQuery('body').on('click', '.send_email_trigger', function(e){
    e.preventDefault();
    if(jQuery(this).hasClass('active')){
        jQuery('.email_form').animate({ height: '0px' }, 800);
        jQuery(this).removeClass('active');
    } else {
        jQuery('.cancel_area').animate({ height: "0px" }, 800);
        jQuery('.upgradedowngrade_area').animate({ height: "0px" }, 800);
        var height = document.getElementById('email_form').scrollHeight;
        jQuery('.email_form').animate({ height: height }, 800);
        jQuery(this).addClass('active');
    }
});

jQuery('body').on('click', '.close', function(e){
    e.preventDefault();
    var toClose = jQuery(this).parent().parent();
    jQuery(toClose).animate({ height: "0px" }, 800);
    jQuery('.send_email_trigger').attr('disabled','');
});

jQuery('body').on('click', '.upgrade_trigger', function(e){
    e.preventDefault();
    jQuery('.cancel_area').animate({ height: "0px" }, 800);
    jQuery('.email_form').animate({ height: "0px" }, 800);    
    jQuery('.free_area').animate({ height: "0px" }, 800);
    var height = document.getElementById('upgradearea').scrollHeight;
    jQuery('.upgradedowngrade_area').animate({ height: height }, 800);
});

jQuery('body').on('click', '.cancel_trigger', function(e){
    e.preventDefault();
    jQuery('.upgradedowngrade_area').animate({ height: "0px" }, 800);
    jQuery('.email_form').animate({ height: "0px" }, 800);
    jQuery('.free_area').animate({ height: "0px" }, 800);
    var height = document.getElementById('cancelarea').scrollHeight;
    jQuery('.cancel_area').animate({ height: height }, 800);
});

jQuery('body').on('click', '.free_trigger', function(e){
    e.preventDefault();
    jQuery('.upgradedowngrade_area').animate({ height: "0px" }, 800);
    jQuery('.email_form').animate({ height: "0px" }, 800);
    var height = document.getElementById('freearea').scrollHeight;
    jQuery('.free_area').animate({ height: height }, 800);
});


jQuery('input[type="checkbox"]').on('click', function(){
    var checkboxes = document.getElementsByName('user_cb');
    var selected = [];
    for (var i=0; i<checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            selected.push(checkboxes[i].value);
        }
    }
    jQuery('#email_to').val(selected);
});

jQuery('body').on('click', '.email_trigger', function(e){
    e.preventDefault();
    if(jQuery(this).hasClass('active')){
        jQuery('.email_form').animate({ height: '0px' }, 800);
        jQuery(this).removeClass('active');
    } else {
        var height = document.getElementById('email_form').scrollHeight;
        jQuery('.email_form').animate({ height: height }, 800);
        jQuery(this).addClass('active');
    }
});

jQuery('body').on('click', '.edit-btn', function(e){
    e.preventDefault();
    if(jQuery('.subscription_info_table_wrapper').hasClass('inactive')){
        jQuery('.subscription_info_table_wrapper_edit_form').animate({ height: '0px' });
        var height = document.getElementById('subscription_info_table_wrapper').scrollHeight;
        jQuery('.subscription_info_table_wrapper').animate({ height:height});
        jQuery('.subscription_info_table_wrapper').removeClass('inactive');
        jQuery(this).html('Edit');
    } else {
        jQuery('.subscription_info_table_wrapper').animate({ height:'0px'});
        var height = document.getElementById('subscription_info_table_wrapper_edit_form').scrollHeight;
        jQuery('.subscription_info_table_wrapper_edit_form').animate({ height: height });
        jQuery('.subscription_info_table_wrapper').addClass('inactive');
        jQuery(this).html('Cancel Edit');
    }
});

jQuery('body').on('click', '.clearfilters', function(e){
    jQuery('#subscription_status').val('');
    jQuery('#subscription_term').val('');
    jQuery('#subscription_email').val('');
    jQuery('#expire_from').val('');
    jQuery('#expire_to').val('');
});

jQuery('body').on('click', '.givefreesubscription_trigger', function(e){
    e.preventDefault();
    var user_id = jQuery(this).data('user_id');
    var term = jQuery(this).data('term');
    var data = {
        'action': 'give_free_plan',
        'term': term,
        'user_id':user_id
    }
    var res = null;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success:function(response){
        }
    });
});

jQuery('body').on('change', '#voucher_type', function(e){
    var val = jQuery(this).val();
    if(val == "single_usage" || val == "single_user_usage"){
        jQuery('#voucher_single_usage').val('1');
    } else {
        jQuery('#voucher_single_usage').val('');
    }
    if(val == "time_bomb"){
        jQuery('.time_bomb_row').fadeIn();
    } else {
        jQuery('.time_bomb_row').fadeOut(); 
    }
    if(val == "single_user_usage"){
        jQuery('.user_select_row').fadeIn();
    } else {
        jQuery('.user_select_row').fadeOut(); 
    }
    if(val == "usage_limited"){
        jQuery('.usage_limit_row').fadeIn();
    } else {
        jQuery('.usage_limit_row').fadeOut(); 
    }
});

jQuery('body').on('click', '.delete_voucher', function(e){
    e.preventDefault();
    var parent = jQuery(this).parent().parent();
    var id=jQuery(this).data('id');
    var data = {
        'action': 'delete_voucher',
        'id':id
    }
    var res = null;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success:function(response){
           parent.fadeOut();
        }
    });
});

jQuery('body').on('click', '.deactivate_voucher', function(e){
    var id=jQuery(this).data('id');
    var data = {
        'action': 'deactivate_voucher',
        'id':id
    }
    var res = null;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success:function(response){
            if(response == 1){ 
                alert('Voucher deactivated.'); 
                jQuery('#voucher_edit_form').trigger('submit');
            }
        }
    });
});

jQuery('body').on('click', '.reactivate_voucher', function(e){
    var id=jQuery(this).data('id');
    var data = {
        'action': 'reactivate_voucher',
        'id':id
    }
    var res = null;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success:function(response){
            if(response == 1){ 
                alert('Voucher Re-Activated.'); 
                jQuery('#voucher_edit_form').trigger('submit');
            }
        }
    });
});

setTimeout(function(){ jQuery('.alert').animate({ height: "0px", padding:"0px", border: "0"}, 400); }, 3000);