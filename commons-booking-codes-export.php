<?php

/*
Plugin Name:  Commons Booking Codes Export
Plugin URI:   https://github.com/flotte-berlin/commons-booking-codes-export
Description:  Ein Plugin zum einfachen Export von Commons Booking Buchungs-Codes als CSV
Version:      0.1.0
Author:       poilu
Author URI:   https://github.com/poilu
License:      GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

// add action to commons booking codes page in admin section

define( 'CB_CODES_EXPORT_PATH', plugin_dir_path( __FILE__ ) );

require_once( CB_CODES_EXPORT_PATH . 'classes/class-cb-codes-export.php' );

$cb_codes_export = new CB_Codes_Export();
add_action( 'plugins_loaded', array($cb_codes_export, 'load_codes_export'));

?>
