<?php

if (!class_exists('mgpc')) {

    class mgpc {

        public $args        = array();
        public $sections    = array();
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if (  true == Redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

           
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);           

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        function compiler_action($options, $css, $changed_values) {

			 $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
        }


       

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            /**
              Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader
            /*
            $sample_patterns_path   = ReduxFramework::$_dir . '../settings/patterns/';
            $sample_patterns_url    = ReduxFramework::$_url . '../settings/patterns/';
            */
            $img_url = ReduxFramework::$_url . '../settings/img/';


            /*$this->sections[] = array(
                'icon'      => 'dashicons el-icon-cog',
                'title'     => __('Basic', 'mgpc'),
                'heading'   => __('Basic Settings:', 'mgpc'),
                'desc'      => __('<p class="description">Contributors list always show after the post contents. Change this settings here...</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id'       => 'contributors-enable',
                        'type'     => 'switch',
                        'title'    => __('Enable Contributors List?', 'redux-framework-demo'),
                        'subtitle' => __('Do you want to enable contributors role? If enable it show Contributors List after post contents. Default: True', 'redux-framework-demo'),
                        'default'  => true
                    ),
                ),
            );*/

            $this->sections[] = array(
                'icon'      => 'dashicons dashicons-admin-appearance',
                'title'     => __('Background', 'mgpc'),
                'heading'   => __('Background Settings:', 'mgpc'),
                'desc'      => __('<p class="description">Change contributors block background color, border, margin, padding etc.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id' => 'background-settings',
                        'type' => 'info',
                        'icon' => 'el-icon-info-sign',
                        'style' => 'warning',
                        'title' => __('This settings affects on Contributors LIST Structure.', 'mgpc'),
                        'desc' => __('<br/> <img src="'.$img_url.'/background-setting.png" />', 'mgpc')
                    ),
                    array(
                        'id'       => 'background-color',
                        'type'     => 'color',
                        'mode'     => 'background',
                        'title'    => __('Background Color', 'mgpc'),
                        'subtitle' => __('Pick a background color for the block. (default: #ffffff).', 'mgpc'),
                        'default'  => '#ffffff',
                        'validate' => 'color',
                        'output'  => array('.mg-contributors-post'),
                    ),
					array(
                        'id'        => 'background-border',
                        'type'      => 'border',
                        'all'		=> false,
                        'title'     => __('Border', 'mgpc'),
                        'subtitle'  => __('Set BORDER of block.', 'mgpc'),
                        'output'    => array('.mg-contributors-post'),
                        'desc'      => __('Change contributors block border color, size for (right, left, top, bottom) or all. Default: Border: 1px solid #CCCDDD.', 'mgpc'),
                        'default'   => array(
                            'border-color'  => '#CCCDDD', 
                            'border-style'  => 'solid', 
                            'border-top'    => '1px', 
                            'border-bottom' => '1px',
                            'border-right' => '1px',
                            'border-left' => '1px'
                        )
                    ),
                    array(
                        'id'             => 'background-padding',
                        'type'           => 'spacing',
                        'output'         => array('.mg-contributors-post'),
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'title'          => __('Padding', 'mgpc'),
                        'subtitle'       => __('Change PADDING of block.', 'mgpc'),
                        'desc'          => __('Change contributors block Padding for (right, left, top, bottom) or all.', 'mgpc'),
                        'default'            => array(
                            'padding-top'     => '10px',
                            'padding-right'   => '20px',
                            'padding-bottom'  => '10px',
                            'padding-left'    => '20px',
                            'units'          => 'px',
                        )
                    ),
                    array(
                        'id'             => 'background-margin',
                        'type'           => 'spacing',
                        'output'         => array('.mg-contributors-post'),
                        'mode'           => 'margin',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'title'          => __('Margin', 'mgpc'),
                        'subtitle'       => __('Change MARGIN of block.', 'mgpc'),
                        'desc'          => __('Change contributors block Margin for (right, left, top, bottom) or all.', 'mgpc'),
                        'default'            => array(
                            'margin-top'     => '1em',
                            'margin-right'   => '0em',
                            'margin-bottom'  => '1em',
                            'margin-left'    => '0em',
                            'units'          => 'em',
                        )
                    ),
                )
            );

            $this->sections[] = array(
                'title'     => __('Contributors', 'mgpc'),
                'heading'   => __('Contributors List Settings:', 'mgpc'),                
                'icon'      => 'el-icon-group',
                'fields'    => array(
                    array(
                        'id' => 'contributors-list-settings',
                        'type' => 'info',
                        'icon' => 'el-icon-info-sign',
                        'style' => 'warning',
                        'title' => __('This settings affects on Contributors LIST Structure.', 'mgpc'),
                        'desc' => __('<br/> <img src="'.$img_url.'/contributors-list.png" />', 'mgpc')
                    ),
                    array(
                        'id'        => 'contributors-list-style',
                        'type'      => 'image_select',
                        //'compiler'  => array('.da-slider'),
                        'title'     => __('Verticle / Horizontal:', 'mgpc'),
                        'desc'      => __('<p class="description">Select Contributors basic structure.</p>', 'mgpc'),
                        'options'  => array(
                            '1'      => array(
                                'title'   => 'Horizontal',
                                'img'   => $img_url.'horizontal.png'
                            ),
                            '2'      => array(
                                'title'   => 'Verticle',
                                'img'   => $img_url.'verticle.png'
                            )
                        ),
                        'default' => '1'
                    ),
                    array(
                        'id'        => 'contributors-list',
                        'type'      => 'image_select',
                        'title'     => __('Show Author with:', 'mgpc'),
                        'desc'      => __('<p class="description">Show author with <strong>Avatar with Title</strong>, <strong>Just Title</strong> or <strong>Just Avatar</strong>.</p>', 'mgpc'),
                        'options'  => array(
                            '1'      => array(
                                'title'   => 'Name + Avatar',
                                'img'   => $img_url.'name+avatar.png'
                            ),
                            '2'      => array(
                                'title'   => 'Just Name',
                                'img'   => $img_url.'name.png'
                            ),
                            '3'      => array(
                                'title'   => 'Just Avatar',
                                'img'  => $img_url.'avatar.png'
                            )
                        ),
                        'default' => '1'
                    ),
                    array(
                        'id'       => 'contributor-link-enable',
                        'type'     => 'switch',
                        'title'    => __('Want to add Author Link?', 'mgpc'),
                        'subtitle' => __('If enable! It link to the author page (YOUR_THEME/author.php). <small><strong>Note: </strong> If your theme dont have author.php then dont enable it.</small> Default: false.', 'mgpc'),
                        'default'  => false,
                    ),
                    array(
                        'id' => 'avatar-info',
                        'type' => 'info',
                        'icon' => 'el-icon-info-sign',
                        'style' => 'warning',
                        'title' => __('AVATAR', 'mgpc'),
                        'desc' => __('Change avatar settings.', 'mgpc'),
                        'required' => array('contributors-list','equals', array('1', '3'))
                    ),
                    array(
                        'id'       => 'avatar-dimensions',
                        'type'     => 'dimensions',
                        'output'    => array('.mg-contributors-post img.avatar'),
                        'units'    => array('em','px','%'),
                        'title'    => __('Avatar - (Width/Height)', 'redux-framework-demo'),
                        'subtitle' => __('Change HEIGHT/WIDTH of Avatar.', 'mgpc'),
                        'desc'  => __('Change avatar height / width.', 'mgpc'),
                        'default'  => array(
                            'Width'   => '50'
                        ),
                        'required' => array('contributors-list','equals', array('1', '3'))
                    ),
                    array(
                        'id'        => 'avatar-border',
                        'type'      => 'border',
                        'all'       => false,
                        'title'     => __('Avatar - (Border)', 'mgpc'),
                        'subtitle' => __('Change BORDER of Avatar.', 'mgpc'),
                        'desc'  => __('Change Avatar Border for (right, left, top, bottom) or all.', 'mgpc'),
                        'output'    => array('.mg-contributors-post img.avatar'),
                        'default'   => array(
                            'border-color'  => '#DDDDDD', 
                            'border-style'  => 'solid', 
                            'border-top'    => '1px', 
                            'border-bottom' => '1px',
                            'border-right' => '1px',
                            'border-left' => '1px'
                        ),
                        'required' => array('contributors-list','equals', array('1', '3'))                        
                    ),
                    array(
                        'id' => 'role-info',
                        'type' => 'info',
                        'icon' => 'el-icon-info-sign',
                        'style' => 'warning',
                        'title' => __('ROLE', 'mgpc'),
                        'desc' => __('Change user role settings.', 'mgpc'),
                        'required' => array('contributors-list','equals', array('1', '2'))
                    ),                    
                    array(
                        'id'       => 'contributors-role',
                        'type'     => 'switch',
                        'title'    => __('Show Role?', 'redux-framework-demo'),
                        'subtitle' => __('Do you want to show contributors role? E.g. Administrator, Editor etc. Default: True', 'redux-framework-demo'),
                        'default'  => true,
                        'required' => array('contributors-list','equals', array('1', '2'))
                    ),
                    array(
                        'id'        => 'role-typography',
                        'type'      => 'typography',
                        'compiler'  => array('.mg-contributors-post h5.avatar-role'),
                        'title'     => __('Role - Typography', 'mgpc'),
                        'subtitle' => __('Change Typography of ROLE.', 'mgpc'),
                        'desc'  => __('Change Role Typography.', 'mgpc'),
                        'default'     => array(
                              'color' => '#6C6C6C',
                              'font-family'  => 'arial',
                              'font-size'  => '12px',
                              'font-style'  => 'normal',
                              'font-weight'  => '400',
                              'line-height'  => '20px',
                              'text-align'  => 'left'
                        ),
                        'required' => array('contributors-role','equals', true)
                    ),
                    array(
                        'id'        => 'role-border',
                        'type'      => 'border',
                        'all'       => false,
                        'title'     => __('Role - Border', 'mgpc'),
                        'subtitle' => __('Change Border of ROLE.', 'mgpc'),
                        'desc'  => __('Change ROLE border for (right, left, top, bottom) or all.', 'mgpc'),
                        'output'    => array('.mg-contributors-post h5.avatar-role'),
                        'default'   => array(
                            'border-color'  => '#fff', 
                            'border-style'  => 'solid', 
                            'border-top'    => '0px', 
                            'border-bottom' => '0px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'required' => array('contributors-role','equals', true)
                    ),
                    array(
                        'id'             => 'role-padding',
                        'type'           => 'spacing',
                        'output'         => array('.mg-contributors-post h5.avatar-role'),
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'title'          => __('Role - Padding', 'mgpc'),
                        'subtitle' => __('Change PADDING of block.', 'mgpc'),
                        'desc'  => __('Change ROLE Padding for (right, left, top, bottom) or all.', 'mgpc'),
                        'default'            => array(
                            'padding-top'     => '0px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '0px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'required' => array('contributors-role','equals', true)
                    ),
                    array(
                        'id'             => 'role-margin',
                        'type'           => 'spacing',
                        'output'         => array('.mg-contributors-post h5.avatar-role'),
                        'mode'           => 'margin',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'title'          => __('Role - Margin', 'mgpc'),
                        'subtitle' => __('Change MARGIN of block.', 'mgpc'),
                        'desc'  => __('Change ROLE Margin for (right, left, top, bottom) or all.', 'mgpc'),
                        'default'            => array(
                            'margin-top'     => '0em',
                            'margin-right'   => '0em',
                            'margin-bottom'  => '0em',
                            'margin-left'    => '0em',
                            'units'          => 'em',
                        ),
                        'required' => array('contributors-role','equals', true)
                    ),                    
                ),
            );


            $this->sections[] = array(
                'icon'      => 'dashicons dashicons-editor-quote',
                'title'     => __('Label', 'mgpc'),
                'heading'   => __('Contributors LABEL settings.', 'mgpc'),
                'desc'      => __('<p class="description">Choose best font face, sice, color which is suitable for your theme.</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id' => 'info_critical',
                        'type' => 'info',
                        'icon' => 'el-icon-info-sign',
                        'style' => 'warning',
                        'title' => __('This settings affects on Contributors LABEL Field.', 'mgpc'),
                        'desc' => __('<br/> <img src="'.$img_url.'/title-setting.png" />', 'mgpc')
                    ),
                    array(
                        'id'       => 'title-enable',
                        'type'     => 'switch',
                        'title'    => __('Enable Label?', 'mgpc'),
                        'subtitle' => __('Do you want to show title on contributors list.', 'mgpc'),
                        'default'  => true,
                    ),
	                array(
                        'id'       => 'title-text',
                        'type'     => 'text',
                        'title'    => __('Label Text:', 'mgpc'),
                        'subtitle' => __('Do you want to change label of contributors list?', 'mgpc'),
                        'desc'     => __('Change label of contributors list. Defaults: Contributors.', 'mgpc'),
                        'default'  => 'Contributors:',
                        'required' => array('title-enable','equals', true)
                    ),
                    array(
                        'id'        => 'title-typography',
                        'type'      => 'typography',
                        'compiler'  => array('.mg-contributors-post h3.title'),
                        'title'     => __('Typography', 'mgpc'),
                        'subtitle'  => __('Change typography of label.', 'mgpc'),
                        'default'     => array(
                              'color' => '#7F7F7F',
                              'font-family'  => 'arial',
                              'font-size'  => '20px',
                              'font-style'  => 'normal',
                              'font-weight'  => '400',
                              'line-height'  => '20px',
                              'text-align'  => 'left'
                        ),
                        'required' => array('title-enable','equals', true)
                    ),
                    array(
                        'id'        => 'title-border',
                        'type'      => 'border',
                        'all'       => false,
                        'title'     => __('Border', 'mgpc'),
                        'subtitle'  => __('Want to change border color?', 'mgpc'),
                        'output'  => array('.mg-contributors-post h3.title'),
                        'desc'      => __('Change border color. Default: Border 1px solid #DDDDDD.', 'mgpc'),
                        'default'   => array(
                            'border-color'  => '#DDDDDD', 
                            'border-style'  => 'solid', 
                            'border-top'    => '0px', 
                            'border-bottom' => '0px',
                            'border-right' => '0px',
                            'border-left' => '0px'
                        ),
                        'required' => array('title-enable','equals', true)
                    ),
                    array(
                        'id'             => 'title-margin',
                        'type'           => 'spacing',
                        'output'         => array('.mg-contributors-post h3.title'),
                        'mode'           => 'margin',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'title'          => __('Margin', 'mgpc'),
                        'subtitle'       => __('Want to change margin?.', 'mgpc'),
                        'desc'           => __('Change label margin.', 'mgpc'),
                        'default'            => array(
                            'margin-top'     => '0px',
                            'margin-right'   => '0px',
                            'margin-bottom'  => '10px',
                            'margin-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'required' => array('title-enable','equals', true)
                    ),
                    array(
                        'id'             => 'title-padding',
                        'type'           => 'spacing',
                        'output'         => array('.mg-contributors-post h3.title'),
                        'mode'           => 'padding',
                        'units'          => array('em', 'px'),
                        'units_extended' => 'false',
                        'title'          => __('Padding', 'mgpc'),
                        'subtitle'       => __('Want to change padding?.', 'mgpc'),
                        'desc'           => __('Change label padding.', 'mgpc'),
                        'default'            => array(
                            'padding-top'     => '5px',
                            'padding-right'   => '0px',
                            'padding-bottom'  => '10px',
                            'padding-left'    => '0px',
                            'units'          => 'px',
                        ),
                        'required' => array('title-enable','equals', true)                        
                    ),
                )
            );

            $this->sections[] = array(
                'icon'      => 'dashicons dashicons-admin-plugins',
                'title'     => __('Our Plugins', 'mgpc'),
                'heading'   => __('Our Plugins:', 'mgpc'),
                'desc'      => __('<p class="description">Check our another usefull wordpress plugins...</p>', 'mgpc'),
                'fields'    => array(
                    array(
                        'id' => 'mg-parallax-slider-plugin',
                        'type' => 'info',
                        'title' => __('MG Parallax Slider', 'mgpc'),
                        'desc' => __('<br/>Visit http://wordpress.org/plugins/mg-parallax-slider', 'mgpc')
                    ),
                ),
            );
            
            $this->sections[] = array(
                'title'     => __('Import / Export', 'mgpc'),
                'desc'      => __('Import and Export your Redux Framework settings from file, text or URL.', 'mgpc'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your Redux options',
                        'full_width'    => false,
                    ),
                ),
            );
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'mgpc'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'mgpc')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => __('Theme Information 2', 'mgpc'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'mgpc')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'mgpc');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'mgpc',            // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => 'MG Post Contributors',     // Name that appears at the top of your panel
                'display_version'   => '1.1.',  // Version that appears at the top of your panel
                'menu_type'         => 'menu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
                'menu_title'        => __('MGPC Settings', 'mgpc'),
                'page_title'        => __('MGPC Settings', 'mgpc'),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '', // Must be defined to add google fonts to the typography module
                
                'async_typography'  => false,                    // Use a asynchronous font on the front end or font string
                'admin_bar'         => true,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                    // Show the time the page took to load, etc
                'customizer'        => false,                    // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'themes.php',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '',                      // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => 'mgpc_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => true,                   // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'https://github.com/maheshwaghmare/mg-parallax-slider',
                'title' => 'Visit us on GitHub',
                'icon'  => 'el-icon-github'
                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://www.facebook.com/mgwebthemes',
                'title' => 'Like us on Facebook',
                'icon'  => 'el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'https://twitter.com/mwaghmare7',
                'title' => 'Follow us on Twitter',
                'icon'  => 'el-icon-twitter'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://in.linkedin.com/in/mwaghmare7',
                'title' => 'Find us on LinkedIn',
                'icon'  => 'el-icon-linkedin'
            );

            

            $this->args['intro_text'] = __('<p>Welcome to <strong>MG Post Contributors</strong>...!</p>', 'mgpc');
            

            // Add content after the form.
            $this->args['footer_text'] = __('<p>Thanks for using "MG Post Contributors". Special Thanks to Redux.</p>', 'mgpc');
        }

    }
    
    global $reduxConfig;
    $reduxConfig = new mgpc();
}

