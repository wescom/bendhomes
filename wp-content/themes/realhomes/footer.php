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

                        <div class="span3">
                          <h4>For Sale</h4>
                          <ul>
                            <li><a href="#">Homes for sale</a></li>
                            <li><a href="#">Open houses</a></li>
                            <li><a href="#">Sell your home</a></li>
                            <li><a href="#">Commercial real estate</a></li>
                            <li><a href="#">Find an agent</a></li>
                          </ul>
                        </div>

                        <div class="span3">
                          <h4>Central Oregon Living</h4>
                          <ul>
                            <li><a href="#">Central Oregon Communities</a></li>
                            <li><a href="#">Schools</a></li>
                            <li><a href="#">Places of worship</a></li>
                            <li><a href="#">Utilities &amp; Services</a></li>
                            <li><a href="#">Central Oregon events</a></li>
                            <li><a href="#">Central Oregon jobs</a></li>
                          </ul>
                        </div>

                        <div class="clearfix visible-tablet"></div>

                        <div class="span3">
                            <h4>Real Estate Tips &amp; News</h4>
                            <ul>
                              <li><a href="#">Buying a home</a></li>
                              <li><a href="#">Selling a home</a></li>
                              <li><a href="#">Relocation &amp; Moving</a></li>
                              <li><a href="#">Financing Tips</a></li>
                              <li><a href="#">Latest real estate headlines</a></li>
                            </ul>
                        </div>

                        <div class="span3">
                          <h4>Follow Bend Homes</h3>
                            <div class="socialicons">
                      				<a target="_blank" href="https://www.facebook.com/BendHomescom-774527582683047/"><span class="fa fa-facebook chiclet-facebook"></span></a>
                      				<a target="_blank" href="https://twitter.com/BendHomes541"><span class="fa fa-twitter chiclet-twitter"></span></a>
                      				<!-- <a target="_blank" href="https://www.youtube.com/user/bulletinwebmaster"><span class="fa fa-youtube chiclet-youtube"></span></a> -->
                      				<a target="_blank" href="https://www.instagram.com/bendhomes/"><span class="fa fa-instagram chiclet-instagram"></span></a>
                              <a target="_blank" href="https://www.pinterest.com/bendhomescom/"><span class="fa fa-pinterest chiclet-pinterest"></span></a>
                            </div>
                        </div>
                </div>
                <div class="row">

                        <div class="span3">
                          <h4>Browse homes by city</h4>
                          <ul>
                            <li><a href="#">Bend</a></li>
                            <li><a href="#">Redmond</a></li>
                            <li><a href="#">Terrebonne</a></li>
                            <li><a href="#">Sisters</a></li>
                            <li><a href="#">Prineville</a></li>
                            <li><a href="#">Madras</a></li>
                            <li><a href="#">La Pine</a></li>
                            <li><a href="#">Sunriver</a></li>
                          </ul>
                        </div>

                        <div class="span3">
                          <h4>Community Profiles</h4>
                          <ul>
                            <li><a href="#">Bend</a></li>
                            <li><a href="#">Redmond</a></li>
                            <li><a href="#">Sisters</a></li>
                            <li><a href="#">Sunriver</a></li>
                            <li><a href="#">Prineville</a></li>
                            <li><a href="#">La Pine</a></li>
                            <li><a href="#">Madras</a></li>
                          </ul>
                        </div>

                        <div class="clearfix visible-tablet"></div>

                        <div class="span3">
                            <h4>Popular Home Searches</h4>
                            <ul>
                              <li><a href="#">3 bedroom, 2 bathroom</a></li>
                              <li><a href="#">Price under $300,000</a></li>
                              <li><a href="#">Single family home</a></li>
                              <li><a href="#">Homes over 3000 sq ft</a></li>
                              <li><a href="#">Open houses</a></li>
                            </ul>
                        </div>

                        <div class="span3 logocol">
                          <h3>Find your dream home in Central Oregon</h3>
                          <a title="Bend Homes &amp; Real Estate" href="http://local.bendhomes.com">
                            <img src="http://local.bendhomes.com/wp-content/uploads/2016/04/bh_logo.png" alt="Bend Homes &amp; Real Estate">
                          </a>
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
