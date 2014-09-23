<?php
 
//	Social profile array Note: Use only small caps (GLOBAL Social array)
$profiles = array("facebook", "twitter", "google-plus", "wordpress", "linkedin", "youtube", "pinterest", "instagram", "tumblr", "flickr", "skype");

//	CREATE fields 
add_action( 'show_user_profile', 'mgpc_user_profile_fields' );
add_action( 'edit_user_profile', 'mgpc_user_profile_fields' );
function mgpc_user_profile_fields( $user ) { 

	/*
	 *	1. Create SOCIAL Links
	 * @desc social links
	 *-----------------------------------------------------------------*/
	//	get profiles from array
	global $profiles;
	?>
	<h3>MGPC - Social Links</h3>
	<table class="form-table">
		<?php foreach($profiles as $profile) { ?>
		<tr>
			<?php echo "<th><label for='mgpc_social_link_". $profile ."'>". ucfirst($profile) ."</label></th>"; ?>
			<td>
				<input type="text" name='mgpc_social_link_<?php echo $profile; ?>' id='mgpc_social_link_<?php echo $profile; ?>' value="<?php echo esc_attr( get_the_author_meta( 'mgpc_social_link_'. $profile, $user->ID ) ); ?>" class="regular-text" /><br />
				<?php printf("<span class='description'>Please enter your %s username.</span>", ucfirst($profile)); ?>
			</td> 
		</tr>
		<?php }	?>
	</table>

	<?php
	/*
	 *	2. Create Profile IMAGE
	 * @desc IMAGE
	 *-----------------------------------------------------------------*/
	?>
	<h3>MGPC - Profile Images</h3>
		<style type="text/css">
		.fh-profile-upload-options th,
		.fh-profile-upload-options td,
		.fh-profile-upload-options input {
			vertical-align: top;
		}

		.user-preview-image {
			display: block;
			height: auto;
			width: 300px;
		}
	</style>
	<table class="form-table fh-profile-upload-options">
		<tr>
			<th>
				<label for="image">Original Pic</label>
			</th>
			<td>
				<img class="user-preview-image" src="<?php echo esc_attr( get_the_author_meta( 'mgpc_original_pic', $user->ID ) ); ?>">
				<input type="text" name="mgpc_original_pic" id="mgpc_original_pic" value="<?php echo esc_attr( get_the_author_meta( 'mgpc_original_pic', $user->ID ) ); ?>" class="regular-text" />
				<input type='button' class="button-primary" value="Upload Image" id="uploadimage"/><br />
				<span class="description">Please upload an image for your profile.</span>
			</td>
		</tr>
		<tr>
			<th>
				<label for="image">Thumbnail Pic</label>
			</th>
			<td>
				<img class="user-preview-image" src="<?php echo esc_attr( get_the_author_meta( 'mgpc_thumb_pic', $user->ID ) ); ?>">
				<input type="text" name="mgpc_thumb_pic" id="mgpc_thumb_pic" value="<?php echo esc_attr( get_the_author_meta( 'mgpc_thumb_pic', $user->ID ) ); ?>" class="regular-text" />
				<input type='button' class="button-primary" value="Upload Image" id="sidebarUploadimage"/><br />
				<span class="description">Please upload an image for the sidebar.</span>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		(function( $ ) {
			$( '#uploadimage' ).on( 'click', function() {
				tb_show('test', 'media-upload.php?type=image&TB_iframe=1');
				window.send_to_editor = function( html ) 
				{
					imgurl = $( 'img',html ).attr( 'src' );
					$( '#mgpc_original_pic' ).val(imgurl);
					tb_remove();
				}
				return false;
			});
			$( 'input#sidebarUploadimage' ).on('click', function() {
				tb_show('', 'media-upload.php?type=image&TB_iframe=true');
				window.send_to_editor = function( html ) 
				{
					imgurl = $( 'img', html ).attr( 'src' );
					$( '#mgpc_thumb_pic' ).val(imgurl);
					tb_remove();
				}
				return false;
			});
		})(jQuery);
	</script>
<?php }


//	Saving data (Additional social links, Uploaded profile image)
add_action( 'personal_options_update', 'mgpc_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'mgpc_save_user_profile_fields' );
function mgpc_save_user_profile_fields( $user_id ) {
	
	//	get profiles from array
	global $profiles;

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	foreach($profiles as $profile) {
		$getLink = 'mgpc_social_link_'. $profile;
		update_usermeta( $user_id, $getLink, $_POST[$getLink] );
	}
	update_user_meta( $user_id, 'mgpc_original_pic', $_POST[ 'mgpc_original_pic' ] );
	update_user_meta( $user_id, 'mgpc_thumb_pic', $_POST[ 'mgpc_thumb_pic' ] );
}

?>