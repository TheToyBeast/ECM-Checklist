<?php
function add_checklist_metabox() {
    add_meta_box(
        'checklist_meta', // Unique ID
        'Checklist Items', // Box title
        'checklist_meta_html', // Content callback, must be of type callable
        'checklist' // Post type
    );
}
add_action('add_meta_boxes', 'add_checklist_metabox');

function checklist_meta_html($post) {
    wp_nonce_field( basename( __FILE__ ), 'checklist_meta_nonce' );
    $product_list = get_post_meta( $post->ID, 'product_list', true );
    ?>
    <div id="product-list-wrapper">
        <?php
        if ( $product_list && count( $product_list ) > 0 ) {
            foreach ( $product_list as $product ) {
				
				// Get the 100x100 image URL
					$image_url = $product['image']; // Assuming this is the URL of the full size image
					$image_id = attachment_url_to_postid($image_url); // Get the image ID from the URL
					$image_info = wp_get_attachment_image_src($image_id, 'thumbnail'); // Use 'thumbnail' for now
					$thumbnail_url = $image_info[0];
                ?>
                <div class="product-row">
					<div class="row1">
					<label>Number:</label><br>
                    <input type="number" name="product_number[]" value="<?php echo esc_attr( $product['number'] ); ?>"><br>
					<label>Name:</label><br>
                    <input type="text" name="product_name[]" value="<?php echo esc_attr( $product['name'] ); ?>"><br>
                    <label>Product Link:</label>
                    <input type="text" name="product_link[]" value="<?php echo esc_attr( $product['link'] ); ?>"><br>
					</div>
					<div class="row3">
					<label>Short Description:</label><br>
                    <input type="text" name="product_description[]" value="<?php echo esc_attr( $product['description'] ); ?>"><br>
					<label for="product_image_<?php echo $i; ?>">Image:</label><br>
                    <input id="upload_image_button_<?php echo $i; ?>" type="button" class="button upload_image_button" value="" />
                    <input id="product_image_<?php echo $i; ?>" type="hidden" name="product_image[]" value="<?php echo esc_attr( $product['image'] ); ?>" /><br>
					<img id="product_image_display_<?php echo $i; ?>" class="product_image_display" src="<?php echo esc_attr( $thumbnail_url ); ?>" style="max-width: 100px; max-height: 100px;" />
					</div>
					 <button class="remove-row button">Remove Item</button>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <button id="add-product-row" class="button">Add row</button>
    <?php
}

function save_checklist_metabox($post_id, $post) {
    // Verify the nonce before proceeding.
    if ( !isset( $_POST['checklist_meta_nonce'] ) || !wp_verify_nonce( $_POST['checklist_meta_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    // Get the post type object.
    $post_type = get_post_type_object( $post->post_type );

    // Check if the current user has permission to edit the post.
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    $product_list = array();

    if ( isset( $_POST['product_number'] ) ) {
        for ( $i = 0; $i < count( $_POST['product_number'] ); $i++ ) {
            $product_list[] = array(
                'number' => $_POST['product_number'][$i],
                'name' => $_POST['product_name'][$i],
                'image' => $_POST['product_image'][$i],
                'link' => $_POST['product_link'][$i],
                'description' => $_POST['product_description'][$i],
            );
        }
    }

    update_post_meta( $post_id, 'product_list', $product_list );
}
add_action('save_post', 'save_checklist_metabox', 10, 2);
?>
