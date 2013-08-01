<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	var csbBgImages = <?php echo utilsCsp::jsonEncode($this->images)?>;

	var cspBgSlides = [];
	for(var i in csbBgImages) {
		cspBgSlides.push({
			image: csbBgImages[ i ]
		});
	}
	if(csbBgImages.length) {
		jQuery.supersized({
			slides:	cspBgSlides
		,	slide_interval: <?php echo strval((int)$this->slider_slide_interval)?>
		,	transition: '<?php echo strval($this->slider_transition)?>'
		,	transition_speed: <?php echo strval((int)$this->slider_transition_speed)?>
		});
	}
});
// -->
</script>

