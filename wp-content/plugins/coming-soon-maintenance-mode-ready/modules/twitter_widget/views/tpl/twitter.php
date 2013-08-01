<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
    var twittUrl = 'https://api.twitter.com/1/statuses/user_timeline.json?';
    twittUrl += 'include_entities=true&';
    twittUrl += 'include_rts=true&';
    twittUrl += 'screen_name=<?php echo $this->instance['username']?>&';
    twittUrl += 'count=<?php echo $this->instance['count']?>&';
	twittUrl += 'include_entities=1&'
    twittUrl += 'callback=?';
    try {
        jQuery.getJSON(twittUrl, function(data){
            try {
                if(data && jQuery(data).size()) {
                    var tweetsCount = jQuery(data).size();
                    var box = jQuery('#<?php echo $this->uniqBoxId?> .toeTwittData:first');
                    jQuery(box).css('display', '');
                    for(var i = 0; i < tweetsCount; i++) {
						var tweet = data[i].text.replace(/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig, function(url) {
							return '<a href="'+url+'" target="_blank">'+url+'</a>';
						}).replace(/B@([_a-z0-9]+)/ig, function(reply) {
							return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
						});
                        jQuery('#<?php echo $this->uniqBoxId?>').append(
                            jQuery(box).clone().html(tweet)
                        );
                    }
                }
            } catch(e) {}
        });
    } catch(e) {}
});
// -->
</script>
<?php if(!empty($this->instance['title'])) { ?>
    <div class="toeWidgetTitle">
        <h2><?php echo $this->instance['title']?></h2>
    </div>
<?php }?>
<div id="<?php echo $this->uniqBoxId?>">
    <div class="toeTwittData" style="display: none;"></div>
</div>