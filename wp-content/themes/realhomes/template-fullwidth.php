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
			
			<div class="row home-top-blob">
				<div class="col-md-6 no-right-padding">
					<div class="promo-block">
						<div id="new-flexslider" class="clearfix new-flexslider">
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
											echo sprintf('<li class="new-flex-li">%s<a href="%s"><img src="%s" alt="" height="%s" /></a></li>',
														$content, $slide['link'], $image[0], "100%");
										}	
									}
									?>

								</ul>
							</div><!-- flexslider -->
						</div><!-- new-flexslider -->
					</div><!-- promo-block -->
				</div><!-- col-sm-6 -->
				<div class="col-md-6 relative">
					<div class="story-loop-wrap top-stories-list">

					<?php
						// The Query
						$args = array( 'numberposts' => '5' );
						$recent_posts = wp_get_recent_posts($args);

						//var_dump($recent_posts);
						// The Loop
						foreach ( $recent_posts as $post ) {
							$catArray = get_the_category($post['ID']);
							foreach($catArray as $cat) {
								$catName = $cat->cat_name;
							} 

							$postContent = $post['post_content'];
							$contentArray = explode('src="', $postContent);
							if (count($contentArray) > 0) {
								$imgArray = explode('"', $contentArray[1]);
								$imgUrl = $imgArray[0];
								$imgUrl = str_replace(".jpg", "-1-244x163.jpg", $imgUrl);
							}
							
							//url: http://www.bendhomes.com/wp-content/uploads/2018/04/CMSID6194444_1.jpg
							//echo "url: ".$imgUrl;
                		?>
             
						<div class="story-item clearfix category-1829475">
						
						
							<a href="<?php echo $post['guid']; ?>">
								<img src="<?php echo $imgUrl; ?>" class="pull-right" alt="" width="140" height="93">
							</a>
								
							<?php 
								$catLink = str_replace("&", "and", $catname);
								$catLink = str_replace(" ", "-", $catLink);
							?>
							<div class="section"><small><a href="/category/<?php echo $catLink; ?>/" class="color-darkgray all-uppercase"><?php echo $catName; ?></a></small></div>	
							<h2>
								<a href="<?php $post['guid']; ?>"><?php echo $post['post_title'] ?></a>
							</h2>
							<div class="pub-date-wrap">			
								<?php echo "Published ".date("M j, Y", strtotime($post['post_date'])); ?>
							</div>
						</div><!-- story-item -->

							<?php }  ?>
					</div><!--story-loop-wrap-->
				</div><!-- col-sm-6 -->
			</div><!-- row home-top-blob -->

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