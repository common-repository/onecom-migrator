<?php
if(!class_exists('OneComMigrator')){
    class OneComMigrator{

        const ONECOM_MENU_SLUG = 'onecom-wp';//onecom menu slug
        public $oc_inline_logo;//onecom menu icon

        /**
         * Entry point
         */
        public function __construct()
        {
            add_action( 'plugins_loaded', array($this,'onecom_wp_mg_load_textdomain'), - 1 );
            add_action('admin_menu', array($this, 'onecom_register_mg_menu'),10);
            add_action('network_admin_menu', array($this, 'onecom_register_mg_menu'),10);
            add_action('admin_menu', array($this, 'onecom_remove_menu_mg'),10);
            add_action( 'admin_enqueue_scripts', array($this, 'add_migrator_page_css' ),10);

            $this->oc_inline_logo = sprintf( '<img src="%s" alt="%s" style="height:19px;"/>', ONECOM_WP_MIG_URL . 'assets/images/one.com.black.svg', __( 'One.com', ONECOM_MIG_DOMAIN ) );
        }

        /**
         * Add admin css for migrator menu page
         */
        public function add_migrator_page_css($hook_suffix){

            $resource_extension = ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : '.min'; // Adding .min extension if SCRIPT_DEBUG is enabled
            $resource_min_dir = ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : 'min-'; // Adding min- as a minified directory of resources if SCRIPT_DEBUG is enabled

            wp_register_style('onecom-migrator-css', ONECOM_WP_MIG_URL . 'assets/'.$resource_min_dir.'css/onecom-migrator'.$resource_extension.'.css');
            wp_register_style('onecom-migrator-icon-css', ONECOM_WP_MIG_URL . 'assets/'.$resource_min_dir.'css/onecom-icon'.$resource_extension.'.css');

            //add migrator page css
            if('_page_onecom-migrator' === $hook_suffix ){
                wp_enqueue_style( 'onecom-migrator-css' );
            }

            //add icon css
            if(!$this->onecom_plugin_activation_check()){
                wp_enqueue_style( 'onecom-migrator-icon-css' );
            }
        }

        /**
         * Register Menu
         */
        public function onecom_register_mg_menu(){
            global $submenu;

            $menuname = 'one.com Migrator';
            $menuslug = 'onecom-migrator';

            if($this->onecom_plugin_activation_check()){
                //call submenu page
                $this->addsubmenu($submenu,$menuname,$menuslug);
            }else if(!$this->onecom_plugin_activation_check()){

                //call for register menu
                $this->onecom_addmenu();

                //then call submenu page
                $this->addsubmenu($submenu,$menuname,$menuslug);

            }
        }

        public function onecom_remove_menu_mg(){

            remove_menu_page('onecom-vcache-plugin');
            remove_menu_page('onecom-wp-under-construction');
            remove_submenu_page(self::ONECOM_MENU_SLUG, self::ONECOM_MENU_SLUG);

        }

        /**
         * @return bool
         * Check one.com plugin is activated or not
         */
        public function onecom_plugin_activation_check(): bool
        {
            return (is_plugin_active("onecom-themes-plugins/onecom-themes-plugins.php")) ;
        }

        /**
         * Add menu page
         */
        public function onecom_addmenu(){
            $position = $this->onecom_get_free_menu_position_from_mg('2.1');
            global $admin_page_hooks;

            if(!array_key_exists("onecom-wp",$admin_page_hooks)) {
                add_menu_page(
                    __('One.com', ONECOM_MIG_DOMAIN),
                    $this->oc_inline_logo,
                    'manage_options',
                    self::ONECOM_MENU_SLUG,
                    '',
                    'dashicons-admin-generic',
                    $position
                );
            }
        }

        /**
         * @param $submenu
         * @param $menuname
         * @param $menuslug
         *
         * Add Submenu
         */
        public function addsubmenu($submenu,$menuname,$menuslug){
            add_submenu_page(self::ONECOM_MENU_SLUG, __($menuname, ONECOM_MIG_DOMAIN), '<span id="onecom_migrator">' . __($menuname, ONECOM_MIG_DOMAIN) . '</span>', 'manage_options', $menuslug, array($this,'onecom_migrator_admin_page'), 16);
        }

        /**
         * @param $start
         * @param float $increment
         * @return mixed|string
         *
         * Get Menu position
         */
        public function onecom_get_free_menu_position_from_mg($start, $increment = 0.3){
            foreach ( $GLOBALS['menu'] as $key => $menu ) {
                $menus_positions[] = $key;
            }

            if ( ! in_array( $start, $menus_positions ) ) {
                return $start;
            }

            /* the position is already reserved find the closet one */
            while ( in_array( $start, $menus_positions ) ) {
                $start += $increment;
            }

            return (string) $start;
        }

        /**
         * Include Admin page
         */
        public function onecom_migrator_admin_page(){
            include_once ONECOM_WP_MIG_PATH.'inc/templates/migrator-admin-template.php';
        }

        /**
         * Load lang
         */
        public function onecom_wp_mg_load_textdomain() {

            // load english tranlsations [as] if any unsupported language is selected in WP-Admin
            if ( strpos( get_locale(), 'en_' ) === 0 ) {
                load_textdomain( ONECOM_MIG_DOMAIN, ONECOM_WP_MIG_PATH . 'languages/onecom-wp-en_US.mo' );
            } else if ( strpos( get_locale(), 'pt_BR' ) === 0 ) {
                load_textdomain( ONECOM_MIG_DOMAIN, ONECOM_WP_MIG_PATH . 'languages/onecom-wp-pt_PT.mo' );
            } else {
                load_plugin_textdomain( ONECOM_MIG_DOMAIN, false, ONECOM_WP_MIG_PATH . 'languages' );
            }
        }
    }
}