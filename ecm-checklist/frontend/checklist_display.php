<?php

// Fetch all checklist posts



$args = array(

    'post_type' => 'checklist',

    'post_status' => 'publish',

    'nopaging' => true,

);

$query = new WP_Query($args);



while ($query->have_posts()) {

    $query->the_post();

    

    $product_list = get_post_meta( get_the_ID(), 'product_list', true );

    

    if ( $product_list && count( $product_list ) > 0 ) {

        echo '<h2>' . get_the_title() . '</h2>';



        foreach ( $product_list as $product ) {

            // Render each product. This is just a basic example.

            echo '<div class="product-row">';

            echo '<h3>' . esc_html( $product['name'] ) . '</h3>';

            echo '<p>' . esc_html( $product['description'] ) . '</p>';

            echo '</div>';

        }

    }

}

wp_reset_postdata();



foreach ( $product_list as $product ) {

    $isChecked = get_user_meta( get_current_user_id(), 'checklist_' . get_the_ID() . '_' . $product['name'], true );



    echo '<div class="product-row">';

    echo '<h3>' . esc_html( $product['name'] ) . '</h3>';

    echo '<p>' . esc_html( $product['description'] ) . '</p>';

    echo '<input type="checkbox" class="product-checkbox" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr( $product['name'] ) . '"' . ($isChecked ? ' checked' : '') . '>';

    echo '</div>';

}