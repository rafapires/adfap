<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_facebook_enable_link]', array('checked' => $this->optsModel->get('soc_facebook_enable_link')))?>
<label for="<?php echo 'opt_valuessoc_facebook_enable_link_check'?>" class="button button-large"><?php langCsp::_e('Enable Link to Account')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol">
                <?php langCsp::_e('Account name')?>:
                <?php echo htmlCsp::text('opt_values[soc_facebook_link_account]', array('value' => $this->optsModel->get('soc_facebook_link_account')))?>
            </div>
		</td>
    </tr>
</table>
<br />
<br />

<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_facebook_enable_share]', array('checked' => $this->optsModel->get('soc_facebook_enable_share')))?>
<label for="<?php echo 'opt_valuessoc_facebook_enable_share_check'?>" class="button button-large"><?php langCsp::_e('Enable Share')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol">
                <?php langCsp::_e('Layout Style')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_facebook_share_layout]', array(
                    'value' => $this->optsModel->get('soc_facebook_share_layout'), 
                    'options' => array('box_count' => langCsp::_('box count'), 'button_count' => langCsp::_('button count'), 'button' => langCsp::_('button'), 'icon' => langCsp::_('icon'))))?>
            </div>
		</td>
    </tr>
</table>  
<br />
<br />

<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_facebook_enable_like]', array('checked' => $this->optsModel->get('soc_facebook_enable_like')))?>
<label for="<?php echo 'opt_valuessoc_facebook_enable_like_check'?>" class="button button-large"><?php langCsp::_e('Enable Like')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
			<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_facebook_enable_send]', array('checked' => $this->optsModel->get('soc_facebook_enable_send')))?>
			<label for="<?php echo 'opt_valuessoc_facebook_enable_send_check'?>" class="button button-large csp-sub-button"><?php langCsp::_e('Enable Send Button')?></label>
			<br />
			<br />
            
            <div class="cspLeftCol withCspOptTip">
                <?php langCsp::_e('Layout Style')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_facebook_like_layout]', array(
                    'value' => $this->optsModel->get('soc_facebook_like_layout'), 
                    'options' => array('standard' => langCsp::_('standard'), 'button_count' => langCsp::_('button count'), 'box_count' => langCsp::_('box count'))))?>
			</div>
            <div class="cspRightCol withCspOptTip">
			<?php langCsp::_e('Width')?>:
			<?php echo htmlCsp::text('opt_values[soc_facebook_like_width]', array('value' => $this->optsModel->get('soc_facebook_like_width')))?>
			<a href="#" class="cspOptTip" tip="<?php langCsp::_e(array('The width of the plugin, in pixels. See the layout attributes', 
				'&lt;a target=&quot;_blank&quot; href=&quot;https://developers.facebook.com/docs/reference/plugins/like/&quot;&gt;https://developers.facebook.com/docs/reference/plugins/like/&lt;/a&gt;',
				'for specific widths and how they affect the functionality of the button.'))?>"></a>
			</div>
            <div class="clearfix"></div>
            
			<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_facebook_like_faces]', array('checked' => $this->optsModel->get('soc_facebook_like_faces')))?>
			<label for="<?php echo 'opt_valuessoc_facebook_like_faces_check'?>" class="button button-large csp-sub-button"><?php langCsp::_e('Show Faces')?></label>
			<br />
			<br />
            <div class="cspLeftCol withCspOptTip">
                <?php langCsp::_e('Font')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_facebook_like_font]', array(
                    'value' => $this->optsModel->get('soc_facebook_like_font'), 
                    'options' => array('arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana')))?>
                <br />
                <br />
                <?php langCsp::_e('Verb to display')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_facebook_like_verb]', array(
                    'value' => $this->optsModel->get('soc_facebook_like_verb'), 
                    'options' => array('like' => 'like', 'recommend' => 'recommend')))?>
            </div> 
            <div class="cspRightCol withCspOptTip">            
                <?php langCsp::_e('Color Scheme')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_facebook_like_color_scheme]', array(
                    'value' => $this->optsModel->get('soc_facebook_like_color_scheme'), 
                    'options' => array('light' => 'light', 'dark' => 'dark')))?>
			</div> 		
		</td>
    </tr>
</table>   
<br />
<br />

<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_facebook_enable_follow]', array('checked' => $this->optsModel->get('soc_facebook_enable_follow')))?>
<label for="<?php echo 'opt_valuessoc_facebook_enable_follow_check'?>" class="button button-large"><?php langCsp::_e('Enable Follow')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol withCspOptTip">
                <?php langCsp::_e('Layout Style')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_facebook_follow_layout]', array(
                    'value' => $this->optsModel->get('soc_facebook_follow_layout'), 
                    'options' => array('standard' => langCsp::_('standard'), 'button_count' => langCsp::_('button count'), 'box_count' => langCsp::_('box count'))))?>
			</div>
            <div class="cspRightCol withCspOptTip">
                <?php langCsp::_e('Profile')?>:
                <?php echo htmlCsp::text('opt_values[soc_facebook_follow_profile]', array('value' => $this->optsModel->get('soc_facebook_follow_profile')))?>
                <a href="#" class="cspOptTip" tip="<?php langCsp::_e('Profile name or URL of the user to follow.')?>"></a>
			</div>
            <div class="clearfix"></div>
            
			<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_facebook_follow_faces]', array('checked' => $this->optsModel->get('soc_facebook_follow_faces')))?>
			<label for="<?php echo 'opt_valuessoc_facebook_follow_faces_check'?>" class="button button-large csp-sub-button"><?php langCsp::_e('Show Faces')?></label>
			<br />
			<br />
            
            <div class="cspLeftCol withCspOptTip">
                <?php langCsp::_e('Color Scheme')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_facebook_follow_color_scheme]', array(
                    'value' => $this->optsModel->get('soc_facebook_follow_color_scheme'), 
                    'options' => array('light' => 'light', 'dark' => 'dark')))?>
                <br />
                <?php langCsp::_e('Width')?>:
                <?php echo htmlCsp::text('opt_values[soc_facebook_follow_width]', array('value' => $this->optsModel->get('soc_facebook_follow_width')))?>
            </div>
            <div class="cspRightCol withCspOptTip">
                <?php langCsp::_e('Font')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_facebook_follow_font]', array(
                    'value' => $this->optsModel->get('soc_facebook_follow_font'), 
                    'options' => array('arial' => 'arial', 'lucida grande' => 'lucida grande', 'segoe ui' => 'segoe ui', 'tahoma' => 'tahoma', 'trebuchet ms' => 'trebuchet ms', 'verdana' => 'verdana')))?>
			</div>			
		</td>
    </tr>
</table>  