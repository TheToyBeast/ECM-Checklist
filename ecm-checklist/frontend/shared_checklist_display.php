<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package newco_theme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
// Fetch only the current post
	$checklist_id = $_GET['checklist_id'];
	$args = array(
		'post_type' => 'checklist',
		'p' => $checklist_id, // This will fetch only the current post
	);
	$query = new WP_Query($args);

while ($query->have_posts()) {
		$query->the_post(); ?>
	<title>Share My Checklist - ToyBeast</title>
	<meta property="og:title" content="Share My Checklist" />
	<meta name="robots" content="follow, noindex"/>
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Share My Checklist - ToyBeast" />
	<meta property="og:site_name" content="ToyBeast" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="Share My Checklist - ToyBeast" />


	<?php } ?>
	<?php wp_head(); ?>
	<?php if (get_theme_mod( 'gtmcode', '' )){echo get_theme_mod( 'gtmcode', '' );} ?>
	<?php get_template_part( 'template-parts/components', 'header' ); ?>
</head>
<div style="margin-top:40px">
	<div>

	<main id="primary" class="g-layout__content _check _shared" style="padding:0; margin-bottom:40px;">
		
		<?php
		// Fetch the current post ID
		$current_post_id = get_the_ID();
		$current_post_title = get_the_title($current_post_id);
		
		$unique_key = $_GET['k'];

		// Query the user meta to find the user ID associated with this unique key
		// This code may vary depending on how exactly you've stored the unique key
		$user_query = new WP_User_Query(array(
			'meta_key' => 'shareable_link_key_' . $checklist_id,
			'meta_value' => $unique_key,
		));

		if (!empty($user_query->results)) {
			$user_id = $user_query->results[0]->ID;
			$user_info = get_userdata($user_id);
    		$username = $user_info->user_login;


			// Now you have the user ID and checklist ID and can proceed to display the shared checklist
		}

		the_post_thumbnail( 'large' );

		$counter = 1; // Counter variable to keep track of the number of posts

		while ($query->have_posts()) {
			$query->the_post();

			$product_list = get_post_meta( get_the_ID(), 'product_list', true );

			if ( $product_list && count( $product_list ) > 0 ) {
				echo '<h1>'.$username.'\'s ' . get_the_title() . '</h1>';
				echo '<p class="_no-print">This checklist was made available to you by <b>'.$username.'</b>. Your go-to destination for all things toys has just gotten better. Explore <a href="https://toybeast.ca">Toybest</a> to discover more checklists, stay up-to-date with the latest toy news, and join a community of toy enthusiasts like yourself! But that\'s not all! <b>Register today to be included in our next giveaway, and don\'t miss out on the chance to engage in our brand-new toy forum. See you there!</b></p>';
				 ?>
		<div id="filter">
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="all" checked>&nbsp;All</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="have">&nbsp;Have</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="need">&nbsp;Need</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="ordered">&nbsp;Ordered</label>
			<label style="white-space:nowrap;"><input type="radio" name="filter" value="unchecked">&nbsp;Unchecked/New</label>
		</div>

		<?php 

				foreach ( $product_list as $product ) {
					if( $product['number'] == 9999 ){
						echo '<hr><h2>'.esc_attr($product['name']).'</h2>';
					} else {
					// Fetch the states of each checkbox for this product
					$haveState = get_user_meta($user_id, 'checklist_' . get_the_ID() . '_' . $product['name'] . '_have', true);
					$needState = get_user_meta($user_id, 'checklist_' . get_the_ID() . '_' . $product['name'] . '_need', true);
					$orderedState = get_user_meta($user_id, 'checklist_' . get_the_ID() . '_' . $product['name'] . '_ordered', true);
					$interestState = get_user_meta($user_id, 'checklist_' . get_the_ID() . '_' . $product['name'] . '_interest', true);
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
					echo '<div class="_radio"><div><input type="radio" class="product-radio-have bgcol" name="' . esc_attr($product['name']) . '" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr($product['name']) . '" value="have"' . ($haveState ? ' checked' : '') . ' disabled> Have</div>';
					echo '<div><input type="radio" class="product-radio-need bgcol" name="' . esc_attr($product['name']) . '" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr($product['name']) . '" value="need"' . ($needState ? ' checked' : '') . ' disabled> Need</div>';
					echo '<div><input type="radio" class="product-radio-ordered bgcol" name="' . esc_attr($product['name']) . '" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr($product['name']) . '" value="ordered"' . ($orderedState ? ' checked' : '') . ' disabled> Ordered</div>';
					echo '<div><input type="radio" class="product-radio-interest bgcol" name="' . esc_attr($product['name']) . '" data-post-id="' . get_the_ID() . '" data-product-name="' . esc_attr($product['name']) . '" value="interest"' . ($interestState ? ' checked' : '' ) . ' disabled> Not Interested</div></div>';
					
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
					echo '<div class="_image-pro"><a href="'.$product['image'].'" data-lightbox="'.$current_post_id.'" data-title="'.$current_post_title.'"><img src="' . esc_url( $thumbnail_url ) . '" style="max-width:100px; max-height:100px; "></a>';
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
get_footer();
	
?>
