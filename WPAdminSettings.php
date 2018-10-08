<?php
/*
Plugin Name: WPAdminSettings
Plugin URI: https://www.pickplugins.com/item/site-builder/?ref=dashboard
Description: Zero coding skill required to build your own WordPress site.
Version: 1.0.0
Text Domain: site-builder
Domain Path: /languages
Author: PickPlugins
Author URI: http://pickplugins.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 



class WPAdminSettingsPlugin{

    public function __construct(){

        $this->define_constants();
        $this->declare_classes();
        $this->load_script();
        $this->load_functions();

        //register_activation_hook( __FILE__, array( $this, 'activation' ) );
        //add_action( 'plugins_loaded', array( $this, 'textdomain' ));

    }

    public function activation() {



    }

    public function textdomain() {

        $locale = apply_filters( 'plugin_locale', get_locale(), 'site-builder' );
        load_textdomain('site-builder', WP_LANG_DIR .'/site-builder/site-builder-'. $locale .'.mo' );
        load_plugin_textdomain( 'site-builder', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
    }



    public function load_functions() {

        require_once( PLUGIN_DIR . 'functions-settings.php');

    }


    public function load_script() {

        add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
        add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    }



    public function declare_classes() {

        require_once( PLUGIN_DIR . 'class-WPAdminSettings.php');


    }

    public function define_constants() {

        $this->define('PLUGIN_URL', plugins_url('/', __FILE__)  );
        $this->define('PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        $this->define('PLUGIN_NAME', __('Question Answer', 'site-builder') );
        $this->define('PLUGIN_SUPPORT', 'http://www.pickplugins.com/questions/'  );

    }

    private function define( $name, $value ) {
        if( $name && $value )
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
    }





    public function front_scripts(){

//        wp_enqueue_script('qa_front_js', plugins_url( '/assets/front/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
//        wp_enqueue_script('front_scripts-form', plugins_url( '/assets/front/js/scripts-form.js' , __FILE__ ) , array( 'jquery' ));
//        wp_enqueue_style('qa-user-profile.css', PLUGIN_URL.'assets/front/css/user-profile.css');

    }

    public function admin_scripts(){

        wp_enqueue_script('jquery');
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('jquery-ui-datepicker');

//        wp_enqueue_script('vuejs', plugins_url( '/assets/admin/js/vue.js' , __FILE__ ));
//
//        wp_enqueue_script('Sortable', plugins_url( '/assets/admin/js/Sortable.js' , __FILE__ ));
//        wp_enqueue_script('vue-sortable', plugins_url( '/assets/admin/js/vue-sortable.js' , __FILE__ ));
//
//        wp_enqueue_script('vue-components', plugins_url( '/assets/admin/js/vue-components.js' , __FILE__ ));
//
//        wp_enqueue_style('vue-components', PLUGIN_URL.'assets/admin/css/vue-components.css');
//
//        wp_enqueue_style('site-builder', PLUGIN_URL.'assets/admin/css/site-builder.css');
//        wp_enqueue_style('bootstrap', PLUGIN_URL.'assets/global/css/bootstrap.css');
//        wp_enqueue_style('fontawesome.min', PLUGIN_URL.'assets/global/css/fontawesome.min.css');


        //wp_enqueue_style('qa_admin_style', PLUGIN_URL.'assets/admin/css/style.css');

    }


} new WPAdminSettingsPlugin();