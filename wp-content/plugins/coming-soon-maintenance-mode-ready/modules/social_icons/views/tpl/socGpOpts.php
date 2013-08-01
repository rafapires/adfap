<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_gp_enable_link]', array('checked' => $this->optsModel->get('soc_gp_enable_link')))?>
<label for="<?php echo 'opt_valuessoc_gp_enable_link_check'?>" class="button button-large"><?php langCsp::_e('Enable Link to Account')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol">
                <?php langCsp::_e('Account name')?>:
                <?php echo htmlCsp::text('opt_values[soc_gp_link_account]', array('value' => $this->optsModel->get('soc_gp_link_account')))?>
            </div>
		</td>
    </tr>
</table>
<br />
<br />

<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_gp_enable_badge]', array('checked' => $this->optsModel->get('soc_gp_enable_badge')))?>
<label for="<?php echo 'opt_valuessoc_gp_enable_badge_check'?>" class="button button-large"><?php langCsp::_e('Enable Badge (Follow icon)')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol withCspOptTip">
                <?php langCsp::_e('Color Scheme')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_gp_badge_color_scheme]', array(
                    'value' => $this->optsModel->get('soc_gp_badge_color_scheme'), 
                    'options' => array('light' => 'light', 'dark' => 'dark')))?>
            </div>
            <div class="cspRightCol withCspOptTip">
                <?php langCsp::_e('Google+ user')?>:
                <?php echo htmlCsp::text('opt_values[soc_gp_badge_account]', array('value' => $this->optsModel->get('soc_gp_badge_account')))?>
                <a href="#" class="cspOptTip" tip="<?php langCsp::_e('The URL of the Google+ page or account name.')?>"></a>
                <br />
                <?php langCsp::_e('Width')?>:
                <?php echo htmlCsp::text('opt_values[soc_gp_badge_width]', array('value' => $this->optsModel->get('soc_gp_badge_width')))?>
                <a href="#" class="cspOptTip" tip="<?php langCsp::_e('The pixel width of the badge to render. From 100 to 450.')?>"></a>
            </div>
		</td>
    </tr>
</table>  
<br />
<br />

<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_gp_enable_like]', array('checked' => $this->optsModel->get('soc_gp_enable_like')))?>
<label for="<?php echo 'opt_valuessoc_gp_enable_like_check'?>" class="button button-large"><?php langCsp::_e('Enable +1 Button')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol withCspOptTip">
                <?php langCsp::_e('Width')?>:
                <?php echo htmlCsp::text('opt_values[soc_gp_like_width]', array('value' => $this->optsModel->get('soc_gp_like_width')))?>
            </div>
            <div class="cspRightCol withCspOptTip">
                <?php langCsp::_e('Size')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_gp_like_size]', array(
                    'value' => $this->optsModel->get('soc_gp_like_size'), 
                    'options' => array('small' => 'Small (15px)', 'medium' => 'Medium (20px)', 'standard' => 'Standard (40px)', 'tall' => 'Tall (60px)')))?>
                <div class="clearfix"></div>
                <br />
                <br />
                <?php langCsp::_e('Annotation')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_gp_like_annotation]', array(
                    'value' => $this->optsModel->get('soc_gp_like_annotation'), 
                    'options' => array('inline' => 'inline', 'bubble' => 'bubble', 'none' => 'none')))?>
                <a href="#" class="cspOptTip" tip="<?php langCsp::_e('Sets the annotation to display next to the button.')?>"></a>
			</div>            
        </td>
    </tr>
</table>  