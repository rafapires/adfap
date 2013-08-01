<script type="text/javascript">
// <!--
var cspSlides = <?php echo utilsCsp::jsonEncode($this->cspSlides);?>;
var cspSlidesNames = <?php echo utilsCsp::jsonEncode($this->optModel->get('slider_images'));?>;// Names only, without full path
// -->
</script>
<?php echo htmlCsp::checkbox('slider_enabled_checkbox', array('attrs' => 'id="cspSlideEnabled"', 'checked' => ($this->optModel->get('slider_enabled') == 1)))?>
<?php echo htmlCsp::hidden('opt_values[slider_enabled]', array('value' => $this->optModel->get('slider_enabled')))?>
<label for="cspSlideEnabled"><?php langCsp::_e('Enable')?></label><br />
<div id="cspAdminOptionsSlides" style="display: none;">
	<?php echo htmlCsp::ajaxfile('slide_img', array(
		'url' => uriCsp::_(array('baseUrl' => admin_url('admin-ajax.php'), 'page' => 'bg_slider', 'action' => 'saveSlide', 'reqType' => 'ajax')), 
		'buttonName' => 'Add New Image to Slider', 
		'responseType' => 'json',
		'onSubmit' => 'toeOptSlideImgOnSubmitNewFile',
		'onComplete' => 'toeOptSlideImgCompleteSubmitNewFile',
	))?>
	<div id="cspOptSlideImgMsg"></div>
	<br />
	<div id="cspAdminSlidesListShell">
		<div class="cspAdminSlideShell cspExample" style="display: none; float: left; padding: 10px;">
			<img src="" style="max-width: 100px; cursor: move;" /><br />
			<center><a href="#" onclick="cspRemoveSlide(this); return false;"><?php langCsp::_e('Delete')?></a></center><br />
		</div>
	</div>
	<div style="clear: both;"></div>
	<table>
		<tr>
			<td><?php langCsp::_e('Time between slide changes in milliseconds')?>:</td>
			<td><?php echo htmlCsp::text('opt_values[slider_slide_interval]', array('value' => $this->optModel->get('slider_slide_interval')))?></td>
		</tr>
		<tr>
			<td><?php langCsp::_e('Controls which effect is used to transition between slides')?>:</td>
			<td><?php echo htmlCsp::selectbox('opt_values[slider_transition]', array('value' => $this->optModel->get('slider_transition'), 'options' => array(
				'none' => 'No transition effect',
				'fade' => 'Fade effect (Default)',
				'slideTop' => 'Slide in from top',
				'slideRight' => 'Slide in from right',
				'slideBottom' => 'Slide in from bottom',
				'slideLeft' => 'Slide in from left',
				'carouselRight' => 'Carousel from right to left',
				'carouselLeft' => 'Carousel from left to right',
			)))?></td>
		</tr>
		<tr>
			<td><?php langCsp::_e('Time between slide changes in milliseconds')?>:</td>
			<td><?php echo htmlCsp::text('opt_values[slider_transition_speed]', array('value' => $this->optModel->get('slider_transition_speed')))?></td>
		</tr>
	</table>
</div>