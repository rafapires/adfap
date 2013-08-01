<?php
abstract class toeWordpressWidgetCsp extends WP_Widget {
	public function preWidget($args, $instance) {
		if(frameCsp::_()->isTplEditor())
			echo $args['before_widget'];
	}
	public function postWidget($args, $instance) {
		if(frameCsp::_()->isTplEditor())
			echo $args['after_widget'];
	}
}
