<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package newco_theme
 */

get_header();
?>

<div class="g-columns__group _check">
    <div class="g-columns__item--nine">

        <main id="primary" class="g-layout__content" style="padding-top:20px;">
			<!-- Create a List Button -->
			<?php if ( is_user_logged_in() ): ?>
                <div class="create-list-button-container" style="margin-bottom: 20px;">
                <a href="<?php echo esc_url(home_url('/user-checklist/')); ?>" class="_button">Create a List</a>
            </div>
            <?php endif; ?>
            <div class="_tb-post-loop">
            <?php
            // Query for checklists created by admin
            $admin_args = array(
                'post_type'      => 'checklist',
                'author__in'     => 2, // Assuming '1' is the admin user ID
                'posts_per_page' => -1
            );
            $admin_query = new WP_Query($admin_args);

            if ($admin_query->have_posts()) :
                // Title for admin-generated lists
                
                while ($admin_query->have_posts()) : $admin_query->the_post();?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php
						$featuredimage = get_the_post_thumbnail_url($page->ID, 'medium');
						echo  '<a href="'.esc_url( get_permalink() ).'"><img style="width:600px;max-width:100%" src="'. $featuredimage .'" alt="'.$alt_text.'"></a>';
						the_title( '<h2 class="entry-title" style="margin-top:20px;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
						the_excerpt();
						?>
					<div class="_readmore"><a href="<?php echo esc_url( get_permalink() ) ?>">Checklist ></a></div>	
					</article>
				<?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
			</div>

            <?php
            // Query for shared user-generated checklists
            $user_args = array(
                'post_type'      => 'checklist',
                'author__not_in' => 2, // Excluding admin
                'meta_query'     => array(
                    array(
                        'key'     => '_is_shared',
                        'value'   => 'yes',
                        'compare' => '='
                    )
                ),
                'posts_per_page' => -1
            );
            $user_query = new WP_Query($user_args);

            if ($user_query->have_posts()) :
                // Title for user-generated shared lists
                echo '<div><h2>User Generated Lists</h2>';
                while ($user_query->have_posts()) : $user_query->the_post();
				$author_name = get_the_author();
					// Display the post title as a link
					the_title( '<p class="entry-title" style="margin-top:20px;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a><span style="font-size:12px;"> - Created by ' . esc_html($author_name) . '</span></p>' );
				endwhile;
				echo '</div>';
            endif;
            wp_reset_postdata();
            ?>

        </main><!-- #main -->
    </div><!-- .g-columns__item--nine -->

<?php
get_sidebar();
get_footer();
