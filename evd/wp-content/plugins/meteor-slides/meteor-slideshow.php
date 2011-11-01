<?php
/**
 * Esse plugin foi modificado para o site http://euvotodistrital.org.br
 */
?>
<?php global $post;
	
	$options = get_option( 'meteorslides_options' );

	$i = 1;
	
	$loop = new WP_Query( array( 'post_type' => 'slide', 'posts_per_page' => $options['slideshow_quantity'] ) ); ?>
	
	<div id="meteor-slideshow" class="meteor-slides">
		
		<?php $meteornav = $options['slideshow_navigation'];
		
		if($meteornav == "yes"): ?>
			
		<ul id="meteor-buttons"></ul>
    <div id="meteor-tip"></div>
		
		<?php endif; ?>
		
		<div class="slides">
	
			<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
      
				<div id="slide-<?php echo $i; ?>" class="slide">
				
					<?php if ( has_post_thumbnail() ) :

            if(get_post_meta($post->ID, "slide_url_value", $single = true) != ""): ?>
						
              <a href="<?php echo get_post_meta($post->ID, "slide_url_value", $single = true); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('featured-slide'); ?></a>
			
            <?php else: ?>

              <?php the_post_thumbnail('featured-slide'); ?>

            <?php endif; ?>

						<!-- mostra texto do slide no topo -->
						<p class="slide-title"><?php echo get_post_meta($post->ID, "slide_text_value", $single = true); ?></p>

          <?php else : ?>

            <div class="slide-video" rel="<?php echo get_the_content(); ?>"></div>
            
					<?php endif; ?>
			
				</div><!-- .slide -->
			
				<?php $i++; ?>
			
			<?php endwhile; ?>
		
		</div><!-- .slides -->
		
		<?php wp_reset_query(); ?>
			
	</div><!-- #meteor-slideshow -->