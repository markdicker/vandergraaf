<?php
/**
 * Plugin Name:     Van der Graaf
 * Plugin URI:      vandergraaf.io
 * Description:     Generate a static version of a site.  Allows access via authorised header.
 * Author:          Mark Dicker
 * Author URI:      vandergraaf.io
 * Text Domain:     vandergraaf
 * Domain Path:     /languages
 * Version:         0.0.12
 *
 * @package         VanderGraaf
 * 
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 */

/**
 *	Copyright (C) 2012-2017 Mark Dicker (email: mark@markdicker.co.uk)
 *
 *	This program is free software; you can redistribute it and/or
 *	modify it under the terms of the GNU General Public License
 *	as published by the Free Software Foundation; either version 2
 *	of the License, or (at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, write to the Free Software
 *	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
 

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

require_once( "classes/updater.php" );
require_once( "classes/process-page.php" );
require_once( "classes/local-storage.php");
require_once( "classes/settings.php");

define ( 'vandergraaf_icon', "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+Cjxzdmcgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDExODIgMTE4MiIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4bWw6c3BhY2U9InByZXNlcnZlIiBzdHlsZT0iZmlsbC1ydWxlOmV2ZW5vZGQ7Y2xpcC1ydWxlOmV2ZW5vZGQ7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO3N0cm9rZS1taXRlcmxpbWl0OjEuNDE0MjE7Ij4KICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDEsMCwwLDEsLTM3MTYuNDUsLTEzNTkpIj4KICAgICAgICA8ZyBpZD0iQXJ0Ym9hcmQzIiB0cmFuc2Zvcm09Im1hdHJpeCgwLjMzNjcsMCwwLDAuNDc2MTksMjQ2NS4yLDEzNTkpIj4KICAgICAgICAgICAgPHJlY3QgeD0iMzcxNi4yMSIgeT0iMCIgd2lkdGg9IjM1MDcuODciIGhlaWdodD0iMjQ4MC4zMiIgc3R5bGU9ImZpbGw6d2hpdGU7Ii8+CiAgICAgICAgICAgIDxnIHRyYW5zZm9ybT0ibWF0cml4KDIuOTcsMCwwLDIuMSwtNzMyMS42NSwtMjg1My45KSI+CiAgICAgICAgICAgICAgICA8cGF0aCBkPSJNNDA4Ni41MywxNDYxLjI3QzQyNzEuNzQsMTM3Ny42NSA0NTIwLjg5LDE1MjguNTUgNDY0Mi41NiwxNzk4LjA0QzQ3NjQuMjQsMjA2Ny41MyA0NzEyLjY3LDIzNTQuMjEgNDUyNy40NywyNDM3LjgzQzQzNDIuMjYsMjUyMS40NSA0MDkzLjExLDIzNzAuNTUgMzk3MS40NCwyMTAxLjA2QzM4NDkuNzYsMTgzMS41OCAzOTAxLjMzLDE1NDQuODkgNDA4Ni41MywxNDYxLjI3Wk00NDEwLjE0LDIzODAuNDdDNDI5My40NywyMzM1LjU1IDQxOTguOTcsMjI2NC45NyA0MTI2LjY0LDIxNjguNzJDNDA0MC4zMSwyMDUzLjggMzk5Ny4xNCwxOTE1LjI2IDM5OTcuMTQsMTc1My4xTDQwODAuMjYsMTc1My4xQzQwODAuMjYsMTg2Ny40MyA0MTA0Ljc2LDE5NjguOTMgNDE1My43NiwyMDU3LjZDNDE5OC4xLDIxMzYuOTMgNDI1Ni40MywyMTk3LjYgNDMyOC43NiwyMjM5LjZMNDMyOC43NiwxNzUzLjFMNDQxMC4xNCwxNzUzLjFMNDQxMC4xNCwyMzgwLjQ3WiIgc3R5bGU9ImZpbGw6cmdiKDE5MywyMjUsMTgzKTsiLz4KICAgICAgICAgICAgPC9nPgogICAgICAgIDwvZz4KICAgIDwvZz4KPC9zdmc+Cg==" );

if ( ! class_exists( 'Van_der_Graaf' ) ) :

class Van_der_Graaf
{
 
    /*
     *
     * Perform any initialisation now
     *
     */

    private $Updater = null;

	private $use_SSE = false;

	private $Storage = null;

	public $Settings = null;

    function __construct()
    {
		$this->Settings = new Van_der_Graaf_Settings;

        add_action( "admin_init", array( $this, "admin_init" ) );

        // Add a config panel
        add_action( 'admin_menu', array( $this, 'Van_der_Graaf_Config' ) );

		$this->Updater = new VanderGraaf_Updater;
		

        // Do not allow front end display for pages unless called admin
        add_filter( 'template_redirect', array( $this, 'verify_authorised_header' ), 1, 1 );

        add_action( 'post_submitbox_misc_actions', array( $this, "add_generate_button_to_editor" ) );


        // Do any init actions
        add_action( "init", array( $this, "init") );


		// give other plugins a chance to register their page generators

        do_action( "vandergraaf_generator_init", $this );

    }

    function admin_init( )
    {

		// We'll do all our processing in this
        add_action('admin_notices', array( $this, 'bulk_action_admin_notice' )  );

		// add_action('admin_footer-edit.php', array( $this, 'add_bulk_actions') );
        // add_action('load-edit.php', array( $this, 'process_bulk_action') );




		foreach( apply_filters( "vandergraaf_process_post_types", array( ) ) as $post_type  )
		{
			add_filter( 'bulk_actions-edit-'.$post_type, array( $this, 'add_bulk_actions') ) ;

			add_filter( 'handle_bulk_actions-edit-'.$post_type, array( $this, 'process_bulk_action' ), 10, 3 );

			add_filter( $post_type.'_row_actions', array( $this, 'add_row_actions' ), 10, 2);
			add_filter( 'manage_'.$post_type.'_posts_columns', array( $this, 'add_progress_column') );
			add_action( 'manage_'.$post_type.'_posts_custom_column', array( $this, 'display_progress_columns' ), 10, 2 );
		}


		add_action('admin_action_staticgen_page', array( $this, 'staticgen_page') );

		// add_filter('category_row_actions', array( $this, 'add_taxonomy_row_actions' ), 10, 2);
        // add_filter('tag_row_actions', array( $this, 'add_taxonomy_row_actions' ), 10, 2);

		foreach( apply_filters( "vandergraaf_process_taxonomy_types", array( ) ) as $tax_type  )
		{
			add_filter( 'bulk_actions-edit-'.$tax_type, array( $this, 'add_bulk_taxonomy_actions') ) ;

			add_filter( 'handle_bulk_actions-edit-'.$tax_type, array( $this, 'process_bulk_taxonomy_action' ), 10, 3 );


			add_filter( $tax_type.'_row_actions', array( $this, 'add_taxonomy_row_actions' ), 10, 2);
			add_filter( 'manage_edit-'.$tax_type.'_columns', array( $this, 'add_tax_progress_column') );
			add_action( 'manage_'.$tax_type.'_custom_column', array( $this, 'display_tax_progress_columns' ), 10, 3 );

			//add_action( "after-{$tax_type}-table", array( $this, "add_bulk_taxonomy_actions" ) );
		}

		add_action('admin_action_staticgen_taxonomy_page', array( $this, 'staticgen_taxonomy_page') );


        add_thickbox();

		// add_action( 'admin_enqueue_scripts', array( $this, "admin_enqueue_scripts" ) );

		add_action( 'wp_ajax_generate', array( $this, 'ajax_generate' ) );

		add_action( 'wp_ajax_generate_sse', array( $this, 'ajax_generate_sse' ) );

		add_action( 'wp_ajax_getstatus', array( $this, 'ajax_get_status' ) );

		add_filter( 'vandergraaf_deploy_to', array( $this, 'deploy_to_local') ) ;
	
	
        do_action( "vandergraaf_admin_init" );
		
		$this->create_general_config( );

		$this->create_local_deploy_config();

    }

	function deploy_to_local( $deploy_to )
	{

		$deploy_to['local'] = array( "Deploy Locally", "Van_der_Graaf_Local_Storage" );
		
		return $deploy_to;
	}

	function create_general_config( )
	{
		$this->Settings->addSection(
			"vdg_general",
			"General",
			function () {
				echo "<p>These settings control how Van der Graaf works</p>";
			},
			"vdg_general",
			function ( $oldvalue, $_newvalue ) {
				// $perms = fileperm( );

				write_log ( "Options Saved" );
			}
		);

		//wp_die( print_r( apply_filters( "vandergraaf_deploy_to", array( ) ), true ) );

        foreach ( array(

			array (
				'opt_name' => 'VDG_STATIC_URL',
				'field_name' => 'VDG_STATIC_URL',
				'label' => 'Static Site URL',
				'opt_val' => '',
				'type' => 'text',
				'placeholder' => "http://static-pages.dev",
				'size' => 64
			),

			array (
				'opt_name' => 'VDG_DEPLOY_TO',
				'field_name' => 'VDG_DEPLOY_TO',
				'label' => 'Deploy site to ',
				'type' => 'select',
				'opt_val' => apply_filters( "vandergraaf_deploy_to", array( ) )
			),

			array (
				'opt_name' => 'VDG_authorised_header_name',
				'field_name' => 'VDG_authorised_header_name',
				'label' => 'Authorised Header Name',
				'opt_val' => '',
				'type' => 'text',
				'placeholder' => 'Y_ORIGINATION_ID'
			),

			array (
				'opt_name' => 'VDG_authorised_header_id',
				'field_name' => 'VDG_authorised_header_id',
				'label' => 'Authorised Header Value',
				'opt_val' => '',
				'type' => 'text',
				'placeholder' => md5( "AuthorisedID:".time()."!" )
			),

			array (
				'opt_name' => 'VDG_SEO_noindex',
				'field_name' => 'VDG_SEO_noindex',
				'label' => 'SEO No Index',
				'opt_val' => '',
				'type' => 'checkbox',
				'placeholder' => 'N'
			),

			array (
				'opt_name' => 'VDG_SEO_nofollow',
				'field_name' => 'VDG_SEO_nofollow',
				'label' => 'SEO No Follow',
				'opt_val' => '',
				'type' => 'checkbox',
				'placeholder' => 'N'
			),
		) as $field ) 
		{
			$this->Settings->addField ( 
				"vdg_general", 
				"vdg_general", 
				$field
			);
		}
	}

	function create_local_deploy_config( )
	{
		$this->Settings->addSection(
			"vdg_local_deploy",
			"Local Deploy",
			function () {
				echo "<p>Configure local deployment option</p>";				
			},
			"vdg_local_deploy"
		);

        foreach ( array(
			array (
				'opt_name' => 'VDG_STATIC_ROOT',
				'field_name' => 'VDG_STATIC_ROOT',
				'label' => 'Local root folder',
				'opt_val' => '',
				'type' => 'text',
				'placeholder' => $this->from_path(),
				'size' => 64
			),
			array (
				'opt_name' => 'VDG_STATIC_IMAGE',
				'field_name' => 'VDG_STATIC_IMAGE',
				'label' => 'image folder name',
				'opt_val' => '',
				'type' => 'text',
				'placeholder' => $this->from_path()."/images",
				'size' => 64
			),
			array (
				'opt_name' => 'VDG_STATIC_CSS',
				'field_name' => 'VDG_STATIC_CSS',
				'label' => 'CSS folder name',
				'opt_val' => '',
				'type' => 'text',
				'placeholder' => $this->from_path()."/css",
				'size' => 64
			),
			array (
				'opt_name' => 'VDG_STATIC_JS',
				'field_name' => 'VDG_STATIC_JS',
				'label' => 'JS folder name',
				'opt_val' => '',
				'type' => 'text',
				'placeholder' => $this->from_path()."/js",
				'size' => 64
			),

		) as $field ) 
		{
			$this->Settings->addField ( 
				"vdg_local_deploy", 
				"vdg_local_deploy", 
				$field
			);
		}
	}

	function admin_enqueue_scripts()
	{
		wp_register_script( 'vandergraaf_processing', plugin_dir_url( __FILE__ ) . 'js/vandergraaf.js');
		wp_enqueue_script( "vandergraaf_processing" );


	}


    function init( )
    {
        /*Removes RSD, XMLRPC, WLW, WP Generator, ShortLink and Comment Feed links*/
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'feed_links', 2 );
        remove_action('wp_head', 'feed_links_extra', 3 );

        /*Removes prev and next article links*/
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');

        add_filter( 'wp_headers', array( $this, 'remove_pingback' ), 11, 2 );
        add_filter( 'bloginfo_url', array( $this, 'remove_bloginfo' ), 11, 2 );

        add_filter( 'xmlrpc_enabled', '__return_false' );

        add_filter('preview_post_link', array( $this, 'admin_post_link_filter'));
        add_filter('preview_page_link', array( $this, 'admin_post_link_filter'));

        // all actions related to emojis
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

        do_action( "vandergraaf_init_completed" );



    }


    function remove_pingback($headers, $wp_query)
    {
        if (array_key_exists('X-Pingback', $headers))
        {
            unset($headers['X-Pingback']);
        }

        return $headers;
    }


    function remove_bloginfo($output, $property)
    {
        return ( $property == 'pingback_url' ) ? null : $output;
    }

    /*
     *
     * We will try and force the wp-json rename when the plugin is activated
     *
     */

    function __plugin_activation( )
    {

        // Try and flush WP rewrite rules.  Same as updating the permalinks
        flush_rewrite_rules( true );

    }

    /*
     *
     * We will try and reset the api endpoint back to normal when we deactivate the endpoint
     *
     */

    function __plugin_deactivation( )
    {
        // Try and flush WP rewrite rules.  Same as updating the permalinks
        flush_rewrite_rules( true );
    }

    /*
     *
     * Restrict access to pages unless they have the correct header
     *
     */

    function verify_authorised_header( $template )
    {
        // Verify our header is present andf contains the correct value

        //echo "<pre>".print_r( $_SERVER, true )."</pre>";

		// write_log( $_SERVER );

        if ( !is_user_logged_in() )
        {

			// write_log ( "! is user logged in" );

            $authorised_header_name = "HTTP_".strtoupper( str_replace( "-", "_", get_option( "VDG_authorised_header_name" ) ) );
            $authorised_header_id = get_option( "VDG_authorised_header_id" );

            //echo "<pre>ahn : ".$authorised_header_name."</pre>";
            //echo "<pre>ahi : ".$authorised_header_id."</pre>";


            // If we don't have a valid login cookie then deny entry
            if ( !isset( $_SERVER[ $authorised_header_name ] ) )
            {
                WP_die( "Denied", 503 ) ;
                exit(503);
            }

            if ( $_SERVER[ $authorised_header_name ] != $authorised_header_id )
            {
                WP_die( "Denied", 503 ) ;
                exit(503);
            }

        }

       // echo "<pre>template : ".$template."</pre>";

        // If we get this far we're rockin'
        //return $template;
    }
    // **************************************************************************


    /*
     *
     * the actual config panel for the plugin
     *
     */

    function Van_der_Graaf_staticgen_options()
    {
        if ( !current_user_can( 'manage_options' ) )
        {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        // Now display the settings editing screen

        echo '<div class="wrap">';

        // header
        echo "<h1><img src='".plugins_url( "vandergraaf/vandergraaf-logo.png" )."' alt='". __( 'Van der Graaf', 'vandergraaf')."' /></h1>";

        echo "<h3>" . __( 'Static Site Config', 'vandergraaf' ) . "</h3>";

        // variables for the field and option names
        $opt_name = array(
                array (
                    'opt_name' => 'VDG_STATIC_URL',
                    'field_name' => 'VDG_STATIC_URL',
                    'label' => 'Static Site URL',
                    'opt_val' => '',
                    'type' => 'text',
                    'placeholder' => "http://static-pages.dev",
                    'size' => 64
                ),

                array (
                    'opt_name' => 'VDG_authorised_header_name',
                    'field_name' => 'VDG_authorised_header_name',
                    'label' => 'Authorised Header Name',
                    'opt_val' => '',
                    'type' => 'text',
                    'placeholder' => 'Y_ORIGINATION_ID'
                ),

                array (
                    'opt_name' => 'VDG_authorised_header_id',
                    'field_name' => 'VDG_authorised_header_id',
                    'label' => 'Authorised Header Value',
                    'opt_val' => '',
                    'type' => 'text',
                    'placeholder' => md5( "AuthorisedID:".time()."!" )
                ),

                array (
                    'opt_name' => 'VDG_SEO_noindex',
                    'field_name' => 'VDG_SEO_noindex',
                    'label' => 'SEO No Index',
                    'opt_val' => '',
                    'type' => 'checkbox',
                    'placeholder' => 'N'
                ),

                array (
                    'opt_name' => 'VDG_SEO_nofollow',
                    'field_name' => 'VDG_SEO_nofollow',
                    'label' => 'SEO No Follow',
                    'opt_val' => '',
                    'type' => 'checkbox',
                    'placeholder' => 'N'
                ),


        );

        $hidden_field_name = 'mt_submit_hidden';

		$tabs = array(
			'general' => array(	
				"name" => "General",
				"fields" =>"vdg_general",
				"page" => "vdg_general"			
			),
			'local_deploy' => array(
				"name" => "Local",
				"fields" =>"vdg_local_deploy",
				"page" => "vdg_local_deploy"			

			)			
		);


		$tabs = apply_filters( "vandergraaf_config_tabs", $tabs );
		
        // settings form

		if( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = $_GET[ 'tab' ];
		}
		else
		{
			$active_tab = $tabs['general']['page'];
		}

        ?>
		


		<h2 class="nav-tab-wrapper">
		<?php
		
			foreach( $tabs as $tab_name => $tab_options ) :
		?>		
			<a href="?page=vandergraaf-options&tab=<?php echo $tab_options['page']; ?>" class="nav-tab <?php echo ( $active_tab == $tab_options['page'] ? " nav-tab-active " : "" ); ?>" ><?php echo $tab_options['name']; ?></a>

		<?php
			endforeach;
		?>
		</h2>
            <form name="form1" method="post" action="options.php">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

            <?php
				// Output fields for all our options
				
				settings_fields ( $active_tab );
				do_settings_sections ( $active_tab );
			 ?>


			<?php submit_button(); ?>

            </form>
            </div>

        <?php

        // $url = $this->from_url( "" ) ;

        // $this->build_html_page( $url );

    }

    /*
     *
     * the actual config panel for the plugin
     *
     */

    function Van_der_Graaf_generator()
    {
        if ( !current_user_can( 'manage_options' ) )
        {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        ?>
        <h1><img src="<?php echo plugins_url( "vandergraaf/vandergraaf-logo.png" ); ?>" alt="" /></h1>
        <h3>The Static Generator</h3>

        <?php

        echo '<p><a href="'.admin_url().'admin.php?action=vandergraaf-site-build&width=640&height=550&TB_iframe=true" target="_self" class="thickbox"><button>Generate Static Site</button></a></p>';

        // echo '<p><a href="'.admin_url().'admin.php?action=vandergraaf-category-build&width=640&height=550&TB_iframe=true" target="_self" class="thickbox"><button>Generate Static Catagory pages</button></a></p>';

        // echo '<p><a href="'.admin_url().'admin.php?action=vandergraaf-tag-build&width=640&height=550&TB_iframe=true" target="_self" class="thickbox"><button>Generate Static Tag pages</button></a></p>';

        echo '<p><a href="'.admin_url().'admin.php?action=vandergraaf-taxonomy-build&width=640&height=550&TB_iframe=true" target="_self" class="thickbox"><button>Generate Static Taxonomy pages</button></a></p>';

        echo '<p><a href="'.admin_url().'admin.php?action=vandergraaf-chronos-build&width=640&height=550&TB_iframe=true" target="_self" class="thickbox"><button>Generate Static Chronological Archive pages</button></a></p>';
		

		$my_archives=wp_get_archives(array(
			'type'=>'alpha', 
			'show_post_count'=>true, 
			'limit'=>20, 
			'post_type'=>'post', 
			'format'=>'html' 
		));
			
		print_r($my_archives); 
		
		// $types = get_categories($args);

		// foreach( $types as $type )
        // {
		// 	echo "<pre>".print_r( $type, true )."</pre>";

        //     //$this->Post_Types[ $page->post_type ]( $page->ID, $page->post_type );
        //     //do_action( "vandergraaf_generate_category", $type->slug );

        // }

        // $url = $this->from_url( "" ) ;

        // $this->build_html_page( $url );

    }


    /*
     *
     * Add the config panel to the admin area
     *
     */

    function Van_der_Graaf_Config()
    {

        add_menu_page( "Van der Graaf",
                       "Van der Graaf",
                       "manage_options",
                       'vandergraaf',
                       array( $this, "Van_der_Graaf_generator") ) ;
                       //, plugins_url( 'vandergraaf/vandergraaf-icon.svg', dirname(__FILE__) ) ) ;

        add_submenu_page(
            'vandergraaf',
            'Static Site Config',
            'Config',
            'manage_options',
            'vandergraaf-options',
            array (
                $this,
                'Van_der_Graaf_staticgen_options'
            ) );

        // add_submenu_page(
        //     null,
        //     'Build All Pages',
        //     'build All Pages',
        //     'manage_options',
        //     'vandergraaf-site-build',
        //     array( $this, "staticgen_site")
        // );

        add_action( 'admin_action_vandergraaf-site-build',  array( $this, "staticgen_site") );

        add_action( 'admin_action_vandergraaf-category-build',  array( $this, "staticgen_category") );

        add_action( 'admin_action_vandergraaf-tag-build',  array( $this, "staticgen_tag") );

        add_action( 'admin_action_vandergraaf-taxonomy-build',  array( $this, "staticgen_taxonomies") );

        add_action( 'admin_action_vandergraaf-chronos-build',  array( $this, "staticgen_chronological") );
		
	}

    /*
    * Add a bulk action
    */

    function add_bulk_actions( $bulk_actions )
    {

        global $post_type;

        if ( in_array( $post_type, apply_filters( "vandergraaf_process_post_types", array( ) ) ) )
        {
			$bulk_actions['staticgen'] = __('Generate Static Page', 'vandergraaf' );
			return $bulk_actions;
        }
    }


    function add_bulk_taxonomy_actions( $bulk_actions )
    {

        global $post_type;

        if ( in_array( $_REQUEST['taxonomy'], apply_filters( "vandergraaf_process_taxonomy_types", array( ) ) ) )
        {
			$bulk_actions['staticgen_taxonomy'] = __('Generate Static Page', 'vandergraaf' );
			return $bulk_actions;
        }
    }



    /*
    * Process the bulk action 'mark_full'
    */
    function process_bulk_action( $redirect_to, $action, $post_ids )
    {
	global $post_type;

		$allowed_actions = array( "staticgen" );

		if ( !in_array( $action, $allowed_actions ) )
		{
			return $redirect_to;
		}

		if ( empty( $post_ids ) )
			return $redirect_to;

		// $selected = 0;
		// foreach( $post_ids as $post_id )
		// {
		// 	$selected++;
		// }

		$args = array
		(
			"post_type" => apply_filters( "vandergraaf_process_post_types", array( ) ),
			'post__in' => $post_ids,
			'post_status' => 'publish',
			'fields' => "ids"
		);

		$final_ids = get_posts( $args );

		set_transient( "vandergraaf_staticgen", $final_ids, 60 ); //  Short lived so does not repeat

		$redirect_to = add_query_arg( array( 'bulk_'.$action => count( $final_ids ), "post_type" => $post_type ) , $redirect_to );

		return $redirect_to;

	}



    /*
    * Process the bulk action 'mark_full'
    */
    function process_bulk_taxonomy_action( $redirect_to, $action, $post_ids )
    {
	global $taxonomy;

		$allowed_actions = array( "staticgen_taxonomy" );

		if ( !in_array( $action, $allowed_actions ) )
		{
			return $redirect_to;
		}

		if ( empty( $post_ids ) )
			return $redirect_to;

		$selected = count( $post_ids );

		set_transient( "vandergraaf_staticgen", $post_ids, 60 ); //  Short lived so does not repeat

		$redirect_to = add_query_arg( array( 'bulk_'.$action => count( $post_ids ), "taxonomy" => $taxonomy ), $redirect_to );

		return $redirect_to;

    }

	function add_progress_column($cols)
	{
		$cols = $this->array_insert( $cols, 2,  array( 'progress' => __('Progress') ) );

		return $cols;
	}


	function display_progress_columns($col, $id)
	{
		global $post;

		//$post = get_post( $id );

		switch($col)
		{
			case 'progress':

				echo "<div class='progress'>".
 						"<div class='".$post->post_type."-".$id."-progress progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100%' style='width:0;'></div>".
        				"</div>";

				break;
		}
	}


	function add_tax_progress_column($cols)
	{
		$cols = $this->array_insert( $cols, 2,  array( 'progress' => __('Progress') ) );

		return $cols;
	}


	function display_tax_progress_columns($content, $col, $id)
	{
		//$post = get_post( $id );
	global $taxonomy;

		switch($col)
		{
			case 'progress':

				echo "<div class='progress'>".
 						"<div class='".$taxonomy."-".$id."-progress progress-bar progress-bar-striped active' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100%' style='width:0;'></div>".
        				"</div>";

				break;
		}
	}

	function array_insert($array, $pos, $val)
	{
		$before = array_slice($array, 0, $pos);

		$after = array_slice($array, $pos);

		foreach ($val as $k => $v)
			$before[$k] = $v;

		foreach ($after as $k => $v)
			$before[$k] = $v;

		//array_push

		return $before;
	}




	function bulk_action_admin_notice()
	{
		if ( ! empty( $_REQUEST['bulk_staticgen'] ) )
		{

			printf(
				'<div id="message" class="updated ">Processing</div>'
			);

			if ( empty( $_REQUEST[ 'post_type' ] ) )
				$post_type= 'post';
			else
				$post_type = $_REQUEST[ 'post_type' ];


			$pages = get_transient( "vandergraaf_staticgen" );

			if ( $pages !== false )
			{
				delete_transient( "vandergraaf_staticgen" );

				$transients = array( );

				foreach ( $pages as $id )
				{

					$transients[ ] = array(
						"id" => $id,
						"type" => $post_type,
						"url" => get_permalink( $id ),
						"status" => -1,
						"start" => 0,
						"end" => 0
					);

				}

				set_transient( "vandergraaf_processing", $transients, HOUR_IN_SECONDS );

				foreach( $transients as $trans )
					set_transient( "vandergraaf_processing_".$trans['type']."_".$trans['id'], $trans, HOUR_IN_SECONDS );


				echo $this->script_variables( $transients, true );

			}

		}


		if ( ! empty( $_REQUEST['bulk_staticgen_taxonomy'] ) )
		{

			if ( !empty( $_REQUEST[ 'taxonomy' ] ) )
			{
				printf(
					'<div id="message" class="updated ">Processing</div>'
				);

				$taxonomy = $_REQUEST[ 'taxonomy' ];


				$pages = get_transient( "vandergraaf_staticgen" );

				if ( $pages !== false )
				{
					delete_transient( "vandergraaf_staticgen" );

					$transients = array( );

					foreach ( $pages as $id )
					{
						//echo "<pre>".print_r( $id, true )."</pre>";

						$transients[ ] = array(
							"id" => $id,
							"type" => $taxonomy,
							"url" => get_term_link( (int)$id ),
							"status" => -1,
							"start" => 0,
							"end" => 0
						);

					}

					set_transient( "vandergraaf_processing", $transients, HOUR_IN_SECONDS );

					foreach( $transients as $trans )
						set_transient( "vandergraaf_processing_".$trans['type']."_".$trans['id'], $trans, HOUR_IN_SECONDS );

					echo $this->script_variables( $transients, true );

				}

			}
		}

	}


    /*
    * Show an admin notice for this mark_full bulk action.
    *
    */
    function bulk_order_status_messages() {

        global $post_type, $pagenow;

        //print_r( $pagenow );

        //print_r( $post_type );

        if ( $pagenow == 'edit.php' && in_array( $post_type, apply_filters( "vandergraaf_process_post_types", array( ) ) )  && isset( $_REQUEST['cmd'] ) )
        {
            $message = "Processing"; //sprintf( _n( 'Post selected.', '%s posts selected.', $_REQUEST['selected'] ), number_format_i18n( $_REQUEST['selected'] ) );
            echo '<div class="updated"><p>'.$message.'</p></div>';

            switch ( $_REQUEST['cmd'] )
            {
                case 'staticgen' :

                    //$pages = explode( ',', $_REQUEST['ids'] );
					$pages = get_transient( "vandergraaf_staticgen" );

					if ( $pages !== false )
					{
						delete_transient( "vandergraaf_staticgen" );

						$transients = array( );

						foreach ( $pages as $id )
						{

							$transients[ ] = array(
								"id" => $id,
								"type" => $post_type,
								"url" => get_permalink( $id ),
								"status" => -1,
								"start" => 0,
								"end" => 0
							);

						}

						set_transient( "vandergraaf_processing", $transients, HOUR_IN_SECONDS );

						foreach( $transients as $trans )
							set_transient( "vandergraaf_processing_".$trans['type']."_".$trans['id'], $trans, HOUR_IN_SECONDS );


						echo $this->script_variables( $transients, true );

						// echo "<table style='fomt-size:9px' class='table table-striped'>".
						// 		"<thead>".
						// 			"<th>Page</th>".
						// 			"<th>Progress</th>".
						// 		"</thead>".
						// 		"<tbody id='vandergraaf_processing'>".
						// 		"</tbody>".
						// 	"</table>";

						// echo "<script>tb_show();</script>";


						// //echo "<pre>".print_r( $pages, true )."</pre>";

						// foreach( $pages as $p_id )
						// {

						//     // if ( isset( $this->Post_Types[ $post_type ] ) )
						//     //     $this->Post_Types[ $post_type ]( $p_id, $post_type );

						//     do_action( "vandergraaf_generate_{$post_type}", $p_id );

						// }

						// // if ( isset( $this->Data_Types[ $post_type ] ) )
						// //     $this->Data_Types[ $post_type ]( $post_type );
					}

                    break;
            }
        }
    }


    function add_taxonomy_row_actions($actions, $tag)
    {

        //check for your post type
        if ( in_array( $tag->taxonomy,  apply_filters( "vandergraaf_process_taxonomy_types", array( ) ) ) )
        {

            $actions['staticgen'] = '<a href="'.admin_url().'admin.php?action=staticgen_taxonomy_page&id='.$tag->term_id.'&tax='.$tag->taxonomy.'&width=640&height=100&TB_iframe=true" target="_self" class="thickbox">Generate Static Page</a>';
        }

        return $actions;
    }




    function add_row_actions($actions, $post)
    {
        //check for your post type
        if ( in_array( $post->post_type,  apply_filters( "vandergraaf_process_post_types", array( ) ) ) )
        {
            $actions['staticgen'] = '<a href="'.admin_url().'admin.php?action=staticgen_page&id='.$post->ID.'&type='.$post->post_type.'&width=640&height=100&TB_iframe=true" target="_self" class="thickbox">Generate Static Page</a>';
        }

        return $actions;
    }


    function staticgen_taxonomy_page()
    {
        global $wpdb;

        extract ( array_merge( array ( 'id' => 0, 'action' => 'new', 'tax' => 'category' ), $_GET ) );

        if ( in_array( $tax,  apply_filters( "vandergraaf_process_taxonomy_types", array( ) ) ) )
		{
            //  do_action( "vandergraaf_generate_{$tax}", $id );
			$transients = array( );


			$transients[ ] = array(
				"id" => $id,
				"type" => $tax,
				"url" => get_term_link( (int)$id, $tax ),
				"status" => -1,
				"start" => 0,
				"end" => 0
			);


			set_transient( "vandergraaf_processing", $transients, HOUR_IN_SECONDS );

			foreach( $transients as $trans )
				set_transient( "vandergraaf_processing_".$trans['type']."_".$trans['id'], $trans, HOUR_IN_SECONDS );

			echo $this->script_variables( $transients );

			echo "<table style='fomt-size:9px' class='table table-striped'>".
					"<thead>".
						"<th>Page</th>".
						"<th>Progress</th>".
					"</thead>".
					"<tbody id='vandergraaf_processing'>".
					"</tbody>".
				 "</table>";
		}

	}


    function staticgen_page()
    {
        global $wpdb;

        extract ( array_merge( array ( 'post' => 0, 'action' => 'new', 'type' => 'post' ), $_GET ) );

        //echo "<pre>".print_r( $_GET, true )."</pre>";


        if ( in_array( $type,  apply_filters( "vandergraaf_process_post_types", array( ) ) ) )
		{
        //     do_action( "vandergraaf_generate_{$type}", $id );

			$transients = array( );


			$transients[ ] = array(
				"id" => $id,
				"type" => $type,
				"url" => get_permalink( $id ),
				"status" => -1,
				"start" => 0,
				"end" => 0
			);


			set_transient( "vandergraaf_processing", $transients, HOUR_IN_SECONDS );

			foreach( $transients as $trans )
				set_transient( "vandergraaf_processing_".$trans['type']."_".$trans['id'], $trans, HOUR_IN_SECONDS );

			echo $this->script_variables( $transients );

			echo "<table style='fomt-size:9px' class='table table-striped'>".
					"<thead>".
						"<th>Page</th>".
						"<th>Progress</th>".
					"</thead>".
					"<tbody id='vandergraaf_processing'>".
					"</tbody>".
				 "</table>";
		}
    }


    function staticgen_site()
    {

		$transients = array( );

		delete_transient( "vandergraaf_processing" );

        $args = array(
            'numberposts' => -1,
            "post_type" => apply_filters( "vandergraaf_process_post_types", array( ) ),
			'post_status' => "publish"
        );

        $pages = get_posts( $args );

        foreach( $pages as $page )
        {
            //$this->Post_Types[ $page->post_type ]( $page->ID, $page->post_type );
            // do_action( "vandergraaf_generate_{$page->post_type}", $page->ID );

			$transients[ ] = array(
				"id" => $page->ID,
				"type" => $page->post_type,
				"url" => get_permalink( $page->ID ),
				"status" => -1,
				"start" => 0,
				"end" => 0
			);


        }

		write_log( count( $pages ). " Added" );

		$args = array(
			"taxonomy"  => apply_filters( "vandergraaf_process_taxonomy_types", array( ) )
		);

		$types = get_terms( $args);

		foreach( $types as $type )
        {
			//echo "<pre>".print_r( $type, true )."</pre>";

			$transients[ ] = array(
				"id" => $type->term_id,
				"type" => $type->taxonomy,
				"url" => get_term_link( $type->term_id ),
				"status" => -1,
				"start" => 0,
				"end" => 0
			);

        }

		set_transient( "vandergraaf_processing", $transients, HOUR_IN_SECONDS );

		foreach( $transients as $trans )
			set_transient( "vandergraaf_processing_".$trans['type']."_".$trans['id'], $trans, HOUR_IN_SECONDS );


		echo $this->script_variables( $transients );

		echo "<table style='fomt-size:9px' class='table table-striped'>".
				"<thead>".
					"<th>Page</th>".
					"<th>Progress</th>".
				"</thead>".
				"<tbody id='vandergraaf_processing'>".
				"</tbody>".
				"</table>";

    }



    function staticgen_taxonomies()
    {

		// generate categories

		$args = array(
			"taxonomy"  => apply_filters( "vandergraaf_process_taxonomy_types", array( ) )
		);

		$types = get_terms( $args);

		delete_transient( "vandergraaf_processing" );

		$transients = array( );

		foreach( $types as $type )
        {
			//echo "<pre>".print_r( $type, true )."</pre>";

			$transients[ ] = array(
				"id" => $type->term_id,
				"type" => $type->taxonomy,
				"url" => get_term_link( $type->term_id ),
				"status" => -1,
				"start" => 0,
				"end" => 0
			);

        }

		// $this->clear_cache();

		set_transient( "vandergraaf_processing", $transients, HOUR_IN_SECONDS );

		foreach( $transients as $trans )
			set_transient( "vandergraaf_processing_".$trans['type']."_".$trans['id'], $trans, HOUR_IN_SECONDS );

		//wp_localize_script( 'vandergraaf_processing', 'transients', $transients );

		echo $this->script_variables( $transients );

		echo "<table id='vandergraaf_processing' style='fomt-size:9px' class='widefat fixed'></table>";


    }


    function staticgen_chronological()
    {

		$transients = array( );

        $args = array(
            'numberposts' => -1,
            "post_type" => apply_filters( "vandergraaf_process_post_types", array( ) ),
			'post_status' => "publish"

        );

        $pages = get_posts( $args );

		// build array of date archives to poll

		$this->page_builder = new Van_der_Graaf_Process_Page();


        foreach( $pages as $page )
        {
            //$this->Post_Types[ $page->post_type ]( $page->ID, $page->post_type );
            // do_action( "vandergraaf_generate_{$page->post_type}", $page->ID );

			// $transients[ ] = array(
			// 	"id" => $page->ID,
			// 	"type" => $page->post_type,
			// 	"url" => get_permalink( $page->ID ),
			// 	"status" => -1,
			// 	"start" => 0,
			// 	"end" => 0
			// );

			$this->page_builder->push_to_queue( array(
				"id" => $page->ID,
				"type" => $page->post_type,
				"url" => get_permalink( $page->ID ),
				"status" => -1,
				"start" => 0,
				"end" => 0
			) );
			
			$this->page_builder->dispatch();

        }

		

		// set_transient( "vandergraaf_processing", $transients, HOUR_IN_SECONDS );

		// foreach( $transients as $trans )
		// 	set_transient( "vandergraaf_processing_".$trans['type']."_".$trans['id'], $trans, HOUR_IN_SECONDS );


		// echo $this->script_variables( $transients );

		// echo "<table style='fomt-size:9px' class='table table-striped'>".
		// 		"<thead>".
		// 			"<th>Page</th>".
		// 			"<th>Progress</th>".
		// 		"</thead>".
		// 		"<tbody id='vandergraaf_processing'>".
		// 		"</tbody>".
		// 		"</table>";

    }
	
	function script_variables( $transients = array(), $create_table = true )
	{

		return "<link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous'>\n".

		        "<script type='text/javascript'>\n".
					'  var ajax_url = "'.admin_url( 'admin-ajax.php' ).'";'.
					'  var filter_count = '.$this->count_filters( "vandergraaf_page_generator" ).';'.
					'  var urls = ' . wp_json_encode( $transients ) . ';'.
					'  var createTable = ' . $create_table . ';'.
				"\n</script>\n".

				"<script type='text/javascript' src='".includes_url( "js/jquery").'/jquery.js' . "'></script>\n".

				"<script type='text/javascript' src='". plugin_dir_url( __FILE__ ) . 'js/vandergraaf.js' . "'></script>\n".

				"<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' integrity='sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa' crossorigin='anonymous'></script>\n";



	}


	function count_filters( $tag )
	{
		global $wp_filter;

		$count = 0;

		if ( isset( $wp_filter[ $tag ] ) )
		{
			foreach( $wp_filter[ $tag ]->callbacks as $priority => $filters )
			{
				$count += count( $filters );
			}

			return $count;
		}

		return 0;
	}



	function clear_cache()
	{
		// Get our cache
		$cache = get_transient( "vandergraaf_cache" );

		// if we don't have a cache just bail
		if ( $cache === false )
			return;

		// Delete each cached record
		foreach( $cache as $cached )
			delete_transient( "vandergraaf_cache_{$cached}" );

		// delete the cache
		delete_transient( "vandergraaf_cache" );
	}


	function cache_file( $o_file, $file )
	{
		// get our cache
		$cache = get_transient( "vandergraaf_cache" );

		// if it doesn't exist create it
		if ( $cache === false )
			$cache = array();


		$key = md5( $o_file );

		// Add our file to the cache
		$cache[ $key ] = array( "from"=> $o_file, "to" => $file );

		// Store the cache
		set_transient( "vandergraaf_cache", $cache, HOUR_IN_SECONDS );

		// Add a cache entry for the file
		set_transient( "vandergraaf_cache_{$key}", array( "from"=> $o_file, "to" => $file ), HOUR_IN_SECONDS );
	}


	function is_cached_file( $o_file )
	{

		return false;
		$key = md5( $o_file );

		// get our cache
		$file = get_transient( "vandergraaf_cache_{$key}" );

		// echo "<pre>".print_r( $cache, true )."</pre>";

		// $this->send_SSE( 0, "comment", json_encode( $file ) );

		// if it doesn't exist return false
		if ( $file === false )
			return false;

		// echo "<pre> looking for {$key}".isset( $cache[ $key ]) ."</pre>";

		return true; //isset( $cache[ $key ] ) ;
	}


	function cached_file( $o_file )
	{
		// get our cache
		$key = md5( $o_file );

		// get our cache
		$file = get_transient( "vandergraaf_cache_{$key}" );

		// echo "<pre>".print_r( $cache, true )."</pre>";

		// if it doesn't exist return false
		if ( $file === false )
			return false;

		// echo "<pre> looking for {$key}".isset( $cache[ $key ]) ."</pre>";

		return $file['to'];
	}


	function ajax_generate( )
	{
		$this->use_SSE = false;

		$start = microtime( true );

		$transients = get_transient( "vandergraaf_processing" );

		// if we don't have a cache just bail
		if ( $transients === false )
			wp_die( json_encode( $transients ), array( "response" => 404 ) );

		foreach( $transients as $page )
		{
			$type = $page['type'];
			$id = $page['id'];

			// echo "data: started ".$type."-".$id."\n\n";

			do_action( "vandergraaf_generate_{$type}", $id );
		}
		$end = microtime( true );

		//delete_transient( "vandergraaf_processing" );

		wp_die( json_encode( array( "start" => $start, "end" => $end ) ), array( "response" => 200 ) );
	}


	function ajax_generate_SSE( )
	{
		ob_implicit_flush( true );
		header("Access-Control-Allow-Origin: *" );
		header("Content-type: text/event-stream" );

		$this->use_SSE = true;

		$start = microtime( true );

        //$this->send_SSE_retry( 0, 30000 );
		
        $this->send_SSE( 0, "starting", json_encode( array( "start" => $start ) ) );

		$pages = get_transient( "vandergraaf_processing" );

		// if we don't have a cache just bail
		if ( $pages !== false )
		{
			foreach( $pages as $page )
			{
				$type = $page['type'];
				$id = $page['id'];

				// echo "data: started ".$type."-".$id."\n\n";

				$stat = get_transient( "vandergraaf_processing_".$page['type']."_".$page['id'] );
				
				$this->send_SSE( 0, "starting", json_encode( $stat ) );
				
				if ($stat != null && $stat['status'] != 99999 )  // Avoid reprocessing
					do_action( "vandergraaf_generate_{$type}", $id );
			}
		}
		$end = microtime( true );

		//delete_transient( "vandergraaf_processing" );

		 $this->send_SSE( 0, "completed", json_encode( array( "start" => $start, "end" => $end ) ) );
	}


	function ajax_get_status( )
	{
		$this->use_SSE = true;

		$pages = get_transient( "vandergraaf_processing" );

		// if we don't have a cache just bail
		if ( $pages === false )
			return;

		$statii = array ( );

		// Delete each cached record
		foreach( $pages as $status )
		{
			$stat = get_transient( "vandergraaf_processing_".$status['type']."_".$status['id'] );

			$statii [ ] = array ( "id" => $status['id'], "type" => $status['type'], "status" => $stat['status'] );
		}

		wp_die( json_encode( $statii ), array( "response" => 200 ) );

	}


	function send_SSE_retry( $id, $retry )
	{
		ob_start( );
		// echo "id: ".$counter."\n";
		echo "retry: ".$retry."\n\n";
		ob_flush();
		flush();
	}


	function send_SSE( $id, $event, $data = "" )
	{
		ob_start( );
		// echo "id: ".$counter."\n";
		echo "event: ".$event."\n";
		echo "data: ".$data."\n\n";
		ob_flush();
		flush();
	}

	function status_mapper( $status )
	{
		return array( "id" => $status['id'], 'type' => $status['type'], 'status' => $status['status'] );
	}


	function setStatus( $id, $post_type, $status )
	{
		$transients = get_transient( "vandergraaf_processing_".$post_type."_".$id );

		if ( $transients !== false )
		{
			$transients[ 'status' ] = $status;

			set_transient( "vandergraaf_processing_".$post_type."_".$id, $transients, HOUR_IN_SECONDS );

			if ( $this->use_SSE )
				$this->send_SSE( 0, "update", json_encode( array( "type" => $post_type, "id" => $id, "status" => $status ) ) );
		}


	}

	function incStatus( $id, $post_type )
	{
		$transients = get_transient( "vandergraaf_processing_".$post_type."_".$id);

		if ( $transients !== false )
		{
			$transients[ 'status' ] ++;

			set_transient( "vandergraaf_processing_".$post_type."_".$id, $transients, HOUR_IN_SECONDS );

			if ( $this->use_SSE )
				$this->send_SSE( 0, "update", json_encode( array( "type" => $post_type, "id" => $id, "status" => $transients[ 'status' ] ) ) );

		}

	}

    function admin_post_link_filter( $link )
    {

		$link = str_replace( $this->to_url(), $this->from_url(), $link );

        return $link;
    }


    function generate_static_html( $id, $type = 'post' )
    {

        do_action( "vandergraaf_generate_{$type}", $id );

    }


    function add_mime_types($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }


    function add_generate_button_to_editor( $post )
    {

       if ( in_array( $post->post_type , apply_filters( "vandergraaf_process_post_types", array() ) ) )
            echo '<a class="thickbox button button-primary button-large" style="margin:10px;" href="'.admin_url().'admin.php?action=staticgen_page&id='.$post->ID.'&type='.$post->post_type.'&width=640&height=550&TB_iframe=true" target="_self" id="post-preview">Generate Static Files<span class="screen-reader-text"> (opens in a new window)</span></a>';

    }

    function from_url( )
    {
        return site_url();
    }


    function from_path()
    {
        $cwd = realpath( dirname( __FILE__ ) );

		write_log( "cwd => ".$cwd ); 

        $parts = explode( DIRECTORY_SEPARATOR, $cwd );

        // write_log( $parts );
		
        // we are 3 folders from the root.  ROOT/WP_CONTENT/PLUGINS/THIS_PLUGIN

        array_pop( $parts );
        array_pop( $parts );
        array_pop( $parts );

        // write_log( $parts );

		$path = implode( "/", $parts );

		write_log( $path );
        return $path;
    }


    function to_url()
    {
        return get_option( "VDG_STATIC_URL" );
    }


    function to_path( )
    {
        return get_option( "VDG_STATIC_ROOT" );
    }


}
// Create an instance of our Plugin and assign to a global for use later
$VdG = new Van_der_Graaf();

// Register our activation and deactivation actions.
//register_deactivation_hook( __FILE__, [ 'Van_der_Graaf', '__plugin_deactivation'] );
//register_activation_hook( __FILE__, [ 'Van_der_Graaf', '__plugin_activation'] );

endif;



if ( ! function_exists('write_log')) {
	function write_log ( $log )  {
	   if ( is_array( $log ) || is_object( $log ) ) {
		  error_log( print_r( $log, true ) );
	   } else {
		  error_log( $log );
	   }
	}
 }
 