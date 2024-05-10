<?php
/**
 * The template for displaying all single user checklists
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package newco_theme
 */

get_header(); ?>

<main id="main-content">
    <?php
    while (have_posts()) : the_post();
        // Display the title
        the_title('<h1>', '</h1>');

        // Assuming you store checklist items in post meta or a custom table
        $checklist_items = get_checklist_items(get_the_ID());

        if ($checklist_items) {
            echo '<ul>';
            foreach ($checklist_items as $item) {
                // Output your checklist item here
                echo '<li>' . esc_html($item->item_name) . '</li>';
            }
            echo '</ul>';
        }
    endwhile;
    ?>
</main>

<?php get_footer(); ?>
