<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	jQuery('#toePagesRecreate').click(function(){
		jQuery(this).sendForm({
			msgElID: 'toePagesOptionsMsg',
			data: {page: 'pagesCsp', action: 'recreatePages', reqType: 'ajax'}
		});
		return false;
	});
});
// -->
</script>
<h1><?php langCsp::_e('Pages Options')?></h1>
<table>
	<tr>
		<td><?php echo htmlCsp::inputButton(array('value' => langCsp::_('Recreate Pages'), 'attrs' => 'id="toePagesRecreate"'))?></td>
		<td><a href="#" class="toeOptTip" tip="<?php langCsp::_e('If you accidently deleted one of plugin page - Login, or Checkout, or Shopping cart for example, just click on this button - and pages, that you deleted, will be created again. Do not use it without emergency.')?>"></a></td>
	</tr>
</table>
<div id="toePagesOptionsMsg"></div>