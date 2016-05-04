
<?php
$scripturl = get_template_directory() . '/bend-homes/js/mcproc.js';
print_r($scripturl);
?>
<script type='text/javascript' src="<?php echo $scripturl; ?>"></script>

<div class="funnelbox">
    <div class="nl-notice">
      <p>Sign up today to receive special alerts, listings, and knowledge articles about real estate in Central Oregon</p>
    </div>
    <div id="mc_embed_signup" class="clearfix bv-form" novalidate="novalidate">
        <form action="//burmasphere.us12.list-manage.com/subscribe/post-json?u=42c03db0ed60b21c7c76ebe02&amp;id=dfe8c70d46&amp;c=?" method="get" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" target="_blank" novalidate="">

            <input type="hidden" name="u" value="42c03db0ed60b21c7c76ebe02">
            <input type="hidden" name="amp;id" value="dfe8c70d46">
            <input type="hidden" name="amp;c" value="?">

            <div class="form-group has-feedback">
                <div class="right-inner-addon">
                    <!-- <i class="form-control-feedback" data-bv-icon-for="EMAIL" style="visibility: hidden;"></i> -->
                    <input type="email" value="" name="EMAIL" class="bm-form-control" id="mce-EMAIL" placeholder="Enter your email address" data-bv-field="EMAIL">
                </div>
            </div>
            <div class="mc-field-group input-group hide"> <strong>Newsletter type: </strong>
                <ul>
                    <li>
                        <input type="checkbox" value="1" checked="checked" name="group[13][1]" id="mce-group[13]-13-0">
                        <label for="mce-group[13]-13-0">Top Headlines</label>
                    </li>
                </ul>
            </div>
            <div class="mc-field-group input-group hide"> <strong>Email Format </strong>
                <ul>
                    <li>
                        <input type="radio" value="html" name="EMAILTYPE" id="mce-EMAILTYPE-0" checked="checked">
                        <label for="mce-EMAILTYPE-0">html</label>
                    </li>
                    <li>
                        <input type="radio" value="text" name="EMAILTYPE" id="mce-EMAILTYPE-1">
                        <label for="mce-EMAILTYPE-1">text</label>
                    </li>
                </ul>
            </div>
            <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
            <div style="position: absolute; left: -5000px;">
                <input type="text" name="b_42c03db0ed60b21c7c76ebe02_dfe8c70d46" tabindex="-1" value="">
                <input type="hidden" value="benotifiedform" name="SOURCE" class="" id="mce-SOURCE">
            </div>

            <button type="submit" class="btn btn-success bm-form-button" id="mc_sub_button" name="sub_button">Sign Up</button>

        </form>
        <input type="hidden" name="sub_button" value="">
    </div>
    <!-- end: email signup form -->
</div>
</div>
