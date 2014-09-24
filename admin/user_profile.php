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
}

?>