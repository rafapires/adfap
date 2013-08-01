<?php
class twitter_widgetCsp extends moduleCsp {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('toeTwitterWidgetCsp');
    }
}
/**
 * Slider Widget Class
 */
class toeTwitterWidgetCsp extends toeWordpressWidgetCsp {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'toeTwitterWidgetCsp', 
            'description' => langCsp::_('Displays Last Tweets')
        );
        $control_ops = array(
            'id_base' => 'toeTwitterWidgetCsp'
        );
	parent::__construct( 'toeTwitterWidgetCsp', langCsp::_('Ready! Twitter'), $widgetOps );
    }
    public function widget($args, $instance) {
		$this->preWidget($args, $instance);
        frameCsp::_()->getModule('twitter_widget')->getView()->display($instance);
		$this->postWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frameCsp::_()->getModule('twitter_widget')->getView()->displayForm($instance, $this);
    }
}
?>