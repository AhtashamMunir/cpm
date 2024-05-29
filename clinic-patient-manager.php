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
// Enqueue Bootstrap CSS
function enqueue_bootstrap() {
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_bootstrap');

// Filter the single template for the patient CPT
function load_patient_single_template($single_template) {
    global $post;

    if ($post->post_type == 'patient') {
        $single_template = plugin_dir_path(__FILE__) . 'templates/single-patient.php';
    }
    return $single_template;
}
add_filter('single_template', 'load_patient_single_template');

// Filter the archive template for the patient CPT
function load_patient_archive_template($archive_template) {
    if (is_post_type_archive('patient')) {
        $archive_template = plugin_dir_path(__FILE__) . 'templates/archive-patient.php';
    }
    return $archive_template;
}
add_filter('archive_template', 'load_patient_archive_template');

include_once plugin_dir_path(__FILE__) . 'includes/cpm-post-types.php';
include_once plugin_dir_path(__FILE__) . 'includes/cpm-meta-boxes.php';
include_once plugin_dir_path(__FILE__) . 'includes/cpm-shortcodes.php';
