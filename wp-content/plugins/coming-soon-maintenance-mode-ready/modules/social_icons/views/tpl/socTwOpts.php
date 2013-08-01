<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_tw_enable_link]', array('checked' => $this->optsModel->get('soc_tw_enable_link')))?>
<label for="<?php echo 'opt_valuessoc_tw_enable_link_check'?>" class="button button-large"><?php langCsp::_e('Enable Link to Account')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol">
                <?php langCsp::_e('Account name')?>:
                <?php echo htmlCsp::text('opt_values[soc_tw_link_account]', array('value' => $this->optsModel->get('soc_tw_link_account')))?>
            </div>
		</td>
    </tr>
</table>
<br />
<br />

<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_tw_enable_tweet]', array('checked' => $this->optsModel->get('soc_tw_enable_tweet')))?>
<label for="<?php echo 'opt_valuessoc_tw_enable_tweet_check'?>" class="button button-large"><?php langCsp::_e('Enable Tweet')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol withCspOptTip">
                <?php langCsp::_e('Count box position')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_tw_tweet_count]', array(
                    'value' => $this->optsModel->get('soc_tw_tweet_count'), 
                    'options' => array('none' => langCsp::_('none'), 'horizontal' => langCsp::_('horizontal'), 'vertical' => langCsp::_('vertical'))))?>
			</div>
            <div class="cspRightCol withCspOptTip">
                <?php langCsp::_e('Button Size')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_tw_tweet_size]', array(
                    'value' => $this->optsModel->get('soc_tw_tweet_size'), 
                    'options' => array('medium' => langCsp::_('medium'), 'large' => langCsp::_('large'))))?>
            </div>
        </td>
    </tr>
</table>  
<br />
<br />

<?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_tw_enable_follow]', array('checked' => $this->optsModel->get('soc_tw_enable_follow')))?>
<label for="<?php echo 'opt_valuessoc_tw_enable_follow_check'?>" class="button button-large"><?php langCsp::_e('Enable Follow')?></label>
<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol withCspOptTip">
                <?php langCsp::_e('Account')?>:
                <?php echo htmlCsp::text('opt_values[soc_tw_follow_account]', array('value' => $this->optsModel->get('soc_tw_follow_account')))?>
                <br />
                <?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_tw_follow_count]', array('checked' => $this->optsModel->get('soc_tw_follow_count')))?>
			    <label for="<?php echo 'opt_valuessoc_tw_follow_count_check'?>" class="button button-large csp-sub-button"><?php langCsp::_e('Followers count display')?></label>
                <br />
                <br />
                <?php echo htmlCsp::checkboxHiddenVal('opt_values[soc_tw_follow_show_name]', array('checked' => $this->optsModel->get('soc_tw_follow_show_name')))?>
                <label for="<?php echo 'opt_valuessoc_tw_follow_show_name_check'?>" class="button button-large csp-sub-button"><?php langCsp::_e('Show Screen Name')?></label>
            </div>
            <div class="cspRightCol withCspOptTip">
                <?php langCsp::_e('Button Size')?>:
                <?php echo htmlCsp::selectbox('opt_values[soc_tw_follow_size]', array(
                    'value' => $this->optsModel->get('soc_tw_follow_size'), 
                    'options' => array('medium' => langCsp::_('medium'), 'large' => langCsp::_('large'))))?>
                <div class="clearfix"></div>
            </div>
        </td>
    </tr>
</table>  		