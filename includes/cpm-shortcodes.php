<?php
// Display Patients Shortcode
function cpm_display_patients_shortcode($atts) {
    $args = array(
        'post_type' => 'patient',
        'posts_per_page' => -1,
    );
    $patients = new WP_Query($args);
    ob_start();
    if ($patients->have_posts()) {
        echo '<ul class="list-group">';
        while ($patients->have_posts()) {
            $patients->the_post();
            echo '<li class="list-group-item"><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="alert alert-warning">No patients found.</div>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('cpm_display_patients', 'cpm_display_patients_shortcode');

// Filter Patients Shortcode
function cpm_filter_patients_shortcode($atts) {
    ob_start();
    ?>
    <form method="GET" action="" class="form-inline">
        <div class="form-group mb-2">
            <label for="age" class="sr-only"><?php _e('Age', 'clinic-patient-management'); ?></label>
            <input type="number" name="age" id="age" class="form-control" placeholder="<?php _e('Age', 'clinic-patient-management'); ?>">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <label for="gender" class="sr-only"><?php _e('Gender', 'clinic-patient-management'); ?></label>
            <select name="gender" id="gender" class="form-control">
                <option value=""><?php _e('Any', 'clinic-patient-management'); ?></option>
                <option value="Male"><?php _e('Male', 'clinic-patient-management'); ?></option>
                <option value="Female"><?php _e('Female', 'clinic-patient-management'); ?></option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2"><?php _e('Filter', 'clinic-patient-management'); ?></button>
    </form>
    <?php
    if (isset($_GET['age']) || isset($_GET['gender'])) {
        $meta_query = array('relation' => 'AND');
        if (!empty($_GET['age'])) {
            $meta_query[] = array(
                'key' => '_patient_age',
                'value' => intval($_GET['age']),
                'compare' => '='
            );
        }
        if (!empty($_GET['gender'])) {
            $meta_query[] = array(
                'key' => '_patient_gender',
                'value' => sanitize_text_field($_GET['gender']),
                'compare' => '='
            );
        }
        $args = array(
            'post_type' => 'patient',
            'meta_query' => $meta_query,
        );
        $patients = new WP_Query($args);
        if ($patients->have_posts()) {
            echo '<ul class="list-group mt-4">';
            while ($patients->have_posts()) {
                $patients->the_post();
                echo '<li class="list-group-item"><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<div class="alert alert-warning mt-4">No patients found.</div>';
        }
        wp_reset_postdata();
    }
    return ob_get_clean();
}
add_shortcode('cpm_filter_patients', 'cpm_filter_patients_shortcode');

// Display Visits Shortcode
function cpm_display_visits_shortcode($atts) {
    $atts = shortcode_atts(array('patient_id' => 0), $atts, 'cpm_display_visits');
    $args = array(
        'post_type' => 'visit',
        'meta_query' => array(
            array(
                'key' => '_patient_id',
                'value' => $atts['patient_id'],
                'compare' => '='
            )
        )
    );
    $visits = new WP_Query($args);
    ob_start();
    if ($visits->have_posts()) {
        echo '<ul class="list-group">';
        while ($visits->have_posts()) {
            $visits->the_post();
            $visit_date = get_post_meta(get_the_ID(), '_visit_date', true);
            $visit_time = get_post_meta(get_the_ID(), '_visit_time', true);
            echo '<li class="list-group-item">' . esc_html($visit_date) . ' ' . esc_html($visit_time) . ' - ' . get_the_title() . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="alert alert-warning">No visits found.</div>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('cpm_display_visits', 'cpm_display_visits_shortcode');

// Display All Visits Shortcode
function cpm_display_all_visits_shortcode($atts) {
    $args = array(
        'post_type' => 'visit',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_patient_id',
                'compare' => 'EXISTS'
            )
        )
    );
    $visits = new WP_Query($args);
    ob_start();
    if ($visits->have_posts()) {
        echo '<ul class="list-group">';
        while ($visits->have_posts()) {
            $visits->the_post();
            $visit_date = get_post_meta(get_the_ID(), '_visit_date', true);
            $visit_time = get_post_meta(get_the_ID(), '_visit_time', true);
            $patient_id = get_post_meta(get_the_ID(), '_patient_id', true);
            $patient_name = get_the_title($patient_id);

            echo '<li class="list-group-item">';
            echo '<strong>' . esc_html($patient_name) . ':</strong> ';
            echo esc_html($visit_date) . ' ' . esc_html($visit_time) . ' - ' . get_the_title();
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="alert alert-warning">No visits found.</div>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('cpm_display_all_visits', 'cpm_display_all_visits_shortcode');
