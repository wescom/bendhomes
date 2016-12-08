<?php
global $theme_search_url;
$theme_search_url = get_option('theme_search_url');

global $theme_search_fields;
$theme_search_fields= get_option('theme_search_fields');

if( !empty($theme_search_url) && !empty($theme_search_fields) && is_array($theme_search_fields) ):
    ?>
    <section class="advance-search ">
        <?php
        get_template_part('bend-homes/search-form');
        ?>
    </section>
    <?php
endif;