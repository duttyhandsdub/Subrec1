jQuery(document).ready(function(){
	jQuery('.reactivate_trigger').click(function(e){
		e.preventDefault();
		var data = { 
			'action':'reactivate_auto_renew'
		}
		var res = null;
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: "POST",
			success:function(response){
				if(response != 0){
					$('.reactivate_trigger').fadeOut(500, function(){
						$(this).remove();
					});
					$('.subscription_status').html("<span class='label label-success'>Active</span>");
					$('.upgradetrigger').css({
				        opacity: 0,
				        display: 'inline-block'     
				    }).animate({opacity:1},600);
					$('.cancelsubscription').css({
				        opacity: 0,
				        display: 'inline-block'     
				    }).animate({opacity:1},600);
					$('.cancelsubscriptionNow').css({
				        opacity: 0,
				        display: 'inline-block'     
				    }).animate({opacity:1},600);
				}
			}
		});
	});
	jQuery('body').on('click', '.cancel_upgrade_downgrade_trigger', function(e){
		e.preventDefault();
		var data = { 
			'action':'cancel_upgrade_downgrade_subscription'
		}
		var res = null;
	    jQuery.ajax({
	        url: ajaxurl,
	        data: data,
	        type: "POST",
	        success:function(response){
	        	var term = get_user_subscription_term();							        	
	        	if(response == 1){
	        		$('.upgradeInfo').fadeOut();
	        		$('.cancel_upgrade_downgrade_trigger').fadeOut();
	        		$('.upgradetrigger').fadeIn();
	        		if(term != 'yearly'){
	        			$('.subscription_term').append('<a href="/client-area-upgrade-subscription/" class="btn btn-success upgradetrigger">Upgrade Subscription</a>');
	        		}
	        		if(term != 'monthly'){
		        		$('.subscription_term').append('<a href="/client-area-downgrade-subscription/" class="btn btn-success upgradetrigger">Downgrade Subscription</a>');
		        	}
	        		$('.upgrade_cancel_alert').fadeIn();
	        		// animate the alert to height 0 after 3 seconds
					setTimeout(function(){ jQuery('.alert').animate({ height: "0px", padding:"0px", border: "0"}, 400); }, 3000);
	        		//window.location.href = "/client-area-my-subscription/?downgrade_complete=1";
	        	} else {
	        		$('.upgrade_cancel_fail_alert').fadeIn();
	        		// animate the alert to height 0 after 3 seconds
					setTimeout(function(){ jQuery('.alert').animate({ height: "0px", padding:"0px", border: "0"}, 400); }, 3000);
	        	}
	        }
	    });
	});
	function get_user_subscription_term(){
		var term = null;
		var data={
			'action':'get_user_subscription_term'
		}
		var res = null;
		jQuery.ajax({
	        url: ajaxurl,
	        data: data,
	        async: false,
	        type: "POST",
	        success:function(response){
	        	term = response;
	        }
	    });
	    return term;
	}
});