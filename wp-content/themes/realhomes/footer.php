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
                          <h4>Connect with Bend Homes</h4>
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
                          <p>Listings updated:  <?php properties_updated_timestamp(); ?></p>
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

                        <div class="span4">
                        	<h4>Bend Homes Newsletter</h4>
                          <!--a title="Bend Homes &amp; Real Estate" href="<?php //site_url(); ?>">
                            <img src="/wp-content/uploads/2016/06/bendhomeslogo475x169.png" alt="Bend Homes &amp; Real Estate">
                          </a>
                          <!-- <h3>Find your dream home in Central Oregon</h3> -->
                          <p>Sign up today to receive special alerts, listings, and knowledge articles about real estate in Central Oregon.</p>
                          
						  <?php //get_template_part("bend-homes/template-parts/gform-signup"); ?>
                          
						  <?php get_template_part("bend-homes/template-parts/mailchimp_embed_form"); ?>                          
                        </div>

                </div>

       </div>

        <!-- Footer Bottom -->
        <div id="footer-bottom" class="container">
			<div class="row">
            	<div class="span12">
                	<p style="text-align: center;">The content relating to real estate for sale on this website is from the MSL of Central Oregon. ©MLSCO. All information provided is deemed reliable but is not guaranteed and should be independently verified. All content displayed on this website is restricted to personal, non-commercial use, and only for ascertaining information regarding real property for sale. The consumer will not copy, retransmit nor redistribute any of the content from this website. The consumer is reminded that all listing content provided by automatic transmission by MLSCO is © Multiple Listing Service of Central Oregon (MLSCO).</p>
                    <?php
                    $copyright_text = get_option('theme_copyright_text');
                    echo ( $copyright_text ) ? '<p class="copyright" style="text-align: center;">'.$copyright_text.'</p>' : '';

                    $designed_by_text = get_option('theme_designed_by_text');
                    echo ( $designed_by_text ) ? '<p class="designed-by" style="text-align: center;">'.$designed_by_text.'</p>' : '';
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

<!-- Modal -->
<div id="mortgage-calculator" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="mortgageCalculator" aria-hidden="true">
	<div class="modal-scrollable">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <div id="mortgageCalculator">
                <a href="https://www.evergreenhomeloans.com/bend/?ref=bh" target="_blank" onclick="trackOutboundLink('https://www.evergreenhomeloans.com/bend/?ref=bh'); return false;">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/evergreen-home-loans-logo.jpg" alt="Mortage Calculator Sponsored by Evergreen Home Loans" />
                </a>
                <div class="modal-address"><i class="fa fa-map-marker"></i> 685 SE 3rd St., Bend OR, 97702<br><a href="tel:5413185500"><i class="fa fa-mobile-phone"></i> (541) 318-5500</a></div>
            </div>
        </div>
        <div class="modal-body">
            <?php echo do_shortcode('[amortizationcalc format=”mobile-friendly”][/amortizationcalc]'); ?>
            <div style="text-align: center; padding: 15px 0 0; border-top: 1px solid #eee;">
            	<a href="https://www.evergreenhomeloans.com/bend/about-us/loan-officers/?ref=bh" onclick="trackOutboundLink('https://www.evergreenhomeloans.com/bend/about-us/loan-officers/?ref=bh'); return false;" class="btn btn-large real-btn" target="_blank" rel="nofollow"><i class="fa fa-home"></i> Find a Loan Consultant</a>
            </div>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
