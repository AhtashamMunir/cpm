<?php
get_header();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4">Patients</h1>

            <!-- Patient Filter Form -->
            <form method="GET" action="" class="form-inline mb-4">
                <div class="form-group mb-2">
                    <label for="age" class="sr-only"><?php _e('Age', 'clinic-patient-management'); ?></label>
                    <input type="number" name="age" id="age" class="form-control" placeholder="<?php _e('Age', 'clinic-patient-management'); ?>" value="<?php echo isset($_GET['age']) ? esc_attr($_GET['age']) : ''; ?>">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="gender" class="sr-only"><?php _e('Gender', 'clinic-patient-management'); ?></label>
                    <select name="gender" id="gender" class="form-control">
                        <option value=""><?php _e('Any', 'clinic-patient-management'); ?></option>
                        <option value="Male" <?php selected('Male', isset($_GET['gender']) ? $_GET['gender'] : ''); ?>><?php _e('Male', 'clinic-patient-management'); ?></option>
                        <option value="Female" <?php selected('Female', isset($_GET['gender']) ? $_GET['gender'] : ''); ?>><?php _e('Female', 'clinic-patient-management'); ?></option>
                    </select>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="visit_date" class="sr-only"><?php _e('Visit Date', 'clinic-patient-management'); ?></label>
                    <input type="date" name="visit_date" id="visit_date" class="form-control" placeholder="<?php _e('Visit Date', 'clinic-patient-management'); ?>" value="<?php echo isset($_GET['visit_date']) ? esc_attr($_GET['visit_date']) : ''; ?>">
                </div>
                <button type="submit" class="btn btn-primary mb-2"><?php _e('Filter', 'clinic-patient-management'); ?></button>
            </form>

            <?php
            // Meta query based on filter inputs
            $meta_query = array('relation' => 'AND');

            if (isset($_GET['age']) && $_GET['age'] !== '') {
                $meta_query[] = array(
                    'key' => '_patient_age',
                    'value' => intval($_GET['age']),
                    'compare' => '='
                );
            }

            if (isset($_GET['gender']) && $_GET['gender'] !== '') {
                $meta_query[] = array(
                    'key' => '_patient_gender',
                    'value' => sanitize_text_field($_GET['gender']),
                    'compare' => '='
                );
            }

            // Prepare a list of patient IDs who have visits on the given date
            if (isset($_GET['visit_date']) && $_GET['visit_date'] !== '') {
                $visit_date = sanitize_text_field($_GET['visit_date']);
                $visit_args = array(
                    'post_type' => 'visit',
                    'meta_query' => array(
                        array(
                            'key' => '_visit_date',
                            'value' => $visit_date,
                            'compare' => '='
                        )
                    ),
                    'fields' => 'ids'
                );
                $visit_query = new WP_Query($visit_args);
                $patient_ids = array();
                if ($visit_query->have_posts()) {
                    while ($visit_query->have_posts()) {
                        $visit_query->the_post();
                        $patient_id = get_post_meta(get_the_ID(), '_patient_id', true);
                        if ($patient_id) {
                            $patient_ids[] = $patient_id;
                        }
                    }
                    wp_reset_postdata();
                }
                if (!empty($patient_ids)) {
                    $meta_query[] = array(
                        'key' => '_patient_id',
                        'value' => $patient_ids,
                        'compare' => 'IN'
                    );
                }
            }

            // Modify the query if filters are applied
            $args = array(
                'post_type' => 'patient',
                'meta_query' => $meta_query
            );
            $patients_query = new WP_Query($args);

            if ($patients_query->have_posts()) :
                while ($patients_query->have_posts()) : $patients_query->the_post();
            ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h2 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        </div>
                        <div class="card-body">
                            <p><strong>Age:</strong> <?php echo get_post_meta(get_the_ID(), '_patient_age', true); ?></p>
                            <p><strong>Gender:</strong> <?php echo get_post_meta(get_the_ID(), '_patient_gender', true); ?></p>

                            <!-- Display Visits -->
                            <h5 class="mt-3">Visits</h5>
                            <?php
                            $visit_args = array(
                                'post_type' => 'visit',
                                'meta_query' => array(
                                    array(
                                        'key' => '_patient_id',
                                        'value' => get_the_ID(),
                                        'compare' => '='
                                    )
                                )
                            );
                            $visits_query = new WP_Query($visit_args);
                            if ($visits_query->have_posts()) :
                                echo '<ul class="list-group">';
                                while ($visits_query->have_posts()) : $visits_query->the_post();
                                    $visit_date = get_post_meta(get_the_ID(), '_visit_date', true);
                                    $visit_time = get_post_meta(get_the_ID(), '_visit_time', true);
                                    echo '<li class="list-group-item">';
                                    echo '<strong>' . esc_html($visit_date) . ' ' . esc_html($visit_time) . '</strong> - ' . get_the_title();
                                    echo '</li>';
                                endwhile;
                                echo '</ul>';
                            else :
                                echo '<div class="alert alert-warning">No visits found</div>';
                            endif;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
            <?php
                endwhile;
            else :
                echo '<div class="alert alert-warning">No patients found</div>';
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
?>
