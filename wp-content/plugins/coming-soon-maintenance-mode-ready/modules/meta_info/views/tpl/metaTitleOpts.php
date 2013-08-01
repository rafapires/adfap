<table width="100%">
	<tr class="cspBodyCells">
		<td>
            <div class="cspLeftCol">
                <?php langCsp::_e('Enter site title here')?>:
                <?php echo htmlCsp::text('opt_values[meta_title]', array('value' => $this->optsModel->get('meta_title')))?>
            </div>
		</td>
    </tr>
</table>
