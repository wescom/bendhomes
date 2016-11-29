
<div class="simple-search-wrap">
    <form class="simple-search-form clearfix" action="<?php global $theme_search_url; echo $theme_search_url; ?>" method="get">
    
		<div class="option-bar large">
			<label for="keyword-txt"><?php _e('Keyword', 'framework'); ?></label>
			<input type="text" name="keyword" id="keyword-txt" value="<?php echo isset ( $_GET['keyword'] ) ? $_GET['keyword'] : ''; ?>" placeholder="<?php _e('Search Any Property', 'framework'); ?>" />
		</div>
		
		
	<div class="option-bar">
        <input type="submit" value="<?php _e('Search', 'framework'); ?>" class=" real-btn btn">
    </div>
    
   </form>
</div>