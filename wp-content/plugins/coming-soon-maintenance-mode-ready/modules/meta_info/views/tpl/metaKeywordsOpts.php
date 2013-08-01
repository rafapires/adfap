<table width="100%">
    <tr class="cspBodyCells">
        <td>
            <div class="cspLeftCol">
                <?php langCsp::_e('Enter site keywords here')?>:
                <?php echo htmlCsp::text('opt_values[meta_keywords]', array('value' => $this->optsModel->get('meta_keywords')))?>
            </div>
        </td>
    </tr>
</table>