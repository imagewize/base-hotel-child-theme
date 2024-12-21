<?php if( have_rows('sections') ): ?>
<?php while( have_rows('sections') ): the_row(); ?>

<?php if( get_row_layout() == 'hero' ): ?>
    <?php 
    $hero_image = get_sub_field('hero_image');
    $hero_title = get_sub_field('hero_title');
    $hero_tagline = get_sub_field('hero_tagline');
    $cta_title = get_sub_field('hero_cta_title');
    $cta_url = get_sub_field('hero_cta_url');
    
    if ($hero_image): ?>
        <div class="hero-section">
            <?php echo wp_get_attachment_image($hero_image['ID'], 'full', false, [
                'class' => 'hero-image',
                'loading' => 'eager', // Load hero image immediately
                'sizes' => '100vw',
                'srcset' => wp_get_attachment_image_srcset($hero_image['ID']),
            ]); ?>
            
            <?php if ($hero_title || $hero_tagline || ($cta_title && $cta_url)): ?>
            <div class="hero-content">
                <?php if ($hero_title): ?>
                    <h1 class="hero-title"><?php echo esc_html($hero_title); ?></h1>
                <?php endif; ?>
                <?php if ($hero_tagline): ?>
                    <p class="hero-tagline"><?php echo esc_html($hero_tagline); ?></p>
                <?php endif; ?>
                <?php if ($cta_title && $cta_url): ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="hero-cta">
                        <?php echo esc_html($cta_title); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php elseif( get_row_layout() == 'slideshow' ): ?>

<!-- Slideshow | START -->

<?php $images = get_sub_field('gallery'); if( $images ): ?>
<?php if (get_sub_field('style') == 'Default' || get_field('page_layout') == 'Sidebar') { ?>
<div class="centre">
    <div class="slideshow">
        <div class="slider">
            <?php foreach( $images as $image ): ?>
            <?php if (get_field('page_layout') == 'Sidebar') { ?><div class="item"><img alt="<?php echo esc_html ($image['alt']); ?>" 
                src="<?php echo esc_html ($image['sizes']['base_hotel_img_slideshow']); ?>" width="770" height="500" /></div><?php } else { ?><div class="item">
                    <img alt="<?php echo esc_html ($image['alt']); ?>" src="<?php echo esc_html ($image['sizes']['base_hotel_img_slideshow_large']); ?>" width="1200" height="600" />
                </div><?php } ?>
            <?php endforeach; ?>
        </div>
        <div class="nav">
            <a class="prev"><i class="fa fa-angle-left"></i></a>
            <a class="next"><i class="fa fa-angle-right"></i></a>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="galleryslider">
    <div class="slidecontainer">
        <div class="slider">
            <?php foreach( $images as $image ): ?>
            <img alt="<?php echo esc_html ($image['alt']); ?>" src="<?php echo esc_html ($image['sizes']['base_hotel_img_gallery']); ?>" />
            <?php endforeach; ?>
        </div>
        <div class="centre">
            <div class="nav">
                <a class="prev"><i class="fa fa-angle-left"></i></a>
                <a class="next"><i class="fa fa-angle-right"></i></a>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php endif; ?>

<!-- Slideshow | END -->



<?php elseif( get_row_layout() == 'text' ): ?>

<!-- Text | START -->

<div class="text centre">
	<?php the_sub_field('content'); ?>
<img class="size-full wp-image-4453 alignright" src="https://autentical.com/wp-content/uploads/sites/42/2020/11/2007_2020.jpg" alt="AUTENTICAL Get Away From Mass Tourism" width="209" height="81" /></div>

<!-- Text | END -->



<?php elseif( get_row_layout() == 'menu' ): ?>

<!-- Menu | START -->

<p align="center"><img src="https://autentical.com/wp-content/uploads/sites/42//2020/11/2007_2020.jpg" alt="Autentical Selected" width="356" height="65"
class="alignnone size-full wp-image-28643" /></p>

<br>
<br>
<?php if( have_rows('menu_group') ): ?>
<div class="centre">
<section class="menu">
    
    <?php while( have_rows('menu_group') ): the_row();
    $image = get_sub_field('menu_group_image'); ?>
    <div class="menu-group">
        <h4><?php the_sub_field('menu_group_heading'); ?> <span><?php the_sub_field('menu_group_sub_heading'); ?></span>
		<img alt="<?php the_sub_field('menu_group_heading'); ?>" src="<?php echo esc_html ($image['sizes']['thumbnail']); ?>" width="120" height="120" /></h4>
        <?php if( have_rows('menu_item') ): ?>
        <ul>
            <?php while( have_rows('menu_item') ): the_row(); ?>
            <li>
                <strong><?php the_sub_field('menu_item_heading'); ?></strong>
                <p><?php the_sub_field('menu_item_description'); ?></p>
                <div class="price"><div><?php the_sub_field('menu_item_price'); ?></div></div>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</section>
</div>
<?php endif; ?>

<!-- Menu | END -->



<?php elseif( get_row_layout() == 'accordion' ): ?>

<!-- Accordion | START -->

<?php if( have_rows('accordion_list') ): ?>
<div class="centre">
<ul class="accordion">
<?php while( have_rows('accordion_list') ): the_row(); ?>
<li>
    <h2 class="question"><?php the_sub_field('accordion_title'); ?></h2>
    <div class="answer"><?php the_sub_field('accordion_content'); ?></div>
</li>
<?php endwhile; ?>
</ul>
</div>
<?php endif; ?>

<!-- Accordion | END -->



<?php elseif( get_row_layout() == 'rooms' ): ?>

<!-- Rooms | START -->

<?php if(get_field('page_layout') == 'Full Width') { ?>
<div class="boxes centre">
	<?php if (get_sub_field('rooms_style') == 'Grid') { ?>
        <section class="rooms list grid">
        <?php $args=array(
        'post_type' => 'rooms',
        'orderby' => 'menu_order',
        'posts_per_page' => -1
        );
        $my_query = null;
        $my_query = new WP_Query($args);
        if( $my_query->have_posts() ) { ?>
        <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
        <div class="item">
            <div class="container">
                <div class="imgcontainer"><img alt="<?php the_title(); ?>" src="<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'base_hotel_img_room_thumb'); echo esc_html ($image[0]); ?>" width="380" height="380" /></div>
                <div class="details">
                    <a href="<?php the_permalink(); ?>">
                        <h3 class="title"><?php the_title(); ?><br />
                        <?php if(get_field('room_sub_heading')) { ?><span><?php the_field('room_sub_heading'); ?></span><?php } ?></h3>
                        <?php if(get_field('room_description')) { ?><p><?php the_field('room_description'); ?></p><?php } ?>
                        <div class="button"><span data-hover="<?php the_field('room_button_text'); ?>"><?php the_field('room_button_text'); ?></span></div>
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        <?php } wp_reset_postdata(); ?>
    </section>
    <?php } else { ?>
    <section class="rooms list">
		<?php $args=array(
        'post_type' => 'rooms',
        'orderby' => 'menu_order',
        'posts_per_page' => -1
        );
        $my_query = null;
        $my_query = new WP_Query($args);
        if( $my_query->have_posts() ) {
        while ($my_query->have_posts()) : $my_query->the_post(); ?>
        <div class="item">
            <div class="imgcontainer"><img alt="<?php the_title(); ?>" src="<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'base_hotel_img_special_banner'); echo esc_html ($image[0]); ?>" width="1200" height="400" /></div>
            <div class="details">
                <a href="<?php the_permalink(); ?>">
                    <h3 class="title"><?php the_title(); ?><br />
                    <?php if(get_field('room_sub_heading')) { ?><span><?php the_field('room_sub_heading'); ?></span><?php } ?></h3>
                    <?php if(get_field('room_description')) { ?><p><?php the_field('room_description'); ?></p><?php } ?>
                    <div class="button"><span data-hover="<?php the_field('room_button_text'); ?>"><?php the_field('room_button_text'); ?></span></div>
                </a>
            </div>
        </div>
        <?php endwhile;
        } wp_reset_postdata(); ?>
    </section>
	<?php } ?>
</div>
<?php } ?>

<!-- Rooms | END -->



<?php elseif( get_row_layout() == 'boxes' ): ?>

<!-- Boxes | START -->

<?php if(get_field('page_layout') == 'Full Width') { ?>
<div class="boxes centre">
    <?php if (get_sub_field('boxes_style') == 'Grid') { ?>
    <?php if( have_rows('boxes_list') ): ?>
	<section class="rooms list grid">
    	<?php while( have_rows('boxes_list') ): the_row(); $image = get_sub_field('box_image'); ?>
        <div class="item <?php if (!get_sub_field('box_link')) { ?>nolink<?php } ?>">
            <div class="container">
                <div class="imgcontainer"><img alt="<?php the_sub_field('box_heading'); ?>" src="<?php echo esc_html ($image['sizes']['base_hotel_img_room_thumb']); ?>" width="380" height="380" /></div>
                <div class="details">
                    <?php if (get_sub_field('box_link')) { ?><?php if (get_sub_field('box_link') == 'Internal') { ?><a href="<?php the_sub_field('box_link_internal'); ?>"><?php } else if (get_sub_field('box_link') == 'External') { ?><a href="<?php the_sub_field('box_link_external'); ?>" target="_self"><?php } ?><?php } ?>
                        <h3 class="title"><?php the_sub_field('box_heading'); ?>
                        <?php if (get_sub_field('box_sub_heading')) { ?><br /><span><?php the_sub_field('box_sub_heading'); ?></span></h3><?php } ?>
                        <?php if (get_sub_field('box_description')) { ?><p><?php the_sub_field('box_description'); ?></p><?php } ?>
                        <?php if (get_sub_field('box_link')) { ?><div class="button"><span data-hover="<?php the_sub_field('box_link_text'); ?>"><?php the_sub_field('box_link_text'); ?></span></div><?php } ?>
                    <?php if (get_sub_field('box_link')) { ?></a><?php } ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </section>
    <?php endif; ?>
    <?php } else { ?>
    <?php if( have_rows('boxes_list') ): ?>
    <section class="rooms list">
    	<?php while( have_rows('boxes_list') ): the_row(); $image = get_sub_field('box_image'); ?>
        <div class="item <?php if (!get_sub_field('box_link')) { ?>nolink<?php } ?>">
            <div class="imgcontainer"><img alt="<?php the_title(); ?>" src="<?php echo esc_html ($image['sizes']['base_hotel_img_special_banner']); ?>" width="1200" height="400" /></div>
            <div class="details">
                <?php if (get_sub_field('box_link')) { ?><?php if (get_sub_field('box_link') == 'Internal') { ?><a href="<?php the_sub_field('box_link_internal'); ?>"><?php } else if (get_sub_field('box_link') == 'External') { ?><a href="<?php the_sub_field('box_link_external'); ?>" target="_blank"><?php } ?><?php } ?>
                    <h3 class="title"><?php the_sub_field('box_heading'); ?>
                    <?php if (get_sub_field('box_sub_heading')) { ?><br /><span><?php the_sub_field('box_sub_heading'); ?></span></h3><?php } ?>
                    <?php if (get_sub_field('box_description')) { ?><p><?php the_sub_field('box_description'); ?></p><?php } ?>
                    <?php if (get_sub_field('box_link')) { ?><div class="button"><span data-hover="<?php the_sub_field('box_link_text'); ?>"><?php the_sub_field('box_link_text'); ?></span></div><?php } ?>
                <?php if (get_sub_field('box_link')) { ?></a><?php } ?>
            </div>
        </div>
        <?php endwhile; ?>
    </section>
    <?php endif; ?>
	<?php } ?>
    </div>
<?php } ?>

<!-- Boxes | END -->



<?php elseif( get_row_layout() == 'features' ): ?>

<!-- Features | START -->

<?php if(get_field('page_layout') == 'Full Width') { ?>
<?php $image = get_sub_field('features_custom_background'); ?>
<section class="features <?php if(get_sub_field('features_custom_background')) { ?>custom<?php } ?>" <?php if(get_sub_field('features_custom_background')) { ?>style="background-image:url(<?php echo esc_html ($image['sizes']['base_hotel_img_slideshow_home_large']); ?>)"<?php } ?>>
    <div class="centre">
        <?php if (get_sub_field('features_main_heading')) { ?><h2><?php the_sub_field('features_main_heading'); ?></h2><?php } ?>
        <?php if( have_rows('features_list') ): ?>
        <div class="featurelist">
            <?php while( have_rows('features_list') ): the_row(); $image = get_sub_field('feature_image'); ?>
            <div class="feature">
                <img alt="<?php the_sub_field('features_main_heading'); ?>" src="<?php echo esc_html ($image['sizes']['thumbnail']); ?>" width="120" height="120" class="thumb" />
                <div class="details">
                    <h3><?php the_sub_field('feature_heading'); ?></h3>
                    <p><?php the_sub_field('feature_sub_heading'); ?>
                    <?php if (get_sub_field('feature_link')) { ?><br /><br /><?php if (get_sub_field('feature_link') == 'Internal') { ?><a href="<?php the_sub_field('feature_link_internal'); ?>"><i class="fa fa-external-link"></i> <?php the_sub_field('feature_link_text'); ?></a><?php } else if (get_sub_field('feature_link') == 'External') { ?><a href="<?php the_sub_field('feature_link_external'); ?>" target="_blank"><i class="fa fa-external-link"></i> <?php the_sub_field('feature_link_text'); ?></a><?php } ?><?php } ?></p>
                </div>
                <div class="copy">
                    <?php the_sub_field('feature_description'); ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php } ?>

<!-- Features | END -->



<?php elseif( get_row_layout() == 'gallery' ): ?>

<!-- Gallery | START -->

<?php if(get_field('page_layout') == 'Full Width') { ?>
<div class="gallery">
	<?php if( have_rows('gallery_images') ): ?>
    <?php while( have_rows('gallery_images') ): the_row();
    $image = get_sub_field('gallery_image'); ?>
    <figure class="<?php the_sub_field('gallery_size'); ?>">
    <a href="<?php echo esc_html ($image['sizes']['base_hotel_img_gallery']); ?>">
        <span><?php if (get_sub_field('gallery_caption')) { ?><?php the_sub_field('gallery_caption'); ?><?php } ?></span>
        <img alt="" data-original="<?php echo esc_html ($image['sizes']['base_hotel_img_gallery']); ?>" src="<?php echo esc_url(get_template_directory_uri()) ?>/images/blank.png" />
    </a>
    </figure>
    <?php endwhile; ?>
    <?php endif; ?>
</div>
<?php } ?>

<!-- Gallery | END -->



<?php elseif( get_row_layout() == 'stats' ): ?>

<!-- Stats | START -->

<?php if(get_field('page_layout') == 'Full Width') { ?>
<div class="centre">
    <section id="stats">
        <h3><?php the_sub_field('stats_title'); ?></h3>
        <figure>
            <strong><?php the_sub_field('stat_1_number'); ?></strong><br />
            <span><?php the_sub_field('stat_1_title'); ?></span>
        </figure>
        <figure>
            <strong><?php the_sub_field('stat_2_number'); ?></strong><br />
            <span><?php the_sub_field('stat_2_title'); ?></span>
        </figure>
        <figure>
            <strong><?php the_sub_field('stat_3_number'); ?></strong><br />
            <span><?php the_sub_field('stat_3_title'); ?></span>
        </figure>
        <figure>
            <strong><?php the_sub_field('stat_4_number'); ?></strong><br />
            <span><?php the_sub_field('stat_4_title'); ?></span>
        </figure>
    </section>
</div>
<?php } ?>

<!-- Stats | END -->



<?php endif; ?>



<?php endwhile; ?>
<?php endif; ?>