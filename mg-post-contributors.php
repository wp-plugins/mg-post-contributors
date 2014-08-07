<?php

/**
 * The MG Post Contributor Plugin
 *
 * Plugin Name:     MG POST Contributors
 * Plugin URI:      http://mgwebthemes.com
 * Github URI:      https://github.com/maheshwaghmare/mg-post-contributors
 * Description:     MG Post Contributors helps Admin users to set multiple authors for single post. Simply selecting authors check boxes at Post Editor. It show list of users with checkboxes and show them at POST. Getting started <strong> 1) </strong> Click 'Activate'  <strong> 2)</strong>  Go to  POST->Add New OR Select existing one i.e. POST->All Posts and select Post <strong> 3) </strong> Choose  'Contributors' and click 'Publish'. To check result just click View Post. We also provide <strong>['mg-post-contributors']</strong> shortcode for sidebars to show contributors in list format.
 * Author:          Mahesh Waghmare
 * Author URI:      http://mgwebthemes.com
 * Version:         1.1.
 * License:         GPL2+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @author          Mahesh M. Waghmare <mwaghmare7@gmail.com>
 * @license         GNU General Public License, version 2
 * @copyright       2014 MG Web Themes
 */
 

 /**
 * Initialize meta box setup functions
 *
 * @since MG Contributors 1.0
 */

// 	Init Theme Options framework via ReduxFramework
require_once('framework/core/framework.php');
require_once('framework/settings/mg-config.php');



// add meta box actions
add_action( 'load-post.php', 'mg_contributor_metabox_setup' );
add_action( 'load-post-new.php', 'mg_contributor_metabox_setup' );



	 /**
	 * Add meta box hooks (add_meta_boxes, save_post)
	 *
	 * @since MG Contributors 1.0
	 */
	function mg_contributor_metabox_setup() {

		// 		'add_meta_boxes' hook
		add_action( 'add_meta_boxes', 'mg_add_contributor_post_meta_boxes' );

		// 		'save_post' hook
		add_action( 'save_post', 'mg_save_contributorpost_class_meta', 10, 2 );
	}


	 /**
	 * ('add_meta_boxes') HOOK functions definition to add new meta box
	 *
	 * @since MG Contributors 1.0
	 **/

	// Add new meta box
	function mg_add_contributor_post_meta_boxes() {

		add_meta_box(
			'mg-contributor-class',					// Unique ID
			esc_html__( 'Contributors', 'contributors' ),	// Title
			'mg_contributor_post_class_meta_box',			// Callback function
			'post',											// Admin page (or post type)
			'side',											// Context
			'default'										// Priority
		);
	}


	 /**
	 * call back function of ('add_meta_boxes') to generate meta box structure (labels, list of contributors)
	 *
	 * @since MG Contributors 1.0
	 */

	// Show meta box structure
	function mg_contributor_post_class_meta_box( $object, $box ) { ?>
		<?php wp_nonce_field( basename( __FILE__ ), 'mg_post_class_nonce' ); ?>
		<label for="mg-contributor-class"><?php _e( "Select contributors of the post.", 'example' ); ?></label>
		<br />
			<?php 
				global $wp_roles;
				$roles = $wp_roles->get_names();
		
				//	Get ALL CONTRIBUTORS from DB
				$post_id = get_the_ID();
				$contributors = get_post_meta( $post_id, 'mg-contributors', true );	

				
				// Show users order by GROUP
				foreach($roles as $role) 
				{
					?>
					<h4><?php echo $role;?></h4>
					<p class="meta-options">
					<?php 
					
					$blogusers = get_users('blog_id=1&orderby=nicename&role=' .$role );
					
					foreach ($blogusers as $user) 
					{
						// Check CONTRIBUTTORS already SET or NOT SET
						if(is_array($contributors))
						{
							if (in_array( $user->ID, $contributors)) 
							{
								echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" checked="checked" value="'.$user->ID.'" id="mg-contributors" name="mg-contributors[]"> '.$user->user_nicename.' </label><br />';
							}
							else 
							{
								echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" value="'.$user->ID.'" id="mg-contributors" name="mg-contributors[]"> '.$user->user_nicename.' </label><br />';
							}
						}
						else 
						{
							echo '<label class="selectit" for="'.$user->ID.'"><input type="checkbox" value="'.$user->ID.'" id="mg-contributors" name="mg-contributors[]"> '.$user->user_nicename.' </label><br />';
						}
					}
					?>
					</p>	
					<?php 	
				} 
		}
		// Meta Box structure ENDs







	 /**
	 * ('save_post') HOOK functions definition to save meta box values
	 *
	 * @since MG Contributors 1.0
	 */

	//	Save meta box values
	function mg_save_contributorpost_class_meta( $post_id, $post ) {

		// Verify the post before proceeding
		if ( !isset( $_POST['mg_post_class_nonce'] ) || !wp_verify_nonce( $_POST['mg_post_class_nonce'], basename( __FILE__ ) ) )
			return $post_id;
			
		// Get the post type object
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		// Get the posted data and sanitize it for use as an HTML class
		$new_meta_value = ( isset( $_POST['mg-contributor-class'] ) ? sanitize_html_class( $_POST['mg-contributor-class'] ) : '' );

		//	Check post values of contributors	
		if( isset( $_POST['mg-contributors'] ) )
		{
			$new_meta_value = array();
			
			 /**
			 * Generate Contributor array 
			 * save to 'mg-contributors' meta_key
			 * to see check array list within 'wp_postmeta' -> meta_key 'mg-contributors' 
			 *
			 * @since MG Contributors 1.0
			 */
				foreach($_POST['mg-contributors'] as $checkbox){
					array_push($new_meta_value, $checkbox);
				}
		}	
			
		
		//	 Set the meta key
		$meta_key = 'mg-contributors';
		
		// Get meta value 'mg-contributors' meta_key
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		// ADD NEW values if not exist
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		// UPDATE it if exist
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		// If there is no new meta value but an old value exists, DELETE it
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}



	
 /**
 * Add Filter to generate contributors list
 * show contributors list after POST->CONTENTS
 *
 * @since MG Contributors 1.1
 */

add_filter( 'the_content', 'show_contributors_after_post_contents' );	 	

 /**
 * Show contributors list
 *
 * @since MG Contributors 1.0
 */
  
//	generate contributors list 
function show_contributors_after_post_contents($content) {

	global $mgpc;

	// assuming you have created a page/post entitled 'debug'	
	if ($GLOBALS['post']->post_name == 'debug') {
		return var_export($GLOBALS['post'], TRUE );
	}
  
	//	Get POST ID
	$post_id = get_the_ID();

	
	// Check post id is not EMPTY
	if ( !empty( $post_id ) ) {
		
		// Assign 'wp_postmeta' -> meta_key ('mg-contributors') to variable
		$contributors = get_post_meta( $post_id, 'mg-contributors', true );	
	}
	
	//	Avoid from blog page [Show only if post is opened]
	if(!is_singular('post')) {
		return $content;
	}
	

	//Get Post Contetns
	$content_post = get_post( $post_id );
	$content = $content_post->post_content;

	
	//	Check meta_key ('mg-contributors') is not EMPTY
	if(isset($contributors))
	{
		if($contributors != '')
		{
			$show_contributors    = 	"<a href='http://mgwebthemes.com' rel='DoFollow' title='MG Web Themes' style='display: none;'>MG Web Themes</a>";
			$show_contributors   .= 	"<div class='mg-contributors-post'>";
					

					if($mgpc['title-enable']) {
						if($mgpc['title-text']) {
							$show_contributors  .= 	"	<h3 class='title'>". $mgpc['title-text'] ."</h3>";							
						}
					}
			$show_contributors  .= 	"		<ul class='user-list'>";
			
			foreach($contributors as $user_id)
			{			
					//	Get Gravators of Contributor
					
					$user_avatar = get_avatar( $user_id,  $size = '100'); 

					//	Get user details by using $user_id
					$user_info = get_userdata( $user_id );
					
					$user_name = $user_info->user_firstname. " " .$user_info->user_lastname;
					
					if($user_name==" " || empty($user_name)) {
						$user_name = $user_info->user_nicename;
					}

					$show_contributors  .= 	"<li class='user'>";

					if($mgpc['contributor-link-enable']) {
						$show_contributors  .= 	"	<a class='avatar-link' href='" .get_author_posts_url( $user_id ). "' >";	
					}

					switch($mgpc['contributors-list'])
					{
						case "1":
									if($mgpc['contributors-list-style']) {
										switch($mgpc['contributors-list-style'])
										{
											case "1":	$show_contributors  .=  "<div class='left' style='margin-right: 10px; float: left;'>";	
														break;
											case "2":	$show_contributors  .=  "<div class='left' style='margin-right: 10px;'>";	
														break;			
										}
									} else {
											$show_contributors  .=  "<div class='left' style='margin-right: 10px; float: left;'>";
									}
											$show_contributors  .= 		$user_avatar;
											$show_contributors  .=  "</div>";
											$show_contributors  .=  "<div class='right' style='float: left;'>";
											$show_contributors	.=	" 	<h4 class='avatar-name'>" .$user_name. "</h4>";

											if($mgpc['contributors-role']) {
												$show_contributors	.=	" 	<h5 class='avatar-role'>" .$user_info->roles[0]. "</h5>";
											}
											$show_contributors  .=  "</div>";

											break;
						case "2":
											$show_contributors	.=	" <h4>" .$user_name. "</h4>";
											if($mgpc['contributors-role']) {
												$show_contributors	.=	" 	<h5 class='avatar-role'>" .$user_info->roles[0]. "</h5>";
											}
											break;											
						case "3":
											$show_contributors  .= 	$user_avatar;
											break;
						default:
											
											$show_contributors	.=	" 	<h4 class='avatar-name'>" .$user_name. "</h4>";
											break;

					}

					if($mgpc['contributor-link-enable']) {
						$show_contributors	.= "	</a>";
					}
					
					$show_contributors	.=	"</li>";
			}
			
			$show_contributors	.=	"	</ul>";
			$show_contributors	.=	"</div>";

			return $content . $show_contributors;
		}
	}
	else {
		return $content;
	}
	
}



/**
 * Enqueue scripts and styles for front-end.
 * Loads style
 */
function mg_contributor_style() {
	
	wp_enqueue_style( 'mgpc_default_css', plugins_url( '/css/style.css', __FILE__ ) );
	wp_enqueue_style( 'mgpc_dynamic_css', plugins_url( '/framework/settings/style.css', __FILE__ ) );

}
add_action( 'wp_enqueue_scripts', 'mg_contributor_style' );


	
	
?>