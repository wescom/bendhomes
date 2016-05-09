<?php get_template_part("template-parts/carousel_partners"); ?>

<?php get_template_part("bend-homes/template-parts/footer-banner-ad"); ?>

<?php /* Set aside PHP for nowrap
<?php if ( ! dynamic_sidebar( 'footer-first-column' ) ) : ?>
<?php endif; ?>

<?php if ( ! dynamic_sidebar( 'footer-second-column' ) ) : ?>
<?php endif; ?>

<?php if ( ! dynamic_sidebar( 'footer-third-column' ) ) : ?>
<?php endif; ?>

<?php if ( ! dynamic_sidebar( 'footer-fourth-column' ) ) : ?>
<?php endif; ?>
*/ ?>

<!-- Start Footer -->
<footer id="footer-wrapper">

       <div id="footer" class="container bhfooter">

                <div class="row">

                        <div class="span4">
                          <h4>Connect with Bend Homes</h3>
                            <div class="socialicons">
                              <a target="_blank" href="https://www.facebook.com/BendHomescom-774527582683047/"><span class="fa fa-facebook chiclet-facebook"></span></a>
                              <a target="_blank" href="https://twitter.com/BendHomes541"><span class="fa fa-twitter chiclet-twitter"></span></a>
                              <!-- <a target="_blank" href="https://www.youtube.com/user/bulletinwebmaster"><span class="fa fa-youtube chiclet-youtube"></span></a> -->
                              <a target="_blank" href="https://www.instagram.com/bendhomes/"><span class="fa fa-instagram chiclet-instagram"></span></a>
                              <a target="_blank" href="https://www.pinterest.com/bendhomescom/"><span class="fa fa-pinterest chiclet-pinterest"></span></a>
                            </div>
                            <ul>
                              <li><a href="/about-bend-homes/">About Us</a></li>
                              <li><a href="/advertise-bend-homes/">Advertise With Us</a></li>
                              <li><a href="mailto:info@bendhomes.com">Contact Us</a></li>
                            </ul>
                        </div>

                        <div class="span4">
                            <h4>Popular Home Searches</h4>
                            <ul>
                              <li><a href="/property-search/?bedrooms=3&bathrooms=2">3 bedroom, 2 bathroom</a></li>
                              <li><a href="/property-search/?max-price=300000">Price under $300,000</a></li>
                              <li><a href="/property-search/?type=single-family-home">Single family home</a></li>
                              <li><a href="/property-search/?min-area=3000">Homes over 3000 sq ft</a></li>
                              <li><a href="/property-search/?status=open-house">Open houses</a></li>
                            </ul>
                        </div>

                        <div class="span4 logocol">
                          <a title="Bend Homes &amp; Real Estate" href="<?php site_url(); ?>">
                            <img src="/wp-content/uploads/2016/04/bh_logo.png" alt="Bend Homes &amp; Real Estate">
                          </a>
                          <!-- <h3>Find your dream home in Central Oregon</h3> -->
                          <?php get_template_part("bend-homes/template-parts/gform-signup"); ?>
                        </div>

                </div>

       </div>

        <!-- Footer Bottom -->
        <div id="footer-bottom" class="container">

                <div class="row">
                        <div class="span6">
                            <?php
                            $copyright_text = get_option('theme_copyright_text');
                            echo ( $copyright_text ) ? '<p class="copyright">'.$copyright_text.'</p>' : '';
                            ?>
                        </div>
                        <div class="span6">
                            <?php
                            $designed_by_text = get_option('theme_designed_by_text');
                            echo ( $designed_by_text ) ? '<p class="designed-by">'.$designed_by_text.'</p>' : '';
                            ?>
                        </div>
                </div>

        </div>
        <!-- End Footer Bottom -->

</footer><!-- End Footer -->

<?php
/**
 * include modal login if login & register page URL is not configured
 */
if ( ! is_user_logged_in() ) {
	$theme_login_url = get_option( 'theme_login_url' );
	if ( empty( $theme_login_url ) && ( ! is_page_template( 'template-login.php' ) ) ) {
		get_template_part( 'template-parts/modal-login' );
	}
}
?>

<a href="#top" id="scroll-top"><i class="fa fa-chevron-up"></i></a>

<?php wp_footer(); ?>
</body>
</html>
