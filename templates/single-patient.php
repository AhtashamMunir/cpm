<?php get_header(); ?>


<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title"><?php the_title(); ?></h1>
                    </div>
                    <div class="card-body">
                        <?php
                        while (have_posts()) :
                            the_post();
                            $age = get_post_meta(get_the_ID(), '_patient_age', true);
                            $gender = get_post_meta(get_the_ID(), '_patient_gender', true);
                            $phone = get_post_meta(get_the_ID(), '_patient_phone', true);
                            $address = get_post_meta(get_the_ID(), '_patient_address', true);
                            ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <header class="entry-header">
                                    <h1 class="entry-title"><?php the_title(); ?></h1>
                                </header>
                                <div class="entry-content">
                                    <p><strong>Age:</strong> <?php echo esc_html($age); ?></p>
                                    <p><strong>Gender:</strong> <?php echo esc_html($gender); ?></p>
                                    <p><strong>Phone:</strong> <?php echo esc_html($phone); ?></p>
                                    <p><strong>Address:</strong> <?php echo esc_html($address); ?></p>
                                </div>

                                <div class="content">
                                    <?php the_content(); ?>
                                </div>

                                <h2><?php _e('Visits', 'text_domain'); ?></h2>
                                <?php
                                $args = array(
                                    'post_type' => 'visit',
                                    'meta_query' => array(
                                        array(
                                            'key' => '_patient_id',
                                            'value' => get_the_ID(),
                                            'compare' => '='
                                        )
                                    )
                                );
                                $visits = new WP_Query($args);
                                if ($visits->have_posts()) {
                                    echo '<ul>';
                                    while ($visits->have_posts()) {
                                        $visits->the_post();
                                        $visit_date = get_post_meta(get_the_ID(), '_visit_date', true);
                                        $visit_time = get_post_meta(get_the_ID(), '_visit_time', true);
                                        echo '<li>' . esc_html($visit_date) . ' ' . esc_html($visit_time) . ' - ' . get_the_title() . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p>No visits found.</p>';
                                }
                                wp_reset_postdata();
                                ?>
                            </article>
                        </div>
                    </div>
                <?php endwhile; ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>
