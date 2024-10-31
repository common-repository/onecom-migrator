<?php
/**
 * Plugin Name:         one.com Migrator
 * Plugin URI:          https://www.one.com/en/wordpress-hosting
 * Description:         one.com migrator plugin helps to migrate a WordPress site from previous hosting provider to one.com
 * Version:             0.1.2
 * Requires at least:   5.9
 * Requires PHP:        7.4
 * Author:              one.com
 * Author URI:          https://one.com/
 * Text Domain:         onecom-migrator
 * Domain Path:         /languages
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);
defined( 'ABSPATH' ) or die( 'Cheating Huh!' );

//define text-domain
if ( ! defined( 'ONECOM_MIG_DOMAIN' ) ) {
    define('ONECOM_MIG_DOMAIN', 'onecom-migrator');
}

//define plugin version
if ( ! defined( 'ONECOM_WP_MIG_VERSION' ) ) {
    define( 'ONECOM_WP_MIG_VERSION', '0.1.2' );
}

//define plugin directory path
if ( ! defined( 'ONECOM_WP_MIG_PATH' ) ) {
    define( 'ONECOM_WP_MIG_PATH', plugin_dir_path( __FILE__ ) );
}

//define plugin dir url
if ( ! defined( 'ONECOM_WP_MIG_URL' ) ) {
    define( 'ONECOM_WP_MIG_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'OC_PLUGIN_API_VERSION' ) ) {
    define( 'OC_PLUGIN_API_VERSION', '1' );
}

$filename = 'onecommigrator';
$include_file = ONECOM_WP_MIG_PATH . 'inc/classes' . DIRECTORY_SEPARATOR . 'class-' . $filename . '.php';
if (file_exists($include_file)) {
    require $include_file;
}

/* Plugins REST API */
add_action( 'rest_api_init', function () {
    $oc_api_class = ONECOM_WP_MIG_PATH . 'inc/api/class-oc-plugins-api.php';

    if ( file_exists( $oc_api_class ) ) {
        require_once( $oc_api_class );
        $oc_api = new OCPluginsApi();
        $oc_api->register_routes();
    }
} );

//Initiate OneComMigrator Class
new OneComMigrator();