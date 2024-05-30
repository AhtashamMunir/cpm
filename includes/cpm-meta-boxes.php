<?php
function cpm_add_meta_boxes() {
    add_meta_box(
        'patient_details',
        __('Patient Details', 'clinic-patient-management'),
        'cpm_render_patient_details_box',
        'patient',
        'normal',
        'high'
    );

    add_meta_box(
        'visit_details',
        __('Visit Details', 'clinic-patient-management'),
        'cpm_render_visit_details_box',
        'visit',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cpm_add_meta_boxes');

function cpm_render_patient_details_box($post) {
    // Retrieve current details based on post ID
    $age = get_post_meta($post->ID, '_patient_age', true);
    $gender = get_post_meta($post->ID, '_patient_gender', true);
    $phone = get_post_meta($post->ID, '_patient_phone', true);
    $address = get_post_meta($post->ID, '_patient_address', true);
    ?>
    <label for="patient_age"><?php _e('Age', 'clinic-patient-management'); ?></label>
    <input type="number" name="patient_age" id="patient_age" value="<?php echo esc_attr($age); ?>">
    <br>
    <label for="patient_gender"><?php _e('Gender', 'clinic-patient-management'); ?></label>
    <select name="patient_gender" id="patient_gender">
        <option value="Male" <?php selected($gender, 'Male'); ?>>Male</option>
        <option value="Female" <?php selected($gender, 'Female'); ?>>Female</option>
    </select>
    <br>
    <label for="patient_phone"><?php _e('Phone', 'clinic-patient-management'); ?></label>
    <input type="text" name="patient_phone" id="patient_phone" value="<?php echo esc_attr($phone); ?>">
    <br>
    <label for="patient_address"><?php _e('Address', 'clinic-patient-management'); ?></label>
    <textarea name="patient_address" id="patient_address"><?php echo esc_textarea($address); ?></textarea>
    <?php
}

function cpm_render_visit_details_box($post) {
    // Retrieve current details based on post ID
    $visit_date = get_post_meta($post->ID, '_visit_date', true);
    $visit_time = get_post_meta($post->ID, '_visit_time', true);
    $patient_id = get_post_meta($post->ID, '_patient_id', true);
    ?>
    <label for="visit_date"><?php _e('Visit Date', 'clinic-patient-management'); ?></label>
    <input type="date" name="visit_date" id="visit_date" value="<?php echo esc_attr($visit_date); ?>">
    <br>
    <label for="visit_time"><?php _e('Visit Time', 'clinic-patient-management'); ?></label>
    <input type="time" name="visit_time" id="visit_time" value="<?php echo esc_attr($visit_time); ?>">
    <br>
    <label for="patient_id"><?php _e('Patient', 'clinic-patient-management'); ?></label>
    <select name="patient_id" id="patient_id">
        <?php
        $patients = get_posts(array('post_type' => 'patient', 'posts_per_page' => -1));
        foreach ($patients as $patient) {
            echo '<option value="' . esc_attr($patient->ID) . '" ' . selected($patient->ID, $patient_id, false) . '>' . esc_html($patient->post_title) . '</option>';
        }
        ?>
    </select>
    <?php
}

function cpm_save_patient_details($post_id) {
    
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    }
    // Sanitize and save the data
    if(isset($_POST['patient_age'])){
        $age = sanitize_text_field($_POST['patient_age']);
        update_post_meta($post_id, '_patient_age', $age);
    }
    if(isset($_POST['patient_gender'])){
        $gender = sanitize_text_field($_POST['patient_gender']);
        update_post_meta($post_id, '_patient_gender', $gender);
    }
    if(isset($_POST['patient_phone'])){
        $phone = sanitize_text_field($_POST['patient_phone']);
        update_post_meta($post_id, '_patient_phone', $phone);
    }
    if(isset($_POST['patient_address'])){
        $address = sanitize_textarea_field($_POST['patient_address']);
        update_post_meta($post_id, '_patient_address', $address);
    }

}
add_action('save_post_patient', 'cpm_save_patient_details');

function cpm_save_visit_details($post_id) {
    
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
    }
    // Sanitize and save the data if the keys exist
    if (isset($_POST['visit_date'])) {
        $visit_date = sanitize_text_field($_POST['visit_date']);
        update_post_meta($post_id, '_visit_date', $visit_date);
    }

    if (isset($_POST['visit_time'])) {
        $visit_time = sanitize_text_field($_POST['visit_time']);
        update_post_meta($post_id, '_visit_time', $visit_time);
    }

    if (isset($_POST['patient_id'])) {
        $patient_id = intval($_POST['patient_id']);
        update_post_meta($post_id, '_patient_id', $patient_id);
    }
}
add_action('save_post_visit', 'cpm_save_visit_details');
