<!doctype html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<meta name="format-detection" content="telephone=no">

    <?php
    if ( !function_exists( 'has_site_icon' ) || !has_site_icon() ) {
	    $favicon = get_option( 'theme_favicon' );
	    if ( ! empty( $favicon ) ) {
		    ?><link rel="shortcut icon" href="<?php echo $favicon; ?>" /><?php
	    }
    }

    if ( is_singular() && pings_open( get_queried_object() ) ) {
	    ?><link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"><?php
    }

    wp_head();

    $janDev = 0;
	if (isset($_GET['janDev'])) {
		echo '<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" />';
	}
    ?>
    

<script type="text/javascript">
<?php /* Unminified
var trackOutboundLink = function(url, type) {
   ga('send', 'event', type, 'click', url, {
	 'transport': 'beacon',
	 'hitCallback': function(){document.location = url;}
   });
}*/ ?>
var trackOutboundLink=function(n,t){ga("send","event",t,"click",n,{transport:"beacon",hitCallback:function(){document.location=n}})};
</script>
</head>
<body <?php body_class(); ?>>
  
        <?php
          // Conditional. Don't show top ad banner on home/front page
          /*if( !is_front_page() ) { ?>
            <div class="ad-wrapper">
              <div class="container" id="leaderBoardContainer">
                <?php do_action('dfp_ad_spot','leadheader'); ?>
              </div>
            </div>
            <?php
          }*/
        ?>

        <!-- Start Header -->

        <div class="header-wrapper">
        
        	<?php // Mobile Nagigation bar
			//get_template_part( 'bend-homes/template-parts/navigation', 'mobile' ); ?>

            <div class="container"><!-- Start Header Container -->

                <header id="header" class="clearfix">

                    <div id="header-top" class="clearfix">
                        <?php
                        /* WPML Language Switcher */
                        if(function_exists('icl_get_languages')){
                            $wpml_lang_switcher = get_option('theme_wpml_lang_switcher');
                            if($wpml_lang_switcher == 'true'){
                                do_action('icl_language_selector');
                            }
                        }


                        // Currency Switcher
                        // get_template_part( 'template-parts/header-currency-switcher' );


                        // header email
                        // $header_email = get_option('theme_header_email');
                        $header_email = NULL;
                        if ( ! empty( $header_email ) ) {
                            ?>
                            <h2 id="contact-email">
                                <?php
                                include( get_template_directory() . '/images/icon-mail.svg' );
                                _e( 'Email us at', 'framework' ); ?> :
                                <a href="mailto:<?php echo antispambot( $header_email ); ?>"><?php echo antispambot( $header_email ); ?></a>
                            </h2>
                            <?php
                        }
                        ?>

                        <!-- Social Navigation -->
                        <?php
                          // get_template_part('template-parts/social-nav') ;
                        ?>


						<div class="user-nav clearfix">

							<a id="menu-toggle" class="menu-control" href="#sidr"><i class="fa fa-bars"></i></a>
							<?php /*
							<a class="nav-advanced-search hidden-phone" href="http://bendhomes.idxbroker.com/idx/search/advanced">Advanced Search</a>
							<a class="nav-map-search hidden-phone" href="http://bendhomes.idxbroker.com/idx/map/mapsearch">Map Search</a>
							*/
							?>

							<a class="nav-login" href="http://bendhomes.idxbroker.com/idx/userlogin">Login / Register</a>

							<a class="nav-profile" href="http://bendhomes.idxbroker.com/idx/myaccount">Profile</a>

						</div>

                    </div>

                    <!-- Logo -->
                    <div id="logo">

                        <?php
                        $logo_path = get_option('theme_sitelogo');
						list($width, $height) = getimagesize($logo_path);
						
                        if(!empty($logo_path)){
                            ?>
                            <a title="<?php  bloginfo( 'name' ); ?>" href="<?php echo home_url(); ?>">
                                <img src="<?php echo $logo_path; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="<?php  bloginfo( 'name' ); ?>">
                            </a>
                            <h2 class="logo-heading only-for-print">
                                <a href="<?php echo home_url(); ?>"  title="<?php bloginfo( 'name' ); ?>">
                                    <?php  bloginfo( 'name' ); ?>
                                </a>
                            </h2>
                            <?php
                        }else{
                            ?>
                            <h2 class="logo-heading">
                                <a href="<?php echo home_url(); ?>"  title="<?php bloginfo( 'name' ); ?>">
                                    <?php  bloginfo( 'name' ); ?>
                                </a>
                            </h2>
                            <?php
                        }

                        $description = get_bloginfo ( 'description' );
                        if($description){
                            echo '<div class="tag-line"><span>';
                            echo $description;
                            echo '</span></div>';
                        }
                        ?>
                    </div>


                    <div class="menu-and-contact-wrap">


                      <!-- Social Navigation -->
                      <?php
                        //get_template_part('template-parts/social-nav') ;

                        // $header_phone = get_option('theme_header_phone');
                        $header_phone = NULL;
                        if( !empty($header_phone) ){
						                $desktop_version = '<span class="desktop-version">' . $header_phone . '</span>';
                            $mobile_version =  '<a class="mobile-version" href="tel://'.$header_phone.'" title="Make a Call">' .$header_phone. '</a>';
                            echo '<h2  class="contact-number "><i class="fa fa-phone"></i>'.  $desktop_version . $mobile_version .  '<span class="outer-strip"></span></h2>';
						            }
                        ?>

                        <!-- Start Main Menu-->
                        <div id="sidr">
                            <nav class="main-menu">
                                <?php
                                wp_nav_menu( array(
                                    'theme_location' => 'main-menu',
                                    'menu_class' => 'clearfix'
                                ));
                                ?>
                            </nav>
                        </div>
                        <!-- End Main Menu -->
                    </div>

                </header>
				
				<div class="header-search" style="border-top:1px solid #444;margin-top:10px;padding-bottom:9px;">
					<script type="text/javascript" id="idxwidgetsrc-41723" src="//bendhomes.idxbroker.com/idx/quicksearchjs.php?widgetid=41723"></script>
					<div style="text-align:right;padding:0 1%;font-size:12px;font-weight:400;letter-spacing:.04em;">
						<a href="http://bendhomes.idxbroker.com/idx/search/advanced" style="color:#C9DA2D">Advanced Search</a> &nbsp;| &nbsp;<a href="http://bendhomes.idxbroker.com/idx/map/mapsearch" style="color:#929A9B">Map Search</a>
					</div>
				</div>

            </div> <!-- End Header Container -->

        </div><!-- End Header -->
