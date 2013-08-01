<table width="100%">
    <tr class="cspBodyCells">
        <td>
            <div class="cspLeftCol">
                <?php langCsp::_e('Enter site description here')?>:
                <?php echo htmlCsp::text('opt_values[meta_description]', array('value' => $this->optsModel->get('meta_description')))?>
            </div>
        </td>
    </tr>
</table>