<?php
/**
 * Plugin Name: WP Checklist Plugin
 * Description: A plugin to create a custom post type 'Checklist' and add/manage products in a checklist
 * Version: 1.0
 * Author: Cristian Ibanez - Sherpa.McKim
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Include other necessary files
require_once( plugin_dir_path( __FILE__ ) . 'checklist_post_type.php' );

function activate_checklist_plugin() {
  // Trigger our function that registers the custom post type plugin.
  create_checklist_post_type(); 

  // Clear the permalinks after the post type has been registered.
  flush_rewrite_rules(); 
}

register_activation_hook( __FILE__, 'activate_checklist_plugin' );

// Include metabox file
require_once( plugin_dir_path( __FILE__ ) . 'checklist_metabox.php' );

// Add action hooks
add_action( 'add_meta_boxes', 'add_checklist_metabox' );
add_action( 'save_post', 'save_checklist_metabox', 10, 2 );

// Enqueue scripts and styles for the admin panel
add_action( 'admin_enqueue_scripts', 'checklist_admin_scripts' );

function checklist_admin_scripts() {
  wp_enqueue_script( 'checklist-admin', plugins_url( 'admin/checklist_admin.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
  wp_enqueue_style( 'checklist-admin', plugins_url( 'admin/checklist_admin.css', __FILE__ ), array(), '1.0.0' );
  wp_enqueue_script('checklist-frontend-js', plugins_url('admin/user-check.js', __FILE__), array('jquery'), '1.0', true);
  wp_enqueue_script( 'checklist-admin', plugins_url( 'admin/jquery-ui-sortable', __FILE__ ), array( 'jquery' ), '1.0.0', true );
  wp_enqueue_script('my-sortable-script', plugins_url('admin/my-sortable.js', __FILE__), array('jquery-ui-sortable'), '1.0.0', true);
  wp_localize_script('my-sortable-script', 'MySortable', array('ajax_url' => admin_url('admin-ajax.php')));
}

include_once( plugin_dir_path( __FILE__ ) . '/templates/user-checklist' );

// Add shortcode
add_shortcode('display_checklist', 'display_checklist');

function display_checklist($atts) {
    // Include the display file
    include( plugin_dir_path( __FILE__ ) . 'frontend/checklist_display.php' );
}

// Enqueue frontend scripts and pass the AJAX URL
add_action( 'wp_enqueue_scripts', 'checklist_frontend_scripts' );

function checklist_frontend_scripts() {
  wp_enqueue_script( 'checklist-frontend', plugins_url( 'frontend/checklist.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
  wp_localize_script( 'checklist-frontend', 'checklist_vars', array(
      'ajax_url' => admin_url( 'admin-ajax.php' ),
      'user_is_logged_in' => is_user_logged_in(),
  ));
  wp_enqueue_style( 'checklist-admin', plugins_url( 'frontend/styles.css?v=1.0', __FILE__ ), array(), '1.0.0' );
}

// Handle AJAX requests
add_action( 'wp_ajax_update_checklist', 'update_checklist' );
add_action( 'wp_ajax_nopriv_update_checklist', 'update_checklist' );

function update_checklist() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_die('You must be logged in to use the checklists.');
    }

    $postId = $_POST['post_id'];
    $productName = $_POST['product_name'];
    $stateName = $_POST['state_name'];

    // Clear all product states for the current user and product
    delete_user_meta(get_current_user_id(), 'checklist_' . $postId . '_' . $productName . '_have');
    delete_user_meta(get_current_user_id(), 'checklist_' . $postId . '_' . $productName . '_need');
    delete_user_meta(get_current_user_id(), 'checklist_' . $postId . '_' . $productName . '_ordered');
	delete_user_meta(get_current_user_id(), 'checklist_' . $postId . '_' . $productName . '_interest');
	delete_user_meta(get_current_user_id(), 'checklist_' . $postId . '_' . $productName . '_quantity');

    // Update the user meta with the current state
    update_user_meta(get_current_user_id(), 'checklist_' . $postId . '_' . $productName . '_' . $stateName, true);

    wp_die();
}

// In your main plugin file (wp-checklist-plugin.php)

function load_custom_template_for_checklist($template) {
     global $post;

     if (is_singular('checklist') && $post->post_type == 'checklist') {
          $template = plugin_dir_path(__FILE__) . 'templates/single-checklist.php';
     } elseif (is_post_type_archive('checklist')) {
          $template = plugin_dir_path(__FILE__) . 'templates/archive-checklist.php';
     }
     return $template;
}
add_filter('template_include', 'load_custom_template_for_checklist');

function save_sort_order() {
    check_ajax_referer('wp_rest', 'security');
    global $wpdb;

    $order = $_POST['order'];
    $counter = 0;

    foreach ($order as $item_id) {
        $wpdb->update($wpdb->posts, array('menu_order' => $counter), array('ID' => $item_id));
        $counter++;
    }

    wp_send_json_success();
}
add_action('wp_ajax_save_sort', 'save_sort_order');

// Settings Archive

function checklist_add_settings_page() {
    add_options_page(
        'Checklist Settings',
        'Checklist Settings',
        'manage_options',
        'checklist-settings',
        'checklist_render_settings_page'
    );
}
add_action('admin_menu', 'checklist_add_settings_page');

function checklist_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    settings_errors('checklist_messages');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('checklist');
            do_settings_sections('checklist');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

function checklist_settings_init() {
    register_setting('checklist', 'checklist_options');

    add_settings_section(
        'checklist_section',
        'Checklist Archive Settings',
        'checklist_section_cb',
        'checklist'
    );

    add_settings_field(
        'checklist_field_desc',
        'Archive Description',
        'checklist_field_desc_cb',
        'checklist',
        'checklist_section',
        array(
            'label_for' => 'checklist_field_desc',
            'class' => 'checklist_row',
            'checklist_custom_data' => 'custom',
        )
    );

    add_settings_field(
        'checklist_field_title',
        'Archive Title',
        'checklist_field_title_cb',
        'checklist',
        'checklist_section',
        array(
            'label_for' => 'checklist_field_title',
            'class' => 'checklist_row',
            'checklist_custom_data' => 'custom',
        )
    );
}
add_action('admin_init', 'checklist_settings_init');

function checklist_section_cb($args) {
    echo '<p id="' . esc_attr($args['id']) . '">' . esc_html('Enter settings for the checklist archive page') . '</p>';
}

function checklist_field_desc_cb($args) {
    $options = get_option('checklist_options');
    ?>
    <textarea id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['checklist_custom_data']); ?>" name="checklist_options[<?php echo esc_attr($args['label_for']); ?>]" rows="10" cols="50"><?php echo esc_html($options[$args['label_for']]); ?></textarea>
    <?php
}

function checklist_field_title_cb($args) {
    $options = get_option('checklist_options');
    ?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" data-custom="<?php echo esc_attr($args['checklist_custom_data']); ?>" name="checklist_options[<?php echo esc_attr($args['label_for']); ?>]" type="text" value="<?php echo esc_html($options[$args['label_for']]); ?>">
    <?php
}

//CSV Function

add_action( 'wp_ajax_export_to_csv', 'export_to_csv' );

function export_to_csv() {
    // Check if a post ID is provided
    if ( ! isset( $_POST['post_id'] ) ) {
        echo 'Error: No post ID provided.';
        wp_die();
    }

    // Get the post ID
    $post_id = intval( $_POST['post_id'] );

    // Get the post
    $post = get_post( $post_id );

    // Get the product list
    $product_list = get_post_meta( $post_id, 'product_list', true );

    // Prepare data for CSV
    $csv_data = "Number,Name,Description,Quantity,Have,Need,Ordered,Not Interested\n";

    foreach ( $product_list as $product ) {
        $number = $product['number'];
        $name = $product['name'];
        $description = $product['description'];
		$quantity = $product['quantity'];

        $haveState = get_user_meta( get_current_user_id(), 'checklist_' . $post_id . '_' . $name . '_have', true ) ? 'Yes' : '-';
        $needState = get_user_meta( get_current_user_id(), 'checklist_' . $post_id . '_' . $name . '_need', true ) ? 'Yes' : '-';
        $orderedState = get_user_meta( get_current_user_id(), 'checklist_' . $post_id . '_' . $name . '_ordered', true ) ? 'Yes' : '-';
        $interestState = get_user_meta( get_current_user_id(), 'checklist_' . $post_id . '_' . $name . '_interest', true ) ? 'Yes' : '-';

        $csv_data .= "{$number},{$name},{$description},{$quantity},{$haveState},{$needState},{$orderedState},{$interestState}\n";
    }

    // Send back the data
    echo $csv_data;

    // Terminate the script
    wp_die();
}

// Share

function generate_shareable_link($user_id, $post_id) {
  // Create a unique key based on the user ID, post ID, and the current time
  $unique_key = md5($user_id . $post_id . time());
  
  // Store the unique key along with the user ID and post ID in your database or user meta
  update_user_meta($user_id, 'shareable_link_key_' . $post_id, $unique_key);

  // Generate the URL for the shared checklist, including the unique key
  $url = home_url() . "/shared-checklist?k=" . $unique_key . "&checklist_id=" . $post_id;

  return $url;
}

function shared_checklist_endpoint() {
  if (isset($_GET['k'])) {
    $key = sanitize_key($_GET['k']);

    // Find the user and post ID associated with this key
    // Code to retrieve the user ID and post ID goes here

    // Include the display file
    include(plugin_dir_path(__FILE__) . 'frontend/shared_checklist_display.php');
    exit;
  }
}
add_action('template_redirect', 'shared_checklist_endpoint');

add_action('wp_ajax_generate_shareable_link', 'ajax_generate_shareable_link');

function ajax_generate_shareable_link() {
  // Validate request, check if user is logged in
  if (!is_user_logged_in()) {
    wp_die('You must be logged in to share the checklist.');
  }

  // Get the post ID from the AJAX request
  $post_id = intval($_POST['post_id']);

  // Generate the shareable link
  $user_id = get_current_user_id();
  $link = generate_shareable_link($user_id, $post_id);

  // Return the link
  echo $link;

  wp_die();
}

//User Checklists

function render_front_end_checklist_form($atts) {
	
	// Enqueue necessary scripts and styles
    wp_enqueue_media(); // For the media uploader
    // Check if we are editing an existing checklist
    $edit_mode = false;
    $checklist_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $checklist_title = '';
	$is_shared = 'no'; // Default to no
    $product_list = [];

    if ($checklist_id > 0) {
        $checklist = get_post($checklist_id);
        $current_user_id = get_current_user_id();
        $post_author_id = $checklist->post_author;

        // Verify that the current user is the author or an admin
        if ($current_user_id == $post_author_id || current_user_can('manage_options')) {
            $edit_mode = true;
            $checklist_title = $checklist->post_title;
            $product_list = get_post_meta($checklist_id, 'product_list', true);
			$is_shared = get_post_meta($checklist_id, '_is_shared', true) === 'yes' ? 'yes' : 'no'; // Use checklist_id
        } else {
            echo "You do not have permission to edit this checklist.";
            return; // Stop rendering the form if not permitted
        }
    }


    // Start the form
    ?>
    <form id="front-end-checklist-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
        <?php wp_nonce_field('front_end_checklist_nonce_action', 'front_end_checklist_nonce'); ?>
        <input type="hidden" name="checklist_id" value="<?php echo esc_attr($checklist_id); ?>">
        <div class="_checklist_title">
            <label for="checklist_title">Checklist Title:</label>
            <input type="text" id="checklist_title" name="checklist_title" placeholder="Enter title here" value="<?php echo esc_attr($checklist_title); ?>" required>
			        <label for="share_checklist">Share?</label>
        <input type="checkbox" id="share_checklist" name="share_checklist" value="1" <?php echo ($is_shared == 'yes') ? 'checked' : ''; ?>> Yes
        </div>
        <div id="dynamic-checklist-container">
            <?php if (!empty($product_list)) : foreach ($product_list as $index => $product) : ?>
			<div class="checklist-item">
            <div class="_preview"><div><label>Number:</label>
            <input type="number" name="product_number[]" value="<?php echo esc_attr($product['number']); ?>" placeholder="Number" required></div>
            <div><label>Name:</label>
            <input type="text" name="product_name[]" value="<?php echo esc_attr($product['name']); ?>" placeholder="Product Name" required></div></div>
            <div><label>Short Description:</label>
            <textarea type="text" name="product_description[]" value="<?php echo esc_attr($product['description']); ?>" placeholder="Description"></textarea></div>
            <div><label>Image:</label>
            <input type="button" class="upload_image_button" value="Upload Image">
            <input type="hidden" name="product_image[]"  value="<?php echo esc_attr($product['image']); ?>" class="product_image">
            <div class="image_preview"><img src="<?php echo esc_url($product['image']); ?>" alt="" style="max-width: 100px;"></div></div>
            <button type="button" class="remove-item button">Remove Item</button>
       		</div>
            <?php endforeach; else: ?>
            <!-- Include a default set of empty fields for new checklist creation -->
            <?php endif; ?>
        </div>
        <button type="button" id="add-checklist-item" class="button">Add Item</button>
        <input type="submit" value="<?php echo $edit_mode ? 'Update Checklist' : 'Submit Checklist'; ?>">
		<?php if ($edit_mode): ?>
            <a href="<?php echo get_permalink($checklist_id); ?>" class="_button">View without Saving</a>
        <?php endif; ?>
    </form>
<?php
}
add_shortcode('front_end_checklist_form', 'render_front_end_checklist_form');

function handle_front_end_form_submission() {
    // First, check if the nonce is set and valid to protect against CSRF attacks.
    if (isset($_POST['front_end_checklist_nonce']) && wp_verify_nonce($_POST['front_end_checklist_nonce'], 'front_end_checklist_nonce_action')) {
        // Retrieve the post ID from the form. It will be 0 if this is a new post creation.
        $post_id = isset($_POST['checklist_id']) && !empty($_POST['checklist_id']) ? intval($_POST['checklist_id']) : 0;

        // Determine the shared status from the form.
        $is_shared = isset($_POST['share_checklist']) ? 'yes' : 'no';

        // Security check: Ensure that the user has the capability to edit this post or manage options.
        if ($post_id != 0 && !current_user_can('edit_checklist', $post_id) && !current_user_can('manage_options')) {
            wp_die('Unauthorized attempt to edit post.');
        }

        // Set up the data for creating or updating the post.
        $post_data = array(
            'ID'           => $post_id,  // if $post_id is 0, wp_insert_post will create a new post.
            'post_title'   => sanitize_text_field($_POST['checklist_title']),
            'post_type'    => 'checklist',
            'post_status'  => 'publish',
        );

        // Insert or update the post and get the new post ID.
        $post_id = wp_insert_post($post_data);

        // Check if the post creation or update was successful.
        if (!is_wp_error($post_id)) {
            // Update the '_is_shared' metadata.
            update_post_meta($post_id, '_is_shared', $is_shared);

            // Prepare the list of products to save.
            $product_list = [];
            if (isset($_POST['product_name'])) {
                for ($i = 0; $i < count($_POST['product_name']); $i++) {
                    $product_list[] = array(
                        'number' => sanitize_text_field($_POST['product_number'][$i]),
                        'name' => sanitize_text_field($_POST['product_name'][$i]),
                        'description' => sanitize_text_field($_POST['product_description'][$i]),
                        'image' => sanitize_text_field($_POST['product_image'][$i])
                    );
                }
            }

            // Update the 'product_list' metadata.
            update_post_meta($post_id, 'product_list', $product_list);

            // Redirect to the permalink of the newly created or updated post.
            $redirect_url = get_permalink($post_id);
            wp_redirect($redirect_url);
            exit;  // Always call exit after wp_redirect.
        } else {
            // Handle errors in post creation or updating.
            wp_die('Post creation failed: ' . $post_id->get_error_message());
        }
    }
}

// Hook the above function into 'wp' action to handle form submissions.
add_action('wp', 'handle_front_end_form_submission');

function restrict_media_library_to_user_uploads($query) {
    if (!current_user_can('manage_options')) { // Check if the user is not an admin
        $user_id = get_current_user_id(); // Get the current user ID
        $query['author'] = $user_id; // Restrict the query to files uploaded by the user
    }
    return $query;
}
add_filter('ajax_query_attachments_args', 'restrict_media_library_to_user_uploads');

function handle_delete_my_checklist() {
    // Check for a valid nonce and if the request is not valid, stop the process.
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'delete_checklist_nonce')) {
        wp_die('Security check failed.');
    }

    // Ensure the checklist ID is present.
    if (!isset($_POST['checklist_id'])) {
        wp_die('Missing checklist ID.');
    }

    $post_id = intval($_POST['checklist_id']);

    // Security: Check if the current user has delete capability on this checklist.
    if (!current_user_can('delete_checklist', $post_id)) {
        wp_die('You do not have permission to delete this checklist.');
    }

    // Proceed with deletion if the user has the necessary capability.
    $deleted = wp_delete_post($post_id, true);  // Set to true to bypass trash and permanently delete.
    if ($deleted) {
        wp_redirect(home_url('/user-profile')); // Adjust the redirection URL as necessary.
        exit;
    } else {
        wp_die('Error occurred deleting the checklist.');
    }
}
add_action('admin_post_delete_my_checklist', 'handle_delete_my_checklist'); // Hook for authenticated users.