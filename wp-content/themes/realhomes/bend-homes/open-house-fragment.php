    <?php
    function openHouseGetData($posid,$maxcount=3) {
      // we want this data for public view open houses
      $keys = array(
        'OPEN_HOUSE_AgentFirstName',
        'OPEN_HOUSE_AgentLastName',
        // 'OPEN_HOUSE_AgentHomePhone',
        'OPEN_HOUSE_StartDateTime',
        'OPEN_HOUSE_EndDateTime',
        'OPEN_HOUSE_TimeComments',
        // 'OPEN_HOUSE_ListingRid',
        // 'OPEN_HOUSE_MLNumber',
        // 'OPEN_HOUSE_post_id'
      );

      // the keys in the db are really like this:
      /*
      [0] => OPEN_HOUSE_0_AgentFirstName
      [1] => OPEN_HOUSE_0_AgentLastName
      [2] => OPEN_HOUSE_0_StartDateTime
      [3] => OPEN_HOUSE_0_EndDateTime
      [4] => OPEN_HOUSE_0_TimeComments
      [5] => OPEN_HOUSE_1_AgentFirstName
      [6] => OPEN_HOUSE_1_AgentLastName
      [7] => OPEN_HOUSE_1_StartDateTime
      [8] => OPEN_HOUSE_1_EndDateTime
      */
      $count = 0;
      $keyscounted = array();
      while($count <= $maxcount) {
        foreach($keys as $key) {
          $keyscounted[$count][] = str_replace('OPEN_HOUSE','OPEN_HOUSE_'.$count,$key);
        }
        $count++;
      }

      $data = array();
      $count = 0;
      foreach($keyscounted as $key => $entry) {
        // echo get_post_meta($posid, $keyuse, true);
        foreach($entry as $val) {

          if(strpos($val,'StartDateTime') == true) {
            $startdate = get_post_meta($posid, $val, true);
            if(!empty($startdate)) {
              $startdate = strtotime($startdate);
              $data[$count]['startdate'] = date('l, M jS',$startdate);
            }
          }
          if(strpos($val,'AgentFirstName') == true) {
            $agentfirst = get_post_meta($posid, $val, true);
            if(!empty($agentfirst)) {
              $data[$count]['agentfirst'] = ucwords($agentfirst);
            }
          }
          if(strpos($val,'AgentLastName') == true) {
            $agentlast = get_post_meta($posid, $val, true);
            if(!empty($agentlast)) {
              $data[$count]['agentlast'] = ucwords($agentlast);
            }
          }
          if(strpos($val,'TimeComments') == true) {
            $timecomments = get_post_meta($posid, $val, true);
            if(!empty($timecomments)) {
              $data[$count]['timecomments'] = str_replace('-',' - ',$timecomments);
            }
          }
        }
        $count++;
      }
      // print_r($data);
      return $data;
    }

    $OHdata = openHouseGetData($post->ID,10);
    if(!empty($OHdata)) {		
      echo '<div class="property-openhouse clearfix">';
		  echo '<span class="oh_header">Open house scheduled for:</span>';
		  foreach($OHdata as $entry) {
			echo '<span class="oh_timeblock">';
				echo '<span class="oh_time"> '.$entry['startdate'].', '.$entry['timecomments'].'</span>';
				echo '<span class="oh_agent"> by '.$entry['agentfirst'].' '.$entry['agentlast'].'</span>';
			echo '</span>';
		  }
      echo '</div>';
    }

    ?>
