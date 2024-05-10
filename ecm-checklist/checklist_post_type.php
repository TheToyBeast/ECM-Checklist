<?php

function register_my_custom_post_type() {
    $args = array(
        'labels' => array('name' => 'Checklists', 'singular_name' => 'Checklist'),
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'custom-fields', 'thumbnail', 'excerpt', 'author' ),
        'capability_type' => 'checklist',
        'map_meta_cap' => true,  // Important to handle capability mapping properly
        'capabilities' => array(
            'edit_post' => 'edit_checklist',
            'read_post' => 'read_checklist',
            'delete_post' => 'delete_checklist',
            'edit_posts' => 'edit_checklists',
            'edit_others_posts' => 'edit_others_checklists',
            'delete_posts' => 'delete_checklists',
            'delete_others_posts' => 'delete_others_checklists',
            'publish_posts' => 'publish_checklists',
            'read_private_posts' => 'read_private_checklists',
			'show_in_menu' => true,
			'show_in_menu' => 'edit_checklists',
        ),
    );
    register_post_type('checklist', $args);
}
add_action('init', 'register_my_custom_post_type');