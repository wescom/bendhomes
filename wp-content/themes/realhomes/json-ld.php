<?php // JSON-LD for Wordpress Home Articles and Author Pages written by Pete Wailes and Richard Baxter 

function get_post_data() { global $post; return $post; } // stuff for any page 

$payload["@context"] = "http://schema.org/"; // this has all the data of the post/page etc 

$post_data = get_post_data(); // stuff for any page, if it exists 

echo "<!--";
echo "some stuff";
echo " -->";

/*$category = get_the_category(); // stuff for specific pages 

if (is_single()) { // this gets the data for the user who wrote that particular item 

	$author_data = get_userdata($post_data->post_author); 
	$post_url = get_permalink(); 
	$post_thumb = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); 

	$payload["@type"] = "Article"; 
	$payload["url"] = $post_url; 
	$payload["author"] = array( "@type" => "Person", "name" => $author_data->display_name, ); 
	$payload["headline"] = $post_data->post_title; 
	$payload["datePublished"] = $post_data->post_date; 
	$payload["image"] = $post_thumb; 
	$payload["ArticleSection"] = $category[0]->cat_name; 
	$payload["identifier"] = $post
	$payload["Publisher"] = "Bendhomes"; 

} // we do all this separately so we keep the right things for organization together 

if (is_front_page()) { 
	$payload["@type"] = "WebPage"; 
	$payload["name"] = "Builtvisible"; 
	$payload["logo"] = "http://builtvisible.com/wp-content/uploads/2014/05/BUILTVISIBLE-Badge-Logo-512x602-medium.png"; 
	$payload["url"] = "http://builtvisible.com/"; 
	$payload["sameAs"] = array( "https://twitter.com/builtvisible", "https://www.facebook.com/builtvisible", "https://www.linkedin.com/company/builtvisible", "https://plus.google.com/+SEOgadget/" ); 
	$payload["contactPoint"] = array( array( "@type" => "ContactPoint", "telephone" => "+44 20 7148 0453", "email" => "hello@builtvisible.com", "contactType" => "sales" ) ); 
} 

if (is_author()) { // this gets the data for the user who wrote that particular item 
	$author_data = get_userdata($post_data->post_author); // some of you may not have all of these data points in your user profiles - delete as appropriate // fetch twitter from author meta and concatenate with full twitter URL 

	$payload["@type"] = "Person"; 
	$payload["name"] = $author_data->display_name; 
	$payload["email"] = $author_data->user_email; 

} 


*/



?>
