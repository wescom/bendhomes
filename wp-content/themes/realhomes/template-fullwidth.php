<?php
/*
*  Template Name: Full Width Template
*/

get_header();
?>
    
<!--div class="simple-search-wrap">
	<div class="container">
		<div class="row">
			<?php
			//get_template_part('bend-homes/simple-search');
			?>
		</div>
	</div>
</div-->

<!-- Page Head -->
<?php //get_template_part("banners/default_page_banner"); ?>

<?php 
if( is_front_page() ) {

	$slides_array = [
		[
			'image' => get_field('imageslide1'),
			'link' => get_field('imagelink1'),
			'office' => get_field('imageoffice1'),
			'content' => get_field('imagecontent1')
		],
		[
			'image' => get_field('imageslide2'),
			'link' => get_field('imagelink2'),
			'office' => get_field('imageoffice2'),
			'content' => get_field('imagecontent2')
		],
		[
			'image' => get_field('imageslide3'),
			'link' => get_field('imagelink3'),
			'office' => get_field('imageoffice3'),
			'content' => get_field('imagecontent3')
		],
		[
			'image' => get_field('imageslide4'),
			'link' => get_field('imagelink4'),
			'office' => get_field('imageoffice4'),
			'content' => get_field('imagecontent4')
		],
		[
			'image' => get_field('imageslide5'),
			'link' => get_field('imagelink5'),
			'office' => get_field('imageoffice5'),
			'content' => get_field('imagecontent5')
		],
	];
		
	// If there's slides set for the homepage display the slider with slides and search bar.
	$janDev = 0;
	if (isset($_GET['janDev'])) {
		$janDev = 1;
	}

	if( !empty( $slides_array ) ) { ?>

		<?php if ($janDev == 1) { ?>
			
			<h3>Janelle Dev</h3>

			<div class="row">
				<div class="col-sm-6" width="50%">
					<div class="promo-block">
						<div id="home-flexslider" class="clearfix">
							<div class="flexslider loading">
								<ul class="slides">

									<?php
									foreach( $slides_array as $slide ) {
										if( !empty( $slide['image'] ) ) {

											$image = wp_get_attachment_image_src( $slide['image'], 'property_detail_slider_image_two' );

											$content = '';
											if( !empty( $slide['content'] ) ) {
												$content = '
												<div class="desc-wrap">
													<div class="slide-description">
														<h3><a href="'. $slide['link'] .'">'. $slide['content'] .'</a></h3>
														<div>'. $slide['office'] .'</div>
														<a href="'. $slide['link'] .'" class="know-more">View</a>
													</div>
												</div>
												';
											}

											// Output the slide
											echo sprintf('<li>%s<a href="%s"><img src="%s" alt="" width="%s" height="%s" /></a></li>',
														$content, $slide['link'], $image[0], $image[1], $image[2] );
										}	
									}
									?>

								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6" width="50%">
					Story list here
				</div>
			</div>

		<?php } else { ?>
		<div id="home-flexslider" class="clearfix">
			<div class="flexslider loading">
				<ul class="slides">

					<?php
					foreach( $slides_array as $slide ) {
						if( !empty( $slide['image'] ) ) {

							$image = wp_get_attachment_image_src( $slide['image'], 'property_detail_slider_image_two' );

							$content = '';
							if( !empty( $slide['content'] ) ) {
								$content = '
								<div class="desc-wrap">
									<div class="slide-description">
										<h3><a href="'. $slide['link'] .'">'. $slide['content'] .'</a></h3>
										<div>'. $slide['office'] .'</div>
										<a href="'. $slide['link'] .'" class="know-more">View</a>
									</div>
								</div>
								';
							}

							// Output the slide
							echo sprintf('<li>%s<a href="%s"><img src="%s" alt="" width="%s" height="%s" /></a></li>',
										$content, $slide['link'], $image[0], $image[1], $image[2] );
						}	
					}
					?>

				</ul>
			</div>
		</div>

		<?php } ?>

		<?php /*
		<div class="banner-search-wrap">
			<div class="container">
				<div class="clearfix">
					<h1 class="page-title">Welcome to BendHomes.com</h1>
					<div class="header-search"><?php echo do_shortcode('[idx-omnibar styles="1" extra="0" min_price="1" ]'); ?></div>
				</div>
			</div>
		</div>
		*/ ?>
	
	<?php 
	// If no slide images are set just display the basic header with search bar overlay.
	} else { ?>

		<div class="page-head">
			<div class="banner-search-wrap">
				<div class="container">
					<div class="clearfix">
						<h1 class="page-title">Welcome to BendHomes.com</h1>
						<div class="header-search"><?php echo do_shortcode('[idx-omnibar styles="1" extra="0" min_price="1" ]'); ?></div>
					</div>
				</div>
			</div>
		</div>

	<?php }
} 
?>

<!-- Content -->
<div class="container contents single">
	<div class="row">
		<div class="span12 main-wrap">
			<!-- Main Content -->
			<div class="main">

				<div class="inner-wrapper">
					<?php
					if ( have_posts() ) :
						while ( have_posts() ) :
							the_post();
							?>
							<article id="post-<?php the_ID(); ?>" <?php post_class("clearfix"); ?>>
									<?php

									the_content();

									// WordPress Link Pages
									wp_link_pages(array('before' => '<div class="pages-nav clearfix">', 'after' => '</div>', 'next_or_number' => 'next'));
									?>
							</article>
							<?php
						endwhile;
						//comments_template();
					endif;
					?>
				</div>

			</div><!-- End Main Content -->

		</div> <!-- End span12 -->

	</div><!-- End contents row -->

</div><!-- End Content -->

<?php get_footer(); ?>