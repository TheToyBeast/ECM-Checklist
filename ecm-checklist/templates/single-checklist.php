<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package newco_theme
 */

get_header();
?>
<div class="g-columns__group">
	<div class="g-columns__item--nine">

	<main id="primary" class="g-layout__content _check">
		
		<?php

		// Fetch the current post ID
		$current_post_id = get_the_ID();
		$current_user_id = get_current_user_id(); // Gets the current logged-in user ID

		// Fetch only the current post
		$args = array(
			'post_type' => 'checklist',
			'p' => $current_post_id, // This will fetch only the current post
		);
		$query = new WP_Query($args);

		the_post_thumbnail( 'large' );

		$counter = 1; // Counter variable to keep track of the number of posts

		while ($query->have_posts()) {
			$query->the_post();

			$product_list = get_post_meta( get_the_ID(), 'product_list', true );
			$post_id = get_the_ID(); // Get the current post ID
        	$post_author_id = get_post_field('post_author', $post_id);
			
			if ($current_user_id == $post_author_id || current_user_can('manage_options')): ?>
            <a href="<?php echo esc_url(add_query_arg('edit', $post_id, get_permalink(get_page_by_path('user-checklist')))); ?>" class="edit-checklist-button">Edit Checklist</a>
        	<?php endif;

			if ( $product_list && count( $product_list ) > 0 ) {
				echo '<h1>' . get_the_title() . '</h1>';
				echo '<p class="_no-print">I will do my best to have these lists as up to date as possible. I may omit some variants as they may be too scarce for your average collector. Still, if you see something missing, mislabeled, or just have some thoughts on making checklists better, feel free to contact me at: <a href="mailto:toybeast@toybeast.ca">toybeast@toybeast.ca</a></p><p class="_no-print">I am also looking for volunteers to help with the creation and maintenance of new checklists. Give me a shout if interested. Lastly, if you find these checklists useful, please share. I really appreciate it and if you haven\'t registered yet, what are you waiting for? <b>Be included in our next figure giveaway!</b></p>';
				if ( ! is_user_logged_in() ) {
			echo '<p style="text-align:center;">Please '; 
			 wp_loginout( $_SERVER['REQUEST_URI'] );  
			echo ' or <a href="'.site_url('wp-login.php?action=register').'" class="simplemodal-register" rel="nofollow">Register</a> for full functionality including saving your checklist!</p>';
			?>
		<div id="filter">
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="all" disabled>&nbsp;All</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="have" disabled>&nbsp;Have</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="need" disabled>&nbsp;Need</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="ordered" disabled>&nbsp;Ordered</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="unchecked" disabled>&nbsp;Unchecked/New</label>
		</div>

		<?php

		} else { ?>
		<div id="filter">
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="all" checked>&nbsp;All</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="have">&nbsp;Have</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="need">&nbsp;Need</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="ordered">&nbsp;Ordered</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="unchecked">&nbsp;Unchecked/New</label>
		</div>

		<?php }
				if ( ! is_user_logged_in() ) {
				echo '<button id="export-to-csv" disabled>Export to CSV</button> <button onclick="window.print();">Print to PDF</button><br><span  class="_no-print" style="font-size:12px;">Print to pdf function may take a few seconds to load, please be patient</span>';
				} else {
					$url = add_query_arg(['download_checklist_csv' => $post->ID], get_permalink($post->ID));
					?>
				<div class="checkbut">
					<button id="export-to-csv">Export to CSV</button>
					<button onclick="window.print();">Print to PDF</button>
					<button id="share-checklist-button" data-post-id="<?php echo get_the_ID(); ?>">Share List</button>
					
				</div>
				<span  class="_no-print" style="font-size:12px;">Print to pdf function may take a few seconds to load, please be patient | Checklist saved automatically</span>
		<?php }


				foreach ( $product_list as $product ) {
					if( $product['number'] == 9999 ){
						echo '<hr><h2>'.esc_attr($product['name']).'</h2>';
					} else {
					// Fetch the states of each checkbox for this product
					$haveState = get_user_meta( get_current_user_id(), 'checklist_' . get_the_ID() . '_' . $product['name'] . '_have', true );
					$needState = get_user_meta( get_current_user_id(), 'checklist_' . get_the_ID() . '_' . $product['name'] . '_need', true );
					$orderedState = get_user_meta( get_current_user_id(), 'checklist_' . get_the_ID() . '_' . $product['name'] . '_ordered', true );
					$interestState = get_user_meta( get_current_user_id(), 'checklist_' . get_the_ID() . '_' . $product['name'] . '_interest', true );
					if( $product['number'] == 0 ){
						$product['number'] = ' XX ';
					};

					// Add snippet after every 10 posts
					if ($counter % 20 == 0) { ?>
					<article>
					<ins class="adsbygoogle"
						 style="display:block; text-align:center;"
						 data-ad-layout="in-article"
						 data-ad-format="auto"
						 data-ad-client="ca-pub-6520437489717144"
						 data-ad-slot="3600308398"></ins>
					<script>
						 (adsbygoogle = window.adsbygoogle || []).push({});
					</script>
					</article>
					<?php	
					}

					echo '<div class="product ' . ($haveState ? 'have ' : '') . ($needState ? 'need ' : '') . ($orderedState ? 'ordered ' : '') . ($interestState ? 'interest ' : '') . ($haveState || $needState || $orderedState || $interestState ? 'checked ' : '') . '">';

					// Display the radio buttons with the correct state
					echo '<div class="_checklist">';
					echo '<div class="_radio"><div><input type="radio" class="product-radio-have" name="' . esc_attr($product['name']) . '" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr($product['name']) . '" value="have"' . ($haveState ? ' checked' : '') . '> Have</div>';
					echo '<div><input type="radio" class="product-radio-need" name="' . esc_attr($product['name']) . '" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr($product['name']) . '" value="need"' . ($needState ? ' checked' : '') . '> Need</div>';
					echo '<div><input type="radio" class="product-radio-ordered" name="' . esc_attr($product['name']) . '" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr($product['name']) . '" value="ordered"' . ($orderedState ? ' checked' : '') . '> Ordered</div>';
					echo '<div><input type="radio" class="product-radio-interest" name="' . esc_attr($product['name']) . '" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr($product['name']) . '" value="interest"' . ($interestState ? ' checked' : '') . '> Not Interested</div></div>';
					
					// Get the 100x100 image URL
					$image_url = $product['image']; // Assuming this is the URL of the full size image
					$image_id = attachment_url_to_postid($image_url); // Get the image ID from the URL
					$image_info = wp_get_attachment_image_src($image_id, 'thumbnail'); // Use 'thumbnail' for now
					$thumbnail_url = $image_info[0];

					// Render each product.
					echo '<div class="product-row">';
					echo '<div class="_number">' . esc_html( $product['number'] ) . '</div>';
					echo '<b>Name:</b> ' . esc_html( $product['name'] ) . '<br>';
					echo '<b>Description:</b> ' . esc_html( $product['description'] ) . '</div>';
					echo '<div class="_image-pro"><a href="'.$product['image'].'" data-lightbox="'.$post->ID.'" data-title="'.$post->post_title.'"><img src="' . esc_url( $thumbnail_url ) . '" style="max-width:100px; max-height:100px; "></a>';
					if( $product['link'] ){
						echo '<a class="b-btn" href="' . esc_url( $product['link'] ) . '" target="_blank">Buy It Now</a>';
					}

					echo '</div></div></div>';

					$counter++; // Increment the counter
				}
				}
			}
		}
		wp_reset_postdata();
		?>
		</main>
	</div>


<?php
get_sidebar();
get_footer();
?>
<div id="share-modal">
  <div>
    <input type="text" id="share-link" readonly>
    <button id="copy-link-button">Copy Link</button>
  </div>
</div>
<script>
	
	jQuery(document).ready(function($) {
    $('#export-to-csv').click(function() {
        var postId = <?php echo get_the_ID(); ?>; // Make sure this is within a PHP file

        // Send an AJAX request to the server to generate the CSV
        $.post({
            url: checklist_vars.ajax_url,
            data: {
                action: 'export_to_csv',
                post_id: postId,
            },
            success: function(data) {
                // Create a Blob from the CSV data
                var blob = new Blob([data], {type: 'text/csv'});

                // Create a URL for the Blob
                var url = URL.createObjectURL(blob);

                // Create a new anchor element
                var a = document.createElement('a');

                // Set the href and the download attribute to start a download
                a.href = url;
                a.download = '<?php echo get_the_title(); ?>.csv'; // Make sure this is within a PHP file

                // Append the anchor to the body to make it clickable
                document.body.appendChild(a);

                // Start the download
                a.click();

                // Remove the anchor from the body
                document.body.removeChild(a);
            }
        });
    });
});
</script>
<script>
	
jQuery(document).ready(function($) {
    
    // When clicking the share button
    $('#share-checklist-button').on('click touchstart', function() {
        var postId = $(this).data('post-id');
        $.post(checklist_vars.ajax_url, {
            action: 'generate_shareable_link',
            post_id: postId
        }, function(response) {
            // Place the link in the input field and display the modal
            $('#share-link').val(response);
            $('#share-modal').show();
        });
    });

    // When clicking the copy button
    $('#copy-link-button').click(function() {
        // Select the text field
        var copyText = document.getElementById("share-link");

        // Select the text
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text
        document.execCommand("copy");

        // Provide feedback (could also be an alert or other UI feedback)
        alert("Copied the link to clipboard");

        // Hide the modal
        $('#share-modal').hide();
    });

    // Disable radio buttons for non-logged-in users
    if (!checklist_vars.user_is_logged_in) {
        $('.product-radio-have, .product-radio-need, .product-radio-ordered, .product-radio-interest').prop('disabled', true);
    }

    // Attach a change event listener to the radio buttons
    $('.product-radio-have, .product-radio-need, .product-radio-ordered, .product-radio-interest').change(function() {
        var postId = $(this).data('post-id');
        var productName = $(this).data('product-name');
        var stateName = $(this).val(); // We can now get the value directly from the radio button

        // Send an AJAX request to the server with the radio button state, post ID, product name, and state name
        $.post({
            url: checklist_vars.ajax_url,
            data: {
                action: 'update_checklist',
                post_id: postId,
                product_name: productName,
                state_name: stateName
            }
        });
    });

    // Filter products based on radio selection
    $('input[type=radio][name=filter]').change(function() {
        var value = $(this).val();
        if (value === 'all') {
            $('.product').show();
            $('article').show(); // Show articles when 'All' is selected
        } else if (value === 'unchecked') {
            $('.product').hide(); // Hide all products initially
            $('.product').each(function() {
                // If a product does not have any checked radio button, show it
                if (!$(this).find('input[type=radio]:checked').length) {
                    $(this).show();
                }
            });
            $('article').hide(); // Hide articles when 'Unchecked' is selected
        } else {
            $('.product').hide();
            $('.product.' + value).show();
            $('article').hide(); // Hide articles when other filters are selected
        }
    });
});
</script>
