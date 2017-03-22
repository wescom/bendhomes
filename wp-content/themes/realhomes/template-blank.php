<?php
/*
*   Template Name: Blank Page
*/
?>

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
    wp_head();
    ?>
<script async defer type="text/javascript">
var trackOutboundLink=function(n,t){ga("send","event",t,"click",n,{transport:"beacon",hitCallback:function(){document.location=n}})};
</script>
</head>

<body <?php body_class(); ?> style="background: #fff;padding:2px;">

<?php the_content(); ?>
                                    
<?php wp_footer(); ?>

</body>
</html>