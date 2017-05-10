<?php
function setup_vpn_templates_page() {
    add_menu_page("Email Templates", "Email Templates", 'manage_options', "vpn_email_templates", "vpn_email_templates");
}

add_action("admin_menu", "setup_vpn_templates_page");


function vpn_email_templates(){
	global $wpdb;
	global $table_prefix;
	$sqlt = "SELECT * FROM ".$table_prefix."email_templates";
	$rest = $wpdb->get_results($sqlt);
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
		<style>
		.update-nag{ display:none;}
		.list-group-item{
			padding: 5px;
		    border-radius: 4px;
		    background: #015A82;
		    color: #fff;
		    cursor:pointer;
		}
		.alert-success {
		    color: #3c763d;
		    background-color: #dff0d8;
		    border-color: #d6e9c6;
		}
		.alert {
		    padding: 15px;
		    margin-bottom: 20px;
		    border: 1px solid transparent;
		    border-radius: 4px;
		}
		.alert-danger {
		    color: #a94442;
		    background-color: #f2dede;
		    border-color: #ebccd1;
		}
		.modalDialog {
		    overflow: hidden;
		    position: fixed;
		    font-family: Arial, Helvetica, sans-serif;
		    top: 0;
		    right: 0;
		    bottom: 0;
		    left: 0;
		    background: rgba(0,0,0,0.8);
		    z-index: 99999;
		    opacity:0;
		    -webkit-transition: opacity 400ms ease-in;
		    -moz-transition: opacity 400ms ease-in;
		    transition: opacity 400ms ease-in;
		    pointer-events: none;
		}
		.modal_active{
		    pointer-events: initial;
		}
		.modalDialog:target {
		    opacity:1;
		    pointer-events: auto;
		}

		.modalDialog > div {
		    width: 600px;
		    height:auto;
		    max-height:700px;
		    overflow-y:auto;
		    position: relative;
		    margin: 10% auto;
		    padding: 10px 10px 13px 10px;
		    border-radius: 10px;
		    background: #fff;
		    background: -moz-linear-gradient(#fff, #999);
		    background: -webkit-linear-gradient(#fff, #999);
		    background: -o-linear-gradient(#fff, #999);
		}
		.close {
			background: #606061;
			color: #FFFFFF;
			line-height: 25px;
			position: absolute;
			right: 0px;
			text-align: center;
			top: 0px;
			width: 24px;
			text-decoration: none;
			font-weight: bold;
			-webkit-border-radius: 12px;
			-moz-border-radius: 12px;
			border-radius: 12px;
			-moz-box-shadow: 1px 1px 3px #000;
			-webkit-box-shadow: 1px 1px 3px #000;
			box-shadow: 1px 1px 3px #000;
		}

		.close:hover { background: #00d9ff; }
		.loading{left:45% !important;}
        .btn{display:inline-block;padding:6px 12px;margin-bottom:0;font-size:14px;font-weight:400;line-height:1.42857143;text-align:center;white-space:nowrap;vertical-align:middle;-ms-touch-action:manipulation;touch-action:manipulation;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background-image:none;border:1px solid transparent;border-radius:4px}.btn-success{color:#fff;background-color:#5cb85c;border-color:#4cae4c}.btn-danger{margin-left:10px;color:#fff;background-color:#d9534f;border-color:#d43f3a}.btn-warning{color:#fff;background-color:#f0ad4e;border-color:#eea236}.btn-primary{color:#fff;background-color:#337ab7;border-color:#2e6da4}.btn-warning.active,.btn-warning.focus,.btn-warning:active,.btn-warning:focus,.btn-warning:hover,.open>.dropdown-toggle.btn-warning{color:#fff;background-color:#ec971f;border-color:#d58512}.table{width:100%;max-width:100%;margin-bottom:20px}.table>tbody>tr>td,.table>tbody>tr>th,.table>tfoot>tr>td,.table>tfoot>tr>th,.table>thead>tr>td,.table>thead>tr>th{padding:8px;line-height:1.42857143;vertical-align:top;border-top:1px solid #ddd}.table>thead>tr>th{vertical-align:bottom;border-bottom:2px solid #ddd}.table>caption+thead>tr:first-child>td,.table>caption+thead>tr:first-child>th,.table>colgroup+thead>tr:first-child>td,.table>colgroup+thead>tr:first-child>th,.table>thead:first-child>tr:first-child>td,.table>thead:first-child>tr:first-child>th{border-top:0}.table>tbody+tbody{border-top:2px solid #ddd}.table .table{background-color:#fff}.table-condensed>tbody>tr>td,.table-condensed>tbody>tr>th,.table-condensed>tfoot>tr>td,.table-condensed>tfoot>tr>th,.table-condensed>thead>tr>td,.table-condensed>thead>tr>th{padding:5px}.table-bordered,.table-bordered>tbody>tr>td,.table-bordered>tbody>tr>th,.table-bordered>tfoot>tr>td,.table-bordered>tfoot>tr>th,.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border:1px solid #ddd}.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border-bottom-width:2px}.table-striped>tbody>tr:nth-of-type(odd){background-color:#f9f9f9}.table-hover>tbody>tr:hover{background-color:#f5f5f5}table col[class*=col-]{position:static;display:table-column;float:none}table td[class*=col-],table th[class*=col-]{position:static;display:table-cell;float:none}.table>tbody>tr.active>td,.table>tbody>tr.active>th,.table>tbody>tr>td.active,.table>tbody>tr>th.active,.table>tfoot>tr.active>td,.table>tfoot>tr.active>th,.table>tfoot>tr>td.active,.table>tfoot>tr>th.active,.table>thead>tr.active>td,.table>thead>tr.active>th,.table>thead>tr>td.active,.table>thead>tr>th.active{background-color:#f5f5f5}.table-hover>tbody>tr.active:hover>td,.table-hover>tbody>tr.active:hover>th,.table-hover>tbody>tr:hover>.active,.table-hover>tbody>tr>td.active:hover,.table-hover>tbody>tr>th.active:hover{background-color:#e8e8e8}.table>tbody>tr.success>td,.table>tbody>tr.success>th,.table>tbody>tr>td.success,.table>tbody>tr>th.success,.table>tfoot>tr.success>td,.table>tfoot>tr.success>th,.table>tfoot>tr>td.success,.table>tfoot>tr>th.success,.table>thead>tr.success>td,.table>thead>tr.success>th,.table>thead>tr>td.success,.table>thead>tr>th.success{background-color:#dff0d8}.table-hover>tbody>tr.success:hover>td,.table-hover>tbody>tr.success:hover>th,.table-hover>tbody>tr:hover>.success,.table-hover>tbody>tr>td.success:hover,.table-hover>tbody>tr>th.success:hover{background-color:#d0e9c6}.table>tbody>tr.info>td,.table>tbody>tr.info>th,.table>tbody>tr>td.info,.table>tbody>tr>th.info,.table>tfoot>tr.info>td,.table>tfoot>tr.info>th,.table>tfoot>tr>td.info,.table>tfoot>tr>th.info,.table>thead>tr.info>td,.table>thead>tr.info>th,.table>thead>tr>td.info,.table>thead>tr>th.info{background-color:#d9edf7}.table-hover>tbody>tr.info:hover>td,.table-hover>tbody>tr.info:hover>th,.table-hover>tbody>tr:hover>.info,.table-hover>tbody>tr>td.info:hover,.table-hover>tbody>tr>th.info:hover{background-color:#c4e3f3}.table>tbody>tr.warning>td,.table>tbody>tr.warning>th,.table>tbody>tr>td.warning,.table>tbody>tr>th.warning,.table>tfoot>tr.warning>td,.table>tfoot>tr.warning>th,.table>tfoot>tr>td.warning,.table>tfoot>tr>th.warning,.table>thead>tr.warning>td,.table>thead>tr.warning>th,.table>thead>tr>td.warning,.table>thead>tr>th.warning{background-color:#fcf8e3}.table-hover>tbody>tr.warning:hover>td,.table-hover>tbody>tr.warning:hover>th,.table-hover>tbody>tr:hover>.warning,.table-hover>tbody>tr>td.warning:hover,.table-hover>tbody>tr>th.warning:hover{background-color:#faf2cc}.table>tbody>tr.danger>td,.table>tbody>tr.danger>th,.table>tbody>tr>td.danger,.table>tbody>tr>th.danger,.table>tfoot>tr.danger>td,.table>tfoot>tr.danger>th,.table>tfoot>tr>td.danger,.table>tfoot>tr>th.danger,.table>thead>tr.danger>td,.table>thead>tr.danger>th,.table>thead>tr>td.danger,.table>thead>tr>th.danger{background-color:#f2dede}.table-hover>tbody>tr.danger:hover>td,.table-hover>tbody>tr.danger:hover>th,.table-hover>tbody>tr:hover>.danger,.table-hover>tbody>tr>td.danger:hover,.table-hover>tbody>tr>th.danger:hover{background-color:#ebcccc}.table-responsive{min-height:.01%;overflow-x:auto}@media screen and (max-width:767px){.table-responsive{width:100%;margin-bottom:15px;overflow-y:hidden;-ms-overflow-style:-ms-autohiding-scrollbar;border:1px solid #ddd}.table-responsive>.table{margin-bottom:0}.table-responsive>.table>tbody>tr>td,.table-responsive>.table>tbody>tr>th,.table-responsive>.table>tfoot>tr>td,.table-responsive>.table>tfoot>tr>th,.table-responsive>.table>thead>tr>td,.table-responsive>.table>thead>tr>th{white-space:nowrap}.table-responsive>.table-bordered{border:0}.table-responsive>.table-bordered>tbody>tr>td:first-child,.table-responsive>.table-bordered>tbody>tr>th:first-child,.table-responsive>.table-bordered>tfoot>tr>td:first-child,.table-responsive>.table-bordered>tfoot>tr>th:first-child,.table-responsive>.table-bordered>thead>tr>td:first-child,.table-responsive>.table-bordered>thead>tr>th:first-child{border-left:0}.table-responsive>.table-bordered>tbody>tr>td:last-child,.table-responsive>.table-bordered>tbody>tr>th:last-child,.table-responsive>.table-bordered>tfoot>tr>td:last-child,.table-responsive>.table-bordered>tfoot>tr>th:last-child,.table-responsive>.table-bordered>thead>tr>td:last-child,.table-responsive>.table-bordered>thead>tr>th:last-child{border-right:0}.table-responsive>.table-bordered>tbody>tr:last-child>td,.table-responsive>.table-bordered>tbody>tr:last-child>th,.table-responsive>.table-bordered>tfoot>tr:last-child>td,.table-responsive>.table-bordered>tfoot>tr:last-child>th{border-bottom:0}.corner-ribbon.shadow{box-shadow:0 0 3px rgba(0,0,0,.3)}.corner-ribbon.top-left{top:20px;left:-31px;transform:rotate(-45deg);-webkit-transform:rotate(-45deg)}.corner-ribbon.white{background:#f0f0f0;color:#555}.corner-ribbon.black{background:#333}.corner-ribbon.grey{background:#999}.corner-ribbon.blue{background:#39d}.corner-ribbon.green{background:#2c7}.corner-ribbon.turquoise{background:#1b9}.corner-ribbon.purple{background:#95b}.corner-ribbon.red{background:#e17c00}.corner-ribbon.orange{background:#e82}.corner-ribbon.yellow{background:#ec0}}
        </style>
        <div id="openModal" class="modalDialog">
			<div>
				<a href="#close_modal" title="Close" class="close_modal">X</a>
				<?php echo get_email_header(); ?>
				<div class='emailbody'>
				</div>
				<?php echo get_email_footer(); ?>

			</div>
		</div>
        <div style='width:100%;height:auto;background:#fff;margin-left: -20px; padding-left: 20px;'>
            <div class='logo'><img src='<?php echo get_template_directory_uri();?>/images/vpn-view-logo.png' style="width: 295px;padding:20px;"></div>
        </div>
		<div class='row' style='box-sizing:border-box; padding-right:20px;'>
	        <h1 style='color:#4186c6;font-weight:bold;'>Email Templates</h1>
	        <div class='notify'>

	        </div>
	        <h3 style='margin-bottom:5px;color:#4186c6;'>Select a Template:</h3>
	        <select name='template_id' id='template_id'>
				<option value=''>Please select a Template</option>
				<?php foreach($rest as $t){
					?>
					<option value='<?php echo $t->id;?>'><?php echo $t->name; ?></option>
					<?php
				}
				?>
	        </select> 
        </div>
        <div style='clear:both;'></div>
        <div class='row clearfix' style='box-sizing:border-box; padding-right:20px;'>
        	<h3 style='margin-bottom:5px;color:#4186c6;'>Edit/Create Template:</h3>
			<div style='width:75%;float:left;padding-top:0px;padding-bottom:10px;padding-right:10px;box-sizing:border-box;'>
				<label for='name'>Template Name</label>
				<input type='text' name='name' id='name' style='width:100%;margin-bottom:10px;border-radius:4px;padding:10px;'>
				<label for='subject'>Email Subject</label>
				<input type='text' name='subject' id='subject' style='width:100%;margin-bottom:10px;border-radius:4px;padding:10px;'>
				<?php
				$settings = array( 'textarea_name' => 'txtMessage',array('tinymce'=>true),'quicktags'=>false );
							wp_editor( '', 'txtMessage', $settings );
				?>
			</div>
			<div style='width:25%;float:left;padding:10px;box-sizing:border-box;'>
				<div class="panel panel-default ">
					<div class="panel-heading"><h3 style='margin-bottom:5px;color:#4186c6;'>Actions</h3></div>
					<div class='panel-body' style="text-align:right;">
						<a href='#' class='btn btn-success savebtn' style='text-decoration:none; display:block;'>Save</a><br/><hr/>
						<a href='#openModal' class="btn btn-primary prevbtn" id="preview-button"  style='text-decoration:none;  display:block'/>Preview Email</a><br/><hr/>
						<a href='#' class="btn btn-danger deletebtn" style='margin-left:0px;text-decoration:none;  display:block' id="delete-button" />Delete Template</a><br/><hr/>
					</div>
				</div>
			</div>
			<div style='clear:both;'></div>
		</div>
		<div style='clear:both;'></div>
		<script>
		jQuery('body').on('click', '.prevbtn', function(e){
			e.preventDefault();
		    var height = jQuery(window).height();
		    jQuery('.modalDialog').addClass('modal_active');
		    jQuery('.modalDialog').animate({ height: '100%', opacity: '1' }, 800);
		});

		jQuery('body').on('click', '.close_modal', function(e){
		    e.preventDefault();
		    var toClose = jQuery(this).parent().parent();
		    jQuery('.modalDialog').removeClass('modal_active');
		    jQuery(toClose).animate({ height: "0px" }, 800);
		    jQuery('.send_email_trigger').attr('disabled','');
		});
		jQuery('body').on('change', '#template_id', function(e){
			get_template_content(jQuery(this).val());
		});
		function get_template_content(id){
			var data = {
		        'action': 'get_email_template_content',
		        'id': id
		    }
		    var res = null;
		    jQuery.ajax({
		        url: ajaxurl,
		        data: data,
		        type: "POST",
		        success:function(response){
		            res = JSON.parse(response)[0];
		            jQuery('#subject').val(res.subject);
		            jQuery('#name').val(res.name);
		            console.dir(res.body);
		            tinymce.get('txtMessage').setContent(res.body);
		        }
		    });
		}
		replaceAll = function(string, omit, place, prevstring) {
		  if (prevstring && string === prevstring)
		    return string;
		  prevstring = string.replace(omit, place);
		  return replaceAll(prevstring, omit, place, string)
		}
		jQuery('body').on('click', '.prevbtn', function(e){
			var content = tinyMCE.get('txtMessage').getContent();
			content = replaceAll(content, '[User First Name]', 'Mark');
			content = replaceAll(content, '[Today Date]', '<?php echo date("l jS \of F Y");?>');
			content = replaceAll(content, '[User Last Name]', 'Alexander');
			content = replaceAll(content, '[User Email]', 'mark@property118.com');
			content = replaceAll(content, '[User Telephone]', '01603 428501');
			content = replaceAll(content, '[Owner/Agent First Name]', 'Mark');
			content = replaceAll(content, '[Owner First Name]', 'Mark');
			content = replaceAll(content, '[First_Name]', 'Mark');
			content = replaceAll(content, '[Surname]', 'Alexander');
			content = replaceAll(content, '[Owner Last Name]', 'Alexander');
			content = replaceAll(content, '[Owner/Agent Last Name]', 'Alexander');
			content = replaceAll(content, '[DATE]', '<?php echo date("d-m-Y"); ?>');
			content = replaceAll(content, '[House Name/No]', '94b');
			content = replaceAll(content, '[Street Name]', 'St Benedicts');
			content = replaceAll(content, '[City]', 'Norwich');
			content = replaceAll(content, '[County]', 'Norfolk');
			content = replaceAll(content, '[Postcode]', 'NR2 4AB');
			content = replaceAll(content, '[Telephone]', '01603 428501');
			content = replaceAll(content, '[Mobile]', '07834 754223');
			content = replaceAll(content, '[Bathrooms]', '2');
			content = replaceAll(content, '[Bedrooms]', '3');
			content = replaceAll(content, '[Property Rent PCM]', '1200');
			content = replaceAll(content, '[Property Price]', '180000');
			content = replaceAll(content, '[Email]', 'mark@property118.com');
			content = replaceAll(content, '[Owner/Agent Email]', 'mark@property118.com');
			content = replaceAll(content, '[Owner/Agent Landline]', '07834 754223');
			content = replaceAll(content, '[Owner/Agent Mobile]', 'mark@property118.com');
			content = replaceAll(content, '[Property Type]', 'House');
			content = replaceAll(content, '[PROPERTY URL]', '<b><a href="#">Click Here</a></b>');
			content = replaceAll(content, '[PAID PROPERTY URL]', '<b><a href="#">Click Here</a></b>');
			var yield = ((1200 * 12) / 180000) * 100;
			content = replaceAll(content, '[YIELD]', yield+'%');
			content = replaceAll(content, '[Yield]', yield+'%');
			content = replaceAll(content, '[Distance]', '25');
			content = replaceAll(content, '[Alert Postcode]', 'NR2 4AB');
			content = replaceAll(content, '[Reason For Listing]', 'New Development');
			
			jQuery('.emailbody').html(content);
		});
		jQuery('#ClickWordList li').click(function(ev, ui) { 
			data = '['+jQuery(this).text()+']';
			tinyMCE.execCommand('mceInsertContent',true,data);
			return false
		});
		jQuery("#DragWordList li").draggable({helper: 'clone'});
		jQuery('.deletebtn').click(function(){
			var data = {
		        'action': 'delete_email_template_data',
		        'id': jQuery('#template_id').val()
		    }
		    var res = null;
		    jQuery.ajax({
		        url: ajaxurl,
		        data: data,
		        type: "POST",
		        success:function(response){
		        	if(response == 1){
		        		location.reload();
		        	}
		        	
		        }
		    });
		});
		jQuery('.savebtn').click(function(){
			var data = {
		        'action': 'save_email_template_data',
		        'id': jQuery('#template_id').val(),
		        'body':tinyMCE.get('txtMessage').getContent(),
		        'subject':jQuery('#subject').val(),
		        'name':jQuery('#name').val(),
		        'type':jQuery('#type').val()
		    }
		    var res = null;
		    jQuery.ajax({
		        url: ajaxurl,
		        data: data,
		        type: "POST",
		        success:function(response){
		        	jQuery('.notify').html('');
		        	if(response == 1){
		        		jQuery('.notify').append('<div class="alert alert-success" role="alert"><b>Congratulations</b> we have successfully saved your template.</div>');
		        	} else {
		        		jQuery('.notify').append('<div class="alert alert-danger" role="alert"><b>Sorry</b> but there was an error saved your template.</div>');
		        	}
		        	jQuery('.notify').fadeIn();
		        	setTimeout(function() {
					    jQuery('.notify').fadeOut('fast');
					}, 10000);
		        }
		    });
		});
		</script>
        <?php
        
}

add_action('wp_ajax_nopriv_get_email_template_content', 'get_email_template_content');
add_action('wp_ajax_get_email_template_content', 'get_email_template_content');
function get_email_template_content(){
	$id = $_POST['id'];
	global $wpdb;
	global $table_prefix;
	$sql = "SELECT * FROM ".$table_prefix."email_templates WHERE id=".$id;
	$result = $wpdb->get_results($sql);
	echo json_encode($result);
	die;

}

add_action('wp_ajax_nopriv_delete_email_template_data', 'delete_email_template_data');
add_action('wp_ajax_delete_email_template_data', 'delete_email_template_data');
function delete_email_template_data(){
	$id = $_POST['id'];
	global $wpdb;
	global $table_prefix;
	$sql = "DELETE FROM ".$table_prefix."email_templates WHERE id=".$id;
	$res = $wpdb->get_results($sql);
	echo 1;
	die;
}

add_action('wp_ajax_nopriv_save_email_template_data', 'save_email_template_data');
add_action('wp_ajax_save_email_template_data', 'save_email_template_data');
function save_email_template_data(){
	global $table_prefix;
	$id = $_POST['id'];
	$body = $_POST['body'];
	$subject = $_POST['subject'];
	$name = $_POST['name'];
	$type = $_POST['type'];
	if(isset($id)){
		if($id != null && $id != ""){
			//sql to update
			$sql = "UPDATE ".$table_prefix."email_templates SET body='".$body."', name='".$name."', subject='".$subject."' WHERE id=".$id;
		} else {
			//sql to save new
			$sql = "INSERT INTO ".$table_prefix."email_templates (body, name, subject) VALUES ('".$body."', '".$name."', '".$subject."')";
		}
	} else {
		$sql = "INSERT INTO ".$table_prefix."email_templates (body, name, subject) VALUES ('".$body."', '".$name."', '".$subject."')";
	}
	global $wpdb;
	$wpdb->get_results($sql);
	if ($wpdb->last_error) {
	  echo 'You done bad! ' . $wpdb->last_error;
	} else {
		echo 1;
	}
	die;
}

function get_email_header(){
	$header = "<table style='width:100%;border-bottom: 1px solid #d4d4d4;' class='' align='center' border='0' cellpadding='0' cellspacing='0'>
    <tbody>
        <tr>
            <td align='center' style='background-color: #fff;'>
                <table style='width: 600px;' cellpadding='0' cellspacing='0'>
                    <tbody>
                        <tr>
                            <td style='padding:20px;'>
                                <a href='".get_option(' siteurl ')."' style='display:block; border:none;'>
                                	<img src='".get_template_directory_uri()."/images/vpn-view-logo.png' width='295px'  style='display:block; border:none; margin:10px;'>
                            	</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<br/>
<table style='width: 560px;background: #fff;padding: 20px;' class='' align='center' border='0' cellpadding='0' cellspacing='0'>
    <tbody>
        <tr>
            <td align='center'>
                <table style='width: 560px;' border='0' cellpadding='0' cellspacing='0'>
                    <tbody>
                        <tr>
                            <td>";
	return $header;
}
function get_email_footer(){
	               $footer = "<br/>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
<table style='width:100%;' class='' align='center' border='0' cellpadding='0' cellspacing='0'>
    <tbody>
        <tr>
            <td align='center' style='background-color: #4186c6;'>
                <table style='width: 600px;' border='0' cellpadding='0' cellspacing='0'>
                    <tbody>
                        <tr>
                            <td style='padding:5px 20px 15px 20px;'>
                                <p style='font-family:Arial, sans-serif; line-height: 18px; font-size:12px; margin:0; padding:0; color:#ffffff;'>&copy;
                                    <?= date('Y');?> <a href='".get_option(' siteurl ')."' style='color:#fff; text-decoration:none;'>VPNView</a></p>
                            </td>
                            <td style='padding:5px 20px 15px 20px;' align='right'>
                                
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>";
	return $footer;
}


?>