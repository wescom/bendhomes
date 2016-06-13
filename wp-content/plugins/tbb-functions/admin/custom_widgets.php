<?php

$remove_defaults_widgets = array(
    'dashboard_incoming_links' => array(
        'page'    => 'dashboard',
        'context' => 'normal'
    ),
    /*'dashboard_right_now' => array(
        'page'    => 'dashboard',
        'context' => 'normal'
    ),*/
    'dashboard_recent_drafts' => array(
        'page'    => 'dashboard',
        'context' => 'side'
    ),
    'dashboard_quick_press' => array(
        'page'    => 'dashboard',
        'context' => 'side'
    ),
    'dashboard_plugins' => array(
        'page'    => 'dashboard',
        'context' => 'normal'
    ),
    'dashboard_primary' => array(
        'page'    => 'dashboard',
        'context' => 'side'
    ),
    'dashboard_secondary' => array(
        'page'    => 'dashboard',
        'context' => 'side'
    ),
    /*'dashboard_recent_comments' => array(
        'page'    => 'dashboard',
        'context' => 'normal'
    ),*/
	'wpseo-dashboard-overview' => array(
		'page'	  => 'dashboard',
		'context' => 'side'
	)
);


// To enable this widget uncomment function add_dashboard_widgets() in dashboard_widget.php
$custom_dashboard_widgets = array(
    'tbb-dashboard-widget' => array(
        'title' => 'Western Communications Dashboard',
        'callback' => 'dashboardWidgetContent'
    )
);

// Widget content for Wescom Dashboard widget above.
function dashboardWidgetContent() {
    $user = wp_get_current_user();
    echo '
	<p>Hello <strong>' . $user->display_name . '</strong>, here is some helpful information for building landing pages.</p>
	<h3 style="padding-left: 0;">Helpful Articles</h3>
	<ul>
		<li><a href="http://business.tutsplus.com/tutorials/what-is-a-landing-page--cms-25872" target="_blank">What is a Landing Page?</a></li>
		<li><a href="http://webdesign.tutsplus.com/articles/tips-for-designing-niche-landing-pages--cms-22472" target="_blank">Tips for Designing Niche Landing Pages</a></li>
		<li><a href="http://do.thelandingpagecourse.com/" target="_blank">11 Part Landing Page Course on Unbounce</a></li>
		<li><a href="http://webdesign.tutsplus.com/articles/how-to-become-a-conversion-centered-designer--cms-19664" target="_blank">7 Principles of Conversion-Centered Design</a></li>
	</ul>
	<h3 style="padding-left: 0;">Awesome Examples of Landing Pages</h3>
	<ul style="float: left; width: 48%; margin-top: 4px;">
		<li><a href="http://worksofwisnu.com/theme-preview/urip/layout-v2/index-app.html" target="_blank">Product Landing Page by Urip</a></li>
		<li><a href="https://offers.impactbnd.com/the-beginners-guide-to-inbound-marketing" target="_blank">Impact Branding &amp; Design</a></li>
		<li><a href="http://webdam.com/how-to-select-a-DAM/" target="_blank">WebDam</a></li>
		<li><a href="http://debt.bills.com/save/" target="_blank">Bills.com</a></li>
		<li><a href="https://www.webprofits.com.au/backlinks/" target="_blank">WebProfits</a></li>
	</ul>
	<ul>
		<li><a href="http://www.hbloom.com/ResidentialSubscriptions/" target="_blank">H.Bloom</a></li>
		<li><a href="http://boundlesspromo.wpengine.com/" target="_blank">Boundless Promo</a></li>
		<li><a href="http://drkas.wpengine.com/" target="_blank">Dr. Kas</a></li>
		<li><a href="http://www.urgentsnowremoval.com/" target="_blank">RoofConnect Snow Removal</a></li>
		<li><a href="http://conversionlab.no/" target="_blank">Conversion Lab</a></li>
		<li><a href="" target="_blank"></a></li>
	</ul>
	';
}