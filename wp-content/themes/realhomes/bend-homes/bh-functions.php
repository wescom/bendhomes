<?php

function brokerageBlock($my_id,$size) {
  $brokerage = array(
    'name' => get_post_meta($my_id, 'brk_office_name',true),
    'address' => get_post_meta($my_id, 'brk_office_address',true),
    'phone' => get_post_meta($my_id, 'brk_office_phone',true)
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
        echo '<img src="'.get_template_directory_uri().'/images/idx-'.$size.'.gif" alt="Broker Reciprocity">';
        echo '</div>'."\n";
      }elseif ($size == 'xsmall') {
        // echo '<br/>'.$brokerage['name'];
        echo '<div class="brokerage-label bl-'.$size.'">'."\n";
        echo '<p>';
        echo $brokerage['name'];
        echo '</p>';
        echo '<img src="'.get_template_directory_uri().'/images/idx-small.gif" alt="Broker Reciprocity2">';
        echo '</div>'."\n";
      }elseif ($size == 'large') {
        // echo '<br/>'.$brokerage['name'];
        echo '<div class="brokerage-label bl-'.$size.'">'."\n";
        echo '<p>';
        echo '<span>brokered by:</span><br/>'."\n";
        echo $brokerage['name'];
        echo '</p>';
        echo '<img src="'.get_template_directory_uri().'/images/idx-'.$size.'.gif" alt="Broker Reciprocity">';
        echo '</div>'."\n";
      }
    }
    /*
    echo '<div class="agent-brokerage-office">'."\n";
    echo '<p>';
    if(!empty($brokerage['name'])){
      echo '<strong>'.$brokerage['name'].'</strong><br/>';
    }
    if(!empty($brokerage['address'])){
      echo $brokerage['address'].'<br/>';
    }
    if(!empty($brokerage['phone'])){
      echo $brokerage['phone'];
    }
    echo '</p>';
    echo '</div>';
    */
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
      $pulldate = file_get_contents($fnamerecent);
    } else {
      $pulldate = strtotime('-30 days'); //'-6 hours' '-1 days'
    }
    $showdate = date('F j, Y g:ia', $pulldate - 60*60^7);
    //$showdate->sub('6H')

    $datetime_now = new DateTime("now");
    $datetime_smp = date_create($showdate);
    $diff = date_diff($datetime_now, $datetime_smp);

    if($diff->h < 1){
      // if date stamp of last update is less than one day, use 'ago' language
      //$showdate = '<span class="time-ago">'.time_ago($showdate).'</span>';
    }
    echo $showdate;
	}
}

?>
