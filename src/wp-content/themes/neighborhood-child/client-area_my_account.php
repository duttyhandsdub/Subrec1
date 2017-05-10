<?php
/**
 * Template Name: Client Area - My Account Profile Page
 *
 * My Account Profile Page Allow users to update their profiles from Frontend.
 *
 */
/* Get user info. */
global $current_user, $wp_roles;
//get_currentuserinfo(); //deprecated since 3.1

/* Load the registration file. */
$error = array();    
/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {
	$old_email = $current_user->user_email;
    /* Update user password. */
    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
        if ( $_POST['pass1'] == $_POST['pass2'] )
            wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
        else
            $error["password"] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
    }

    /* Update user information. */
    if ( !empty( $_POST['url'] ) )
        wp_update_user( array( 'ID' => $current_user->ID, 'user_url' => esc_url( $_POST['url'] ) ) );
    if ( !empty( $_POST['email'] ) ){
        if (!is_email(esc_attr( $_POST['email'] )))
            $error["email"] = 'The Email you entered is not valid.  please try again.';
        elseif(email_exists(esc_attr( $_POST['email'] )) != $current_user->id )
            $error["email"] = 'This email is already used by another user.  try a different one.';
        else{
            wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
            //we need to update radius here.... or they will have differing radius/wp logins.
            update_radius_email(esc_attr($_POST['email']), $old_email);
        }
    }

    if ( !empty( $_POST['first_name'] ) ){
        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first_name'] ) );
    } else {
    	$error['first_name'] = "The First Name cannot be blank";
    }
    if ( !empty( $_POST['last_name'] ) ){
        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last_name'] ) );
    } else {
    	$error['last_name'] = "The Last Name cannot be blank";
    }
    if ( !empty( $_POST['description'] ) ){
        update_user_meta( $current_user->ID, 'description', esc_attr( $_POST['description'] ) );
    } 


    /* DEAL WITH THE AVATAR */
    if($_FILES["avatar"]["name"] != ''){ 
    	$user = wp_get_current_user();
    	$id = $user->ID;
        $uploadedfile = $_FILES['avatar'];
        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $uploadedfile = $_FILES['avatar'];
        $upload_overrides = array( 'test_form' => false );
        // These files need to be included as dependencies when on the front end.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        $attachment_id = media_handle_upload( 'avatar', 0 );
        $attachment = get_post($attachment_id);
        if ( is_wp_error( $attachment_id ) ) {
            // There was an error uploading the image.
            $fail = true;
        } else {
            // The image was uploaded successfully!
            $attach_data = wp_generate_attachment_metadata( $attachment_id, $attachment->guid );
            wp_update_attachment_metadata( $attachment_id, $attach_data );
            $imgurl = wp_get_attachment_url( $attachment_id );
            if(! update_user_meta($id, "avatar", $imgurl)){
                $fail = true;
            }
        }
    }

    /* Redirect so the page will show updated info.*/
  /*I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ */
    if ( count($error) == 0 ) {
        //action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink() );
        exit;
    }
}

get_header(); ?>
<style>
.content{ text-align:left; }
label { width:100%; display:block; }
input{ width:100%; }
textarea{ width:100%; }
.form-submit{ margin-top:30px; }
.my_account_top_title{
	color:#000;
	font-weight:200;
	font-size:20px;
}
.my_account_sub_title{
	margin-bottom:0px !important;
}
.nosidepadding{
	padding-left:0px !important;
	padding-right:0px !important;
}
.user_avatar{ width:75px; height:75px; border-radius:50%; }
.inputfile {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}
.inputfile-2 + label {
    color: #6e8790;
    border: 2px solid currentColor;
}
.inputfile + label {
    font-size: 1.25rem;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    display: inline-block;
    overflow: hidden;
    padding: 10px;
    margin-top: 10px;
    width:auto;
}
.inputfile + label svg {
    width: 1em;
    height: 1em;
    vertical-align: middle;
    fill: currentColor;
    margin-top: -0.25em;
    margin-right: 0.25em;
}
.headingtext{ color:#000 !important; font-weight:200; margin-top:0px !important; font-size:18px;}
.nopaddingleft{ padding-left:0px !important; }
.nopaddingright{ padding-right:0px !important; }
input, textarea{ padding:5px !important; border-radius:4px; color: #555; border: 1px solid #ccc; padding: 6px 12px;
    font-size: 16px;
    line-height: 1.42857143;}
label{ font-weight:400; font-size:16px; }
.sf-button {
	float:right;
    display: inline-block !important;
}
.label-danger {
    background-color: #d9534f;
}
.label {
    display: block;
    padding: 10px;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    width: 100%;
    border-bottom-right-radius: 4px;
    border-bottom-left-radius: 4px;
}
.label-success {
    background-color: #5cb85c;
}
.label-default {
    background-color: #777;
}
.label-warning {
    background-color: #f0ad4e;
}
.label-primary {
    background-color: #337ab7;
}
.label-info {
    background-color: #5bc0de;
}
.has_error{
	border-color: #a94442;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
}
</style>
<div class="main">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	    <div class="inner-page-wrap clearfix">
			<div class="page-content clearfix">
				<section class="container">
					<div class="row">
						<div class="spb-column-container span12  " style="">
							<div class="spb-asset-content">
								<section class="container">
						 			<div class="row">
										<div class="spb_content_element span12 spb_text_column">
								            <?php the_content(); ?>
								            <?php if ( !is_user_logged_in() ) : ?>
								                    <p class="warning">
								                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
								                    </p><!-- .warning -->
								            <?php else : ?>
								            	<form method="post" id="adduser" action="<?php the_permalink(); ?>" enctype="multipart/form-data">
									            	<div class="my_account_top">
														<div class="col-xs-15 col-sm-12 nosidepadding">
															<h2 class="my_account_top_title">Account Settings</h2>
															<p class="quiet my_account_sub_title">Please use this page to update your account settings.</p>
														</div>
														<div class="col-xs-15 col-sm-3 nosidepadding">
															<p class="form-submit">
										                        <?php echo $referer; ?>
										                        <input name="updateuser" type="submit" id="updateuser" class="submit sf-button medium accent slightlyroundedarrow" value="<?php _e('Update', 'profile'); ?>" />
										                        <?php wp_nonce_field( 'update-user' ) ?>
										                        <input name="action" type="hidden" id="action" value="update-user" />
										                    </p><!-- .form-submit -->
														</div>
									            	</div>
									            	<div class="clearfix"></div>
									            	<?php if($fail == true){ ?>
														<div class="alert alert-danger"><strong>Failed</strong> to upload your avatar</div>
													<?php } 

													if($fail == false && count($error) == 0 && isset($_POST['submit'])) {?>
														<div class="alert alert-success">Your updates have <strong>Saved Successfully</strong> to your user profile.</div>
													<?php } ?>
									            	<hr/>
													<div class="col-xs-15 col-sm-2 nosidepadding" id="avatarholder">
														<?php
														$avatar_url = get_member_avatar();
														?>
														<img src="<?php echo $avatar_url; ?>" class="user_avatar" id="avatarimage">
													</div>
													<div class="col-xs-15 col-sm-7 nosidepadding">
														<h3 class="headingtext">Change Avatar</h3>
														<p class="quiet my_account_sub_title">Change your Avatar to further personalise your account.</p>
													</div>
													<div class="col-xs-15 col-sm-6 nosidepadding">
														<input type="file" name="avatar" id="avatar" class="inputfile inputfile-2" data-multiple-caption="{count} files selected">
														<label for="avatar"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg> <span>Choose a file…</span></label>
													</div>
													<div class="clearfix"></div>
									            	<hr/>
									                
									                <div class="col-xs-15 col-sm-5 nopaddingleft">
									                    <p class="form-username">
									                        <label for="first-name"><?php _e('First Name *', 'profile'); ?></label>
									                        <input class="text-input" name="first_name" type="text" id="first_name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" />
									                        <?php if(isset($error['first_name'])){ ?>
									                        	<?php echo "<span class='label label-danger'>".$error['first_name']."</span>"; ?>
								                        	<?php } ?>
									                    </p><!-- .form-username -->
								                    </div>
								                    <div class="col-xs-15 col-sm-5">
									                    <p class="form-username">
									                        <label for="last-name"><?php _e('Last Name *', 'profile'); ?></label>
									                        <input class="text-input" name="last_name" type="text" id="last_name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" />
									                        <?php if(isset($error['last_name'])){ ?>
									                        	<?php echo "<span class='label label-danger'>".$error['last_name']."</span>"; ?>
								                        	<?php } ?>
									                    </p><!-- .form-username -->
								                    </div>
								                    <div class="col-xs-15 col-sm-5 nopaddingright">
									                    <p class="form-email">
									                        <label for="email"><?php _e('E-mail *', 'profile'); ?></label>
									                        <input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" />
									                        <?php if(isset($error['email'])){ ?>
									                        	<?php echo "<span class='label label-danger'>".$error['email']."</span>"; ?>
								                        	<?php } ?>
									                    </p><!-- .form-email -->
								                    </div>
								                    <div class="clearfix"></div>
								                    <hr/>
								                    <h2 class="my_account_top_title">Change Password</h2>
													<div class="col-xs-15 col-sm-5 nopaddingleft">
									                    <p class="form-password">
									                        <label for="pass1"><?php _e('Password *', 'profile'); ?> </label>
									                        <input class="text-input" name="pass1" type="password" id="pass1" />
									                        <?php if(isset($error['password'])){ ?>
									                        	<?php echo "<span class='label label-danger'>".$error['password']."</span>"; ?>
								                        	<?php } ?>
									                    </p><!-- .form-password -->
								                    </div>
								                    <div class="col-xs-15 col-sm-5">
									                    <p class="form-password">
									                        <label for="pass2"><?php _e('Repeat Password *', 'profile'); ?></label>
									                        <input class="text-input" name="pass2" type="password" id="pass2" />
									                    </p><!-- .form-password -->
								                    </div>
								                    <div class="clearfix"></div>
													<!--<div class="col-xs-15 col-sm-5">
														<p class="form-currency">
															<?php /*$country_code = get_user_meta($current_user->ID, 'subscription_country_code', true);
															print_r($country_code);
															*/?>
															<label for="currency"><?php /*_e('Currency *', 'profile'); */?></label>
															<select name="currency" class="currency" id="currency">
																<option value="">Please select a currency</option>
																<option value="GBP" <?php /*if($currency == "GBP"){ echo "selected='selected'"; } */?>>Pound Sterling</option>
																<option value="USD" <?php /*if($currency == "USD"){ echo "selected='selected'"; } */?>>US Dollar</option>
																<option value="EURO" <?php /*if($currency == "EURO"){ echo "selected='selected'"; } */?>>Euro</option>
															</select>
														</p>
													</div>
								                    <hr/>-->
													<div class="clearfix"></div>
													<hr/>
									                    <p class="form-textarea">
									                        <label for="description"><?php _e('Biographical Information', 'profile') ?></label>
									                        <textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea>
									                    </p><!-- .form-textarea -->

									                    <?php 
									                        //action hook for plugin and extra fields
									                        do_action('edit_user_profile',$current_user); 
									                    ?>
									                    
									                
								            	<?php endif; ?>
									            </form><!-- #adduser -->
						            		</div>
					            		</div>
				            		</section>
			            		</div>
		            		</div>
	            		</div>
					</div>
				</div>
			    <?php endwhile; ?>
			<?php else: ?>
			    <p class="no-data">
			        <?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
			    </p><!-- .no-data -->
			<?php endif; ?>
		</div>
</div><!-- .main -->
<script>
var inputs = document.querySelectorAll( '.inputfile' );
Array.prototype.forEach.call( inputs, function( input )
{
	var label	 = input.nextElementSibling,
		labelVal = label.innerHTML;

	input.addEventListener( 'change', function( e )
	{
		var fileName = '';
		if( this.files && this.files.length > 1 )
			fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
		else
			fileName = e.target.value.split( '\\' ).pop();

		if( fileName )
			label.querySelector( 'span' ).innerHTML = fileName;
		else
			label.innerHTML = labelVal;
	});
});

jQuery(function($){
	var fileInput = document.getElementById("avatar");
	fileInput.addEventListener("change",function(e){
	  var files = this.files
	  showThumbnail(files)
	},false)

	function showThumbnail(files){
	  for(var i=0;i<files.length;i++){
	    var file = files[i]
	    var imageType = /image.*/
	    if(!file.type.match(imageType)){
	      console.log("Not an Image");
	      continue;
	    }

	    var image = document.createElement("img");
	    image.setAttribute("style", "width:75px; height:75px;display:none;");

	    // image.classList.add("")
	    var thumbnail = document.getElementById("avatarholder");
	    image.file = file;
	    thumbnail.appendChild(image)

	    var reader = new FileReader()
	    reader.onload = (function(aImg){
	      return function(e){
	        aImg.src = e.target.result;
	      };
	    }(image))
	    var ret = reader.readAsDataURL(file);
	    var canvas = document.createElement("canvas");
	    ctx = canvas.getContext("2d");
	    image.onload= function(){
	      ctx.drawImage(image,100,100)
	    }
	    $('.user_avatar').fadeOut(500, function(){
	    	$(this).remove();
	    	image.setAttribute("class", "user_avatar");
	    	$('.user_avatar').fadeIn();
	    });
	  }
	}
	<?php if(count($error) > 0){
		foreach($error as $k => $v){
		?>
		$('#<?php echo $k; ?>').addClass('has_error');
		<?php
		}
	}
	?>
	jQuery('body').on('click','.has_error', function(e){
		$(this).removeClass('has_error');
		var error_span=$(this).parent().find('.label-danger');
		error_span.animate({ height: "0px", padding:"0px", border: "0"}, 400);
	});
	jQuery('input').blur(function(e){
		if($(this).val() === ""){
			$(this).addClass('has_error');
			var parent = $(this).parent();
			var label = parent.find('.label');
			label.animate({ padding:"10px", border: "0", height: "35px"}, 400);
		}

	});
});
setTimeout(function(){ jQuery('.alert').animate({ height: "0px", padding:"0px", border: "0"}, 400); }, 3000);
</script>
<?php get_footer(); ?>