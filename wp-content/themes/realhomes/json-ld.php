<?php // JSON-LD for Wordpress Home Articles and Author Pages written by Pete Wailes and Richard Baxter 

function get_post_data() { global $post; return $post; } // stuff for any page 

$payload["@context"] = "http://schema.org/"; // this has all the data of the post/page etc 

$post_data = get_post_data(); // stuff for any page, if it exists 

echo "<!--";
echo print_r($post_data);
echo " -->";

$category = get_the_category(); // stuff for specific pages 

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
	$payload["identifier"] = $post_data->ID;
	$payload["Publisher"] = "Bendhomes"; 

} // we do all this separately so we keep the right things for organization together 

if (is_front_page()) { 
	$payload["@type"] = "WebPage"; 
	$payload["name"] = "Bendhomes ".$post_data->post_title; 
	$payload["logo"] = "http://www.bendhomes.com/wp-content/uploads/2017/12/BendHomes.comLogoArt.png"; 
	$payload["url"] = "http://www.bendhomes.com".$_SERVER['REQUEST_URI']; 
	$payload["sameAs"] = array( "https://twitter.com/BendHomes541", "https://www.facebook.com/bendhomes541/", "https://plus.google.com/101058950766867205838" ); 
	$payload["publisher"] = array( array( "@type" => "ContactPoint", "telephone" => "541 382 1811", "email" => "info@bendhomes.com", "contactType" => "sales" ) ); 
	$payload["identifier"] = $post_data->post_title;
} 

if (is_author()) { // this gets the data for the user who wrote that particular item 
	$author_data = get_userdata($post_data->post_author); // some of you may not have all of these data points in your user profiles - delete as appropriate // fetch twitter from author meta and concatenate with full twitter URL 

	$payload["@type"] = "Person"; 
	$payload["name"] = $author_data->display_name; 
	$payload["email"] = $author_data->user_email; 

} 






?>
