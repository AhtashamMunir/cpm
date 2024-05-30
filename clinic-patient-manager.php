<?php
/*
Plugin Name: Clinic Patient Manager
Description: A plugin to manage patients in a clinic.
Version: 1.0
Author: Ahtasham Munir
License: GPLv2 or later
Text Domain: clinic-patient-management
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/cpm-config.php';
include_once plugin_dir_path(__FILE__) . 'includes/cpm-post-types.php';
include_once plugin_dir_path(__FILE__) . 'includes/cpm-meta-boxes.php';
include_once plugin_dir_path(__FILE__) . 'includes/cpm-shortcodes.php';
