<?php
/* show brokerage office infomation, if any */
/*
$brokerage = array(
  'name' => get_post_meta($post->ID, 'brk_office_name',true),
  'address' => get_post_meta($post->ID, 'brk_office_address',true),
  'phone' => get_post_meta($post->ID, 'brk_office_phone',true)
);
*/

echo 'test3333';

$brokerage = array(
  'name' => get_post_meta($my_id, 'brk_office_name',true),
  'address' => get_post_meta($my_id, 'brk_office_address',true),
  'phone' => get_post_meta($my_id, 'brk_office_phone',true)
);

$brokerage['address'] = str_replace("\n",'<br/>', $brokerage['address']);

/* only show block if something is in $brokerage array */
if(array_filter($brokerage)) {
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
} else {
  echo '<!-- no brokerage information supplied -->';
}
?>
