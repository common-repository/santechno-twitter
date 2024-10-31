<?php
/*
    Plugin Name: santechnologies Twitter
    Plugin URI: http://www.idevelopwebsite.com
    Description: Plugin for displaying Twitter feed on the site
    Author: Nabaz Maaruf
    Version: 1.2
    Author URI: http://www.idevelopwebsite.com
    */

// Exit if accessed directly
if (!defined('SANTECH_THEME_DIR'))
    define('SANTECH_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());

if (!defined('SANTECH_PLUGIN_NAME'))
    define('SANTECH_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('SANTECH_PLUGIN_DIR'))
    define('SANTECH_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . SANTECH_PLUGIN_NAME);

if (!defined('SANTECH_PLUGIN_URL'))
    define('SANTECH_PLUGIN_URL', WP_PLUGIN_URL . '/' . SANTECH_PLUGIN_NAME);
if ( ! defined( 'ABSPATH' ) ) echo 'cannot access redirect';

include_once dirname( __FILE__ ) .'/widgets/SanTwitterWidget.class.php';
