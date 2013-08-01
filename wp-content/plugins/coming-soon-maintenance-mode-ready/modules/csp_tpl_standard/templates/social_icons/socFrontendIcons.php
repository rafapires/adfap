<?php if(!empty($this->iconsList)): ?>
<div class="cspSocIcons cspSocIconsLinks">
    <?php
		foreach($this->iconsList as $icon) {
			if(empty($icon['htmlContent'])) continue;
			if(is_array($icon['htmlContent'])) {
				if(isset($icon['htmlContent']['sdk'])) echo $icon['htmlContent']['sdk'];
				if(isset($icon['htmlContent']['link'])) echo $icon['htmlContent']['link'];
			}
		}
	?>
</div>
<?php endif; ?>

<?php if(!empty($this->iconsList)): ?>
<div class="cspSocIcons cspSocIconsOther">
	<?php
		foreach($this->iconsList as $icon) {
			if(empty($icon['htmlContent'])) continue;
			if(is_array($icon['htmlContent'])) {
				if(isset($icon['htmlContent']['share'])) echo $icon['htmlContent']['share'];
				if(isset($icon['htmlContent']['like'])) echo $icon['htmlContent']['like'];
				if(isset($icon['htmlContent']['follow'])) echo $icon['htmlContent']['follow'];
			}
		}
	?>
</div>
<?php endif; ?>