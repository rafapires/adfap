	<section id="footer">
		<div class="row">
			<div class="col-sm-4">
				<h3>ATALHOS</h3>
				<img src="<?php bloginfo('template_url'); ?>/img/2-VIA-BOLETO.png" class="img-responsive center-block">
			</div>
			<div class="col-sm-4">
				<h3>BLOG</h3>
				<ul class="nav nav-pills nav-stacked">
				<?php
					$args = array(
						'numberposts'	=> 6,
						'offset'		=> 0,
						'orderby' 		=> 'post_date',
						'order' 		=> 'DESC',
						'post_type' => 'post',
						'post_status' => 'publish',
						'suppress_filters' => true );
				$recent_posts = wp_get_recent_posts ($args, ARRAY_A);

				foreach ($recent_posts as $post) {	?>
					<li>
						<a href="<?php echo get_permalink($post["ID"]); ?>">
						<h2><?php echo esc_attr($post["post_title"]); ?></h2>
					</a>
					</li>
				<?php }?>
				</ul>


			</div>
			<div class="col-sm-4">
				<h3>SIGA-NOS</h3>
				<div class="row">
					<div class="col-sm-3">
						<a href="<?php echo home_url(); ?>/midia-sociais">
						<img src="<?php bloginfo('template_url'); ?>/img/facebook.png" class="img-responsive center-block">
						</a>
					</div>
					<div class="col-sm-3">
						<a href="<?php echo home_url(); ?>/midia-sociais">
						<img src="<?php bloginfo('template_url'); ?>/img/googleplus.png" class="img-responsive center-block">
						</a>
					</div>
					<div class="col-sm-3">
						<a href="<?php echo home_url(); ?>/midia-sociais">
						<img src="<?php bloginfo('template_url'); ?>/img/linkedin.png" class="img-responsive center-block">
						</a>
					</div>
					<div class="col-sm-3">
						<a href="<?php echo home_url(); ?>/midia-sociais">
						<img src="<?php bloginfo('template_url'); ?>/img/youtube.png" class="img-responsive center-block">
						</a>
					</div>
				</div>
				<h3>ENDEREÇO</h3>
				<p>Praça João Mendes, 42 Cjs. 78 e 79 - CEP:01500-001<br />CENTRO - São Paulo - SP</p>
				<h3>CONTATOS</h3>
				<p>Tel: (11)3106-4817 | <a href="mailto:atendimento@adfap.com.br" target="_blank">atendimento@adfap.com.br</a></p>
			</div>
       	<p class="creditos pull-right">ADFAP © Company 2013 | desenvolvido por <a href="http://br.linkedin.com/in/rafaelpiressp/" target="_blank">Rafael Pires</a></p>
		</div>
	</section>

    </div><!-- /.container -->


<?php wp_footer(); ?>

</body>
</html>