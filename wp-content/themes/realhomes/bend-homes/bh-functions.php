<?php 

/**
 * Display or retrieve the current post title with alternate title
 * if 'show_address_to_public' flag is set to 'no' as an alternate
 * field in the property post type
 *
 * pull is the global $post variable to get needed post data
 *
 * @param string $before Optional. Content to prepend to the title.
 * @param string $after  Optional. Content to append to the title.
 * @param bool   $echo   Optional, default to true.Whether to display or return.
 * @return string|void String if $echo parameter is false.
 */
function bh_the_title( $before = '', $after = '', $echo = true ) {
  global $post;
  $addressflag = get_post_meta($post->ID, 'show_address_to_public',true);
  $addressflag = strtolower($addressflag);
  // if the show_address_to_public field is empty or not equal to 'no', then show address
  if($addressflag == 'no') {
    // only make sentence if no address shown, regex got to first punctuation mark, and return that string
    $sentence = preg_replace('/([^?!.]*.).*/', '\\1', $post->post_content);
    $title = $sentence;
  } else {
    // if any value other than 'no', show standard title
    $title = get_the_title();
  }

	if ( strlen($title) == 0 )
		return;

	$title = $before . $title . $after;

	if ( $echo )
		echo $title;
	else
		return $title;
}


function brokerageBlock($my_id,$size) {
  $brokerage = array(
    'name' => get_post_meta($my_id, 'brk_office_name',true)
  );

  //error_log("size: ".$size, 0);
  $brokerage['address'] = str_replace("\n",'<br/>', $brokerage['address']);

  /* only show block if something is in $brokerage array */
  if(array_filter($brokerage)) {
    if(!empty($brokerage['name'])){

      if($size == 'small') {
        echo '<div class="brokerage-label bl-'.$size.'">'."\n";
        echo '<p>';
        echo $brokerage['name'];
        echo '</p>';
        echo '<img src="'.get_template_directory_uri().'/images/idx-'.$size.'.gif" width="45" height="35" alt="Broker Reciprocity">';
        echo '</div>'."\n";
      }elseif ($size == 'xsmall') {
        // echo '<br/>'.$brokerage['name'];
        echo '<div class="brokerage-label bl-'.$size.'">'."\n";
        echo '<p>';
        echo $brokerage['name'];
        echo '</p>';
        echo '<img src="'.get_template_directory_uri().'/images/idx-small.gif" width="" height="" alt="Broker Reciprocity">';
        echo '</div>'."\n";
      }elseif ($size == 'large') {
        // echo '<br/>'.$brokerage['name'];
        echo '<div class="brokerage-label bl-'.$size.'">'."\n";
        echo '<p>';
        echo '<span>brokered by:</span><br/>'."\n";
        echo $brokerage['name'];
        echo '</p>';
        echo '<img src="'.get_template_directory_uri().'/images/idx-'.$size.'.gif" width="60" height="47" alt="Broker Reciprocity">';
        echo '</div>'."\n";
      }
    }
  } else {
    echo '<!-- no brokerage information supplied -->';
  }
  unset($brokerage);
}


if ( ! function_exists( 'brokerage_label' ) ) {
  /**
	 * Output brokerage name on listing pages
	 *
	 * @param string $post_id string to pull in needed data
	 */
	function brokerage_label( $post_id, $size) {
		$property_agents = get_post_meta( $post_id, 'REAL_HOMES_agents' );
		// remove invalid ids
		$property_agents = array_filter( $property_agents, function($v){
		  return ( $v > 0 );
		});
		// remove duplicated ids
		$property_agents = array_unique( $property_agents );
		// print_r($property_agents);
		if(!empty($property_agents[0])) {
		  brokerageBlock($property_agents[0], $size);
		}
	}
}


if ( ! function_exists( 'time_ago' ) ) {
  function time_ago($timestamp){
      $datetime1 = new DateTime("now");
      // print_r($datetime1);
      // $datetime2 = date_create($timestamp);
      $datetime2 = date_create($timestamp);
      // print_r($datetime2);
      $diff = date_diff($datetime1, $datetime2);
      $timemsg = '';
      if($diff->y > 0){
          $timemsg = $diff->y .' year'. ($diff->y > 1?"s":'');

      }
      else if($diff->m > 0){
       $timemsg = $diff->m . ' month'. ($diff->m > 1?"s":'');
      }
      else if($diff->d > 0){
       $timemsg = $diff->d .' day'. ($diff->d > 1?"s":'');
      }
      else if($diff->h > 0){
       $timemsg = $diff->h .' hour'.($diff->h > 1 ? "s":'');
      }
      else if($diff->i > 0){
       $timemsg = $diff->i .' minute'. ($diff->i > 1?"s":'');
      }
      else if($diff->s > 0){
       $timemsg = $diff->s .' second'. ($diff->s > 1?"s":'');
      }

  $timemsg = $timemsg.' ago';
  return $timemsg;
  }
}


if ( ! function_exists( 'properties_updated_timestamp' ) ) {
  /**
	 * Output brokerage name on listing pages
	 *
	 * @param string $post_id string to pull in needed data
	 */
	function properties_updated_timestamp() {
		$resource = 'Property';
		$class = 'RESI';
		$rc = $resource.'_'.$class;
		$fnamerecent = ABSPATH.'/_retsapi/pulldates/'.$rc.'.txt';
	
		if(file_exists($fnamerecent)) {
		  $file_date = file_get_contents($fnamerecent);
		  //$dt = new DateTime("@$file_date");
		  //$timestamp = $dt->format('M j, Y g:ia');
		  $timestamp = date('M j, Y g:ia', $file_date/1000);
		  
		  //$pulldate = $pulldate - (60*60*7);  // 7 hours off so subtract
		} else {
		  $timestamp = strtotime('-30 days'); //'-6 hours' '-1 days'
		}
		
		//$show_date = date('F j, Y g:ia', $pull_date);
	
		echo $timestamp;
	}
}

?>
