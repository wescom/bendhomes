<?php

function brokerageBlock($my_id) {
  include( 'template-parts/brokerage-block.php' );
}

if ( ! function_exists( 'add_googleanalytics' ) ) {
  function add_googleanalytics() {
    include( 'template-parts/googleanalytics.php' );
  }
  add_action('wp_footer', 'add_googleanalytics');
}
?>
