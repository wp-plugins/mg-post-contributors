<?php

/**
 * The MG Post Contributor Plugin
 *
 * Plugin Name:     MG POST Contributors
 * Plugin URI:      http://mgwebthemes.com
 * Github URI:      http://github.com/MGWebThemes/MGContributors
 * Description:     <strong> MG Post Contributors </strong> helps Admin users to set multiple authors for single post. Simply selecting authors check boxes at Post Editor. It show list of users with checkboxes and show them at POST. <strong> Getting started 1) Click 'Activate'  2)  Go to  POST->Add New </strong> OR Select existing one i.e. <strong> POST->All Posts </strong> and select Post <strong> 3) </strong> Choose <strong> 'Contributors' and click 'Publish'.</strong> To check result just click <strong> View Post </strong>.
 * Author:          Mahesh Waghmare
 * Author URI:      http://mgwebthemes.com
 * Version:         1.0.
 * Text Domain:     mg-contributor
 * License:         GPL2+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:     /MGContributor/lang
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

// INIT meta box setup function 
add_action( 'load-post.php', 'mg_contributor_metabox_setup' );
add_action( 'load-post-new.php', 'mg_contributor_metabox_setup' );



	 /**
	 * Add meta box hooks (add_meta_boxes, save_post)
	 *
	 * @since MG Contributors 1.0
	 */
	function mg_contributor_metabox_setup() {

		// 		'add_meta_boxes' hook
		add_action( 'add_meta_boxes', 'mg_add_post_meta_boxes' );

		// 		'save_post' hook
		add_action( 'save_post', 'mg_save_post_class_meta', 10, 2 );
	}


	 /**
	 * ('add_meta_boxes') HOOK functions definition to add new meta box
	 *
	 * @since MG Contributors 1.0
	 **/

	// Add new meta box
	function mg_add_post_meta_boxes() {

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
							if (in_array( $user->id, $contributors)) 
							{
								echo '<label class="selectit" for="'.$user->id.'"><input type="checkbox" checked="checked" value="'.$user->id.'" id="mg-contributors" name="mg-contributors[]"> '.$user->user_nicename.' </label><br />';
							}
							else 
							{
								echo '<label class="selectit" for="'.$user->id.'"><input type="checkbox" value="'.$user->id.'" id="mg-contributors" name="mg-contributors[]"> '.$user->user_nicename.' </label><br />';
							}
						}
						else 
						{
							echo '<label class="selectit" for="'.$user->id.'"><input type="checkbox" value="'.$user->id.'" id="mg-contributors" name="mg-contributors[]"> '.$user->user_nicename.' </label><br />';
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
	function mg_save_post_class_meta( $post_id, $post ) {

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
 * @since MG Contributors 1.0
 */
 
 add_filter( 'the_content', 'show_contributors_after_post_contents' );	


 
 /**
 * Show contributors list
 *
 * @since MG Contributors 1.0
 */
  
//	generate contributors list 
function show_contributors_after_post_contents($content) {

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
	
	//	Check meta_key ('mg-contributors') is not EMPTY
	if(isset($contributors))
	{
		if($contributors != '')
		{
			$show_contributors    = 	"<a href='http://mgwebthemes.com' rel='DoFollow' title='MG Web Themes' style='display: none;'>MG Web Themes</a>";
			$show_contributors   .= 	"<div class='mg-contributors'>";
					
					//	GET SETTING DATA
					$options = get_option('plugin_options');
					
					//	Set Title
					if($options['mg_show_title'])
					{
						if($options['mg_title'])
						{
							$show_contributors  .= 	"	<h2>" .$options['mg_title']. "</h2>";
						}
						else
						{
							$show_contributors  .= 	"	<h2>Contributors:</h2>";
						}
					}
			$show_contributors  .= 	"		<ul>";
			
			foreach($contributors as $user_id)
			{					
					//	Get Gravators of Contributor
					$user_avatar = get_avatar( $user_id, 32 ); 

					//	Get user details by using $user_id
					$user_info = get_userdata( $user_id );
					$user_name = $user_info->user_firstname. " " .$user_info->user_lastname;

					$show_contributors  .= 	"<li>";
					$show_contributors  .= 	"	<a href='" .get_author_posts_url( $user_id ). "' >";

					switch($options['mg_select_author'])
					{
						case "Only Avatar":
											$show_contributors  .= 			$user_avatar;
											break;
						case "Only Name":
											$show_contributors	.=	"		<h4>" .$user_name. "</h4>";
											if($options['mg_show_author_role'])
											{
												$show_contributors	.=	"		<h5>" .$user_info->roles[0]. "</h5>";
											}
											break;											
						case "Name + Avatar":
											$show_contributors  .= 			$user_avatar;
											$show_contributors	.=	"		<h4>" .$user_name. "</h4>";
											if($options['mg_show_author_role'])
											{
												$show_contributors	.=	"		<h5>" .$user_info->roles[0]. "</h5>";
											}
											break;
						default:
											$show_contributors  .= 			$user_avatar;
											$show_contributors	.=	"		<h4>" .$user_name. "</h4>";
											if($options['mg_show_author_role'])
											{
												$show_contributors	.=	"		<h5>" .$user_info->roles[0]. "</h5>";
											}
											break;
					}

					$show_contributors	.= "	</a>";
					$show_contributors	.=	"</li>";
			}
			
			$show_contributors	.=	"	</ul>";
			$show_contributors	.=	"</div>";
		}
	}
	

	//Get Post Contetns
	$content_post = get_post( $post_id );
	$content = $content_post->post_content;

	return $content . $show_contributors;
	
}



//	ENQUEUE stylesheet ('style.css')	
wp_enqueue_style( 'wp_enqueue_styles', plugins_url( '/css/style.css', __FILE__ ) );

	
	


 /**
 * Add Setting hooks
 * User can set visual design of contributors.
 *
 * @since MG Contributors 1.0
 */
register_activation_hook(__FILE__, 'mg_add_defaults');
add_action('admin_init', 'mg_init_fn' );
add_action('admin_menu', 'mg_add_page_fn');


	// Add sub page to the Settings Menu
	function mg_add_page_fn() {
		add_options_page('Options Example Page', 'MG Post Contributor', 'administrator', __FILE__, 'options_page_fn');
	}


	// Define default option settings
	function mg_add_defaults() {
		$tmp = get_option('plugin_options');
		if(($tmp['mg_restore_all']=='on')||(!is_array($tmp))) 
		{
			$arr = array("mg_title"=>"Contributors", "mg_show_title" => "on", "mg_select_author" => "Name + Avatar", "mg_show_author_role" => "on", "mg_restore_all" => "");
			update_option('plugin_options', $arr);
		}
	}

	
	// Register our settings. Add the settings section, and settings fields
	function mg_init_fn()
	{
		register_setting('plugin_options', 'plugin_options', 'plugin_options_validate' );
		add_settings_section('main_section', 'General Settings', 'section_text_fn', __FILE__);
		add_settings_field('mg_title', 'Title', 'mg_title', __FILE__, 'main_section');
		add_settings_field('mg_show_title', 'Show Title:', 'mg_show_title', __FILE__, 'main_section');
		add_settings_field('mg_select_author', 'Show contributors with:', 'mg_select_author_type', __FILE__, 'main_section');
		add_settings_field('mg_show_author_role', 'Show User Roles:', 'mg_show_author_role', __FILE__, 'main_section');
	}


	// Callback functions

	// TITLE		$options[mg_title]
	function mg_title() 
	{
		$options = get_option('plugin_options');
		echo "<input id='mg_title' name='plugin_options[mg_title]' size='40' type='text' value='{$options['mg_title']}' /><br />";
		echo "<p>[Default 'Contributors']</p>";
	}


	// SHOW AUTHOR WITH : 	$options[mg_select_author]
	function mg_select_author_type() {
		$options = get_option('plugin_options');
		$items = array("Only Avatar", "Only Name", "Name + Avatar");
		echo "<p> [Show contributors after post with?]</p>";
		echo "<table><tr>";
		foreach($items as $item) {
			$checked = ($options['mg_select_author']==$item) ? ' checked="checked" ' : '';
			
			switch($item)
			{
				case "Only Avatar":		$thumb = plugin_dir_url( __FILE__ ) . '/images/Avatar.png';
										break;
				case "Only Name":		$thumb = plugin_dir_url( __FILE__ ) . '/images/Name.png';
										break;
				case "Name + Avatar":	$thumb = plugin_dir_url( __FILE__ ) . '/images/Name+Avatar.png';
										break;
				default:				$thumb = plugin_dir_url( __FILE__ ) . '/images/Name+Avatar.png';
										break;
			}
			
			echo "<td><label><img src='".$thumb."' /><br /><input ".$checked." value='$item' name='plugin_options[mg_select_author]' type='radio' /> $item</label></td>";

		}
		echo "</tr></table>";
	}

	// SHOW/HIDE Role 		$options[mg_show_author_role]
	function mg_show_author_role() 
	{
		$options = get_option('plugin_options');
		if($options['mg_show_author_role']) { $checked = ' checked="checked" '; }
		echo "<input ".$checked." id='mg_show_author_role' name='plugin_options[mg_show_author_role]' type='checkbox' />User role?";
		echo "<p>[HIDE user role? i.e. Administrator, Author, Contributor etc.] below Author name.</p>";
	}	

	// SHOW/HIDE TITLE 		$options[mg_show_title]
	function mg_show_title() 
	{
		$options = get_option('plugin_options');
		if($options['mg_show_title']) { $checked = ' checked="checked" '; }
		echo "<input ".$checked." id='mg_show_title' name='plugin_options[mg_show_title]' type='checkbox' />";
		echo "<p>[HIDE or SHOW Title Default 'SHOW'.]</p>";
	}	
		
	// Section HTML, displayed before the first option
	function  section_text_fn() 
	{
		echo '<p>Select how do you want to show your contributors after post contents.</p>';
	}

	// Display the admin options page
	function options_page_fn() 
	{
?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2>MG Post Contributor</h2>

			<form action="options.php" method="post">
			<?php settings_fields('plugin_options'); ?>
			<?php do_settings_sections(__FILE__); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</p>
			</form>
		</div>
<?php
	}

	
	
	// Validate user data for some/all of your input fields
	function plugin_options_validate($input) 
	{
		// Check our textbox option field contains no HTML tags - if so strip them out
		$input['text_string'] =  wp_filter_nohtml_kses($input['text_string']);	
		return $input; // return validated input
	}
	
	
?>