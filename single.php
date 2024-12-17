<?php get_header(); ?>
<!-- Content | START -->
<main>
	<div class="centre" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div id="left">
			<?php if (has_post_thumbnail()) { ?>
			<!-- Slideshow | START -->
            <div class="centre">
                <div class="slideshow">
                    <div class="slider">
                        <div class="item">
                            <?php
                            // Get the post thumbnail ID
                            $thumb_id = get_post_thumbnail_id();
                            
                            // Define image sizes and their breakpoints for srcset
                            $image_sizes = array(
                                'base_hotel_img_slideshow_small'  => '385w',  // Mobile
                                'base_hotel_img_slideshow_medium' => '610w',  // Tablet
                                'base_hotel_img_slideshow'        => '770w',  // Desktop
                                'base_hotel_img_slideshow_large'  => '1200w'  // Large screens
                            );
                            
                            // Generate srcset attribute
                            $srcset = array();
                            foreach ($image_sizes as $size => $width) {
                                $img_data = wp_get_attachment_image_src($thumb_id, $size);
                                if ($img_data) {
                                    $srcset[] = $img_data[0] . ' ' . $width;
                                }
                            }
                            
                            // Output responsive image
                            echo wp_get_attachment_image($thumb_id, 'base_hotel_img_slideshow', false, array(
                                'srcset' => implode(', ', $srcset),
                                'sizes'  => '(max-width: 385px) 385px, (max-width: 610px) 610px, (max-width: 770px) 770px, 1200px'
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slideshow | END -->
            <?php } ?>
            <?php if(have_posts()): while(have_posts()): the_post(); the_content(); endwhile; endif; ?>
            <?php wp_link_pages(); ?>
            <?php if(get_the_tag_list()) { echo get_the_tag_list('<p class="posttags"><i class="fa fa-tags"></i> ',', ','</p>'); } ?>
            <?php if ( !post_password_required() ) { comments_template(); } ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</main>
<!-- Content | END -->
<?php get_footer(); ?>